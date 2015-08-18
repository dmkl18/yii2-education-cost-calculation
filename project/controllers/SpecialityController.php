<?php

namespace app\controllers;

use Yii;
use yii\base\Model;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use app\models\forms\CalculateCostForm;
use app\models\repositories\VsRepository;
use app\models\repositories\StudyOptionRepository;
use app\models\repositories\EducationVariantRepository;
use app\models\repositories\EducationTechnologyRepository;
use app\models\repositories\SpecialitySubjectsRepository;
use app\models\repositories\ApplicationRepository;
use app\models\repositories\AppPassedSubjectsRepository;
use app\models\repositories\StudyCostRepository;
use app\models\PageSplitting;

class SpecialityController extends Controller
{

	private $countApplicationsOnPage = 10;
	
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['calculate-cost', 'show-passed-subjects', 'view'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
					[
						'actions' => ['index'],
						'allow' => true,
                        'roles' => ['admin'],
					]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'show-passed-subjects' => ['post'],
                ],
            ],
        ];
    }
	
	public function actionIndex() 
	{
		$page = (int) Yii::$app->request->get('page', 1);
		if($page <= 0) {
			throw new \yii\web\NotFoundHttpException('Извините, но запрашиваемая Вами страница не найдена');
		}
		$appRepository = new ApplicationRepository();
		//В том случае, если page превышает допустимое значение, при использовании PageSplitting будет выброшено исключение throw new \yii\web\NotFoundHttpException
		$pages = new PageSplitting($appRepository, $this->countApplicationsOnPage, $page);
		return $this->render('index', [
			'materials' => $pages->getCurrentMaterial(),
			'pages' => $pages->getPagination(),
		]);
	}
	
	public function actionView() 
	{
		$app = (int) Yii::$app->request->get('num');
		if($app <= 0) {
			throw new NotFoundHttpException('Извините, но запрашиваемая Вами страница не найдена');
		}
		$appRepository = new ApplicationRepository();
		$appPSRepository = new AppPassedSubjectsRepository();
		$specSubRepository = new SpecialitySubjectsRepository();
		$application = $appRepository->getOne($app, true);
		if(!$application) {
			throw new NotFoundHttpException('Извините, но запрашиваемая Вами страница не найдена');
		}
        if(!\Yii::$app->user->can('fullViewUsers', ['identity' => $application['idUser']])) {
			throw new ForbiddenHttpException('Вам не разрешено просматривать данную заявку');
		}
		$specialitySubjects = $specSubRepository->getSpSubjects($application['idSpeciality']);
		$passedSubjects = $appPSRepository->getForApplication($app);
		return $this->render('view', [
			'application' => $application,
			'specialitySubjects' => $specialitySubjects,
			'passedSubjects' => $passedSubjects,
		]);
	}

    public function actionCalculateCost()
    {
		if(!\Yii::$app->user->can('admin')) { //все, кроме админа могут заполнять лишь одну заявку!!!
			$appRepository = new ApplicationRepository();
			if($appRepository->getCount(['idUser' => Yii::$app->getSession()->get('__id')])) {
				Yii::$app->getSession()->setFlash('warning', 'Вы уже заполняли заявку на поступление. Вы можете просматривать заявку в вашем профиле, переходя по соответствующей ссылке');
				return $this->redirect(Yii::$app->urlManager->createUrl(['user/profile', 'num' => Yii::$app->params['userSettings']['diffBetweenId'] + Yii::$app->session->get('__id')]));
			}
		}
		$model = new CalculateCostForm();
        $vs = new VsRepository();
        $so = new StudyOptionRepository();
        $ev = new EducationVariantRepository();
        $et = new EducationTechnologyRepository();
        if($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $app = new ApplicationRepository();
            if($model->soPassed) {
                $answer = $app->addOneWithPassedSubjects($model);
            } else {
                $answer = $app->addOne($model);
            }
            if($answer) {
                $appData = $app->getOne($answer, true);
                $sub = new SpecialitySubjectsRepository();
                $subjects = $sub->getSpSubjects($model->variantSpeciality, -1);
                $this->sendApplicationMail($appData, $subjects, $model);
                return $this->render('calculate-cost', [
                   'username' => $model->username,
                    'applicationSent' => true,
                ]);
            }
            else {
                Yii::$app->getSession()->setFlash('error', 'Извините, но при попытке отправить заявку возникла ошибка. Попробуйте отправить заявку позже.');
                return $this->refresh();
            }
        }
        $v = $vs->getAll();
        $s = $so->getAll();
        $e1 = $ev->getAll();
        $e2 = $et->getAll();
        if(!$v || !$s || !$e1 || !$e2) {
            throw new NotFoundHttpException();
        }
		$cost = (new StudyCostRepository())->getAll(true);
		return $this->render('calculate-cost', [
            'model' => $model,
            'speciality' => $v,
            'options' => $s,
            'variant' => $e1,
            'technology' => $e2,
            'user' => \Yii::$app->user->getIdentity(),
            'cost' => $cost,
			'applicationSent' => false,
		]);
    }

    public function actionShowPassedSubjects()
    {
        if(!Yii::$app->request->isAjax) {
            return json_encode("no access");
        }
        $sp = (int) Yii::$app->request->post('sp');
        if(!$sp || $sp < 1) {
            throw new NotFoundHttpException;
        }
        $rep = new SpecialitySubjectsRepository();
        $subjects = $rep->getSpSubjects($sp, 1);
        if(!$subjects) {
            return json_encode("no");
        } else {
            return json_encode($subjects);
        }
    }

    private function sendApplicationMail(array $appData, array $subjects, Model $model)
	{
        $passedSubjects = array();
        if($model->soPassed) {
            for($i=0, $lt = count($subjects); $i < $lt; $i++) {
                if(in_array($subjects[$i]['id'], $model->soPassed)) {
                    array_push($passedSubjects, $subjects[$i]);
                }
            }
        }
        $paidSubjectsCount = count($subjects) - count($passedSubjects);
        Yii::$app->mailer->compose('speciality/application', [
            'data' => $model,
            'application' => $appData,
            'subjects' => $subjects,
            'passedSubjects' => $passedSubjects,
            'paidSubjectsCount' => $paidSubjectsCount,
        ])->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($model->email)
            ->setSubject('Подтверждение принятия заявки')
            ->send();
    }

}