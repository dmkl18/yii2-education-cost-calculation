<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use app\models\repositories\UserRepository;
use app\models\repositories\ApplicationRepository;
use app\models\forms\LoginForm;
use app\models\forms\RegistryForm;
use app\models\forms\PasswordResetRequestForm;
use app\models\forms\ResetPasswordForm;

class UserController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'request-reset-password', 'reset-password'],
				'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
					[
						'actions' => ['request-reset-password', 'reset-password'],
						'allow' => true,
                        'roles' => ['?'],
					],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actionProfile()
    {
        $num = ((int) Yii::$app->request->get('num')) - Yii::$app->params['userSettings']['diffBetweenId'];
        if($num <= 0) {
            throw new NotFoundHttpException;
        }
        $mat = (new UserRepository())->getOne($num, true);
		if(!$mat) {
            throw new NotFoundHttpException;
        }
        $mat['registDate'] = date("d.m.Y в H:i", $mat['registDate']);
        $applications = (new ApplicationRepository())->getAllWhere(['idUser' => $num]);
		return $this->render('user-one', [
            'material' => $mat,
			'applications' => $applications ? $applications : null,
        ]);
    }

	/*
		если пользователь уже залогинился, то просто пересылаем его на главную страницу
	*/
    public function actionLogin()
    {
        if(!Yii::$app->user->isGuest) {
			Yii::$app->getSession()->setFlash('success', 'Вы уже вошли на сайт. Если Вы желаете войти под другим логином, Вам необходимо сначала выйти.');
            return $this->goHome();
        }
        $userRep = new UserRepository();
        $model = new LoginForm();
        if($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = $userRep->getOneByAttrs(['login' => $model->username]);
            if(!$user || !$userRep->validatePassword($user, $model->password)) {
                $model->addError('password', 'Вы ввели не правильные данные при авторизации, исправьте.');
            }
            else {
                $userRep->createCurrCheck($user);
                Yii::$app->user->login($user, $model->rememberMe ? 3600*24*30 : 0);
                Yii::$app->getSession()->setFlash('success', 'Приветствуем Вас '.$model->username.' на нашем сайте.');
                return $this->goHome();
            }
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
	
	/*
		Используется при регистрации
		Проверяет как форму целиком, так и поля login и email через Ajax
		В целях обеспечения правильной работы капчи при ajax-проверке полей используется отдельный сценарий формы - 'individual'
	*/
	public function actionSignIn()
	{
        if(!\Yii::$app->user->isGuest) {
            \Yii::$app->getSession()->setFlash('success', 'Вы уже вошли на сайт.');
			return $this->goHome();
        }
        $userRep = new UserRepository();
		$model = new RegistryForm();
        if(Yii::$app->request->isAjax) {
            $model->scenario = 'individual';
        }
        $load = $model->load(Yii::$app->request->post());
		if(Yii::$app->request->isAjax && $load) {
            /*echo json_encode("good");
            exit;*/
            Yii::$app->response->format = Response::FORMAT_JSON;
			return ActiveForm::validate($model);
		}
		if($load && $model->validate() && $userRep->addOne($model)) {
            $model->sendMail();
            Yii::$app->getSession()->setFlash('success', true);
		}
        return $this->render('regist', [
            'model' => $model,
		]);
	}

	/*
		выполняется при запросе на изменение пароля
	*/
    public function actionRequestResetPassword()
    {
        $userRep = new UserRepository();
        $model = new PasswordResetRequestForm();
        if($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = $userRep->getOneByAttrs(['email' => $model->email]);
            if(!$user) {
                $model->addError('email', 'Пользователя с таким адресом электронной почты не существует!');
            }
            else {
                if($userRep->validResetToken($user->resetToken)) {
                    Yii::$app->getSession()->setFlash('warning', 'Проверьте также Вашу электронную почту, возможно Вы уже сделали данный запрос!');
                }
                else {
                    $token = $userRep->createResetToken($user);
                    if(!$token || !$model->sendMail($token)) {
                        Yii::$app->getSession()->setFlash('warning', 'Извините, но в настоящий момент Вы не можете сменить пароль. Попробуйте сделать это позже.');
                    }
                    else {
                        Yii::$app->getSession()->setFlash('success', 'На Вашу электронную почту высланы инструкции по смене пароля.');
                        return $this->goHome();
                    }
                }
            }
        }
        return $this->render('request-reset-password', [
            'model' => $model,
        ]);
    }

	/*
		В том случае, если не указан $token или пользователя с таким $token-ом нет - выбрасываем InvalidParamException, который выбрасывает BadRequestHttpException
		Если же время $token вышло, то выбрасываем обычное исключение, которое сообщает от том, что
			время $token вышло и нужно вновь сделать запрос и перекидывает на страницу user/request-reset-password
	*/
    public function actionResetPassword($token)
    {
        try {

            if(empty($token) || !is_string($token)) {
                throw new InvalidParamException('Должен быть обязательно указан параметр смены пароля.');
            }
			$userRep = new UserRepository();
			if(!$userRep->validResetToken($token)) {
				throw new \Exception('Вы должны сделать новый запрос на смену пароля');
			}
            $user = $userRep->getOneByAttrs(['resetToken' => $token]);
			if(!$user) {
				throw new InvalidParamException('Пользователя с указанным значением параметра не существует.');
			}
            $model = new ResetPasswordForm();
            if($model->load(Yii::$app->request->post()) && $model->validate() && $userRep->update($user, [ 'password' => $model->password, 'resetToken' => null])) {
                Yii::$app->getSession()->setFlash('success', 'Пароль успешно изменен');
                return $this->goHome();
            }
            return $this->render('reset-password', [
                'model' => $model,
            ]);

        } catch(InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        } catch(\Exception $e) {
            Yii::$app->getSession()->setFlash('warning', $e->getMessage());
            return $this->redirect(Yii::$app->urlManager->createUrl('user/request-reset-password'));
        }

    }

}