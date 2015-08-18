<?php

namespace app\models\repositories;

use yii\base\Model;
use app\models\interfaces\IDataPages;
use app\models\dbmodels\User;
/*
    Класс, предназначенный для взаимодействия с таблицей БД 'application'
        Таблица содержит заявку на постуаление (за исключением предметов для перезачета)
        Поля таблицы - ['id', 'idSp', 'idSo', 'idEv', 'idTech', 'idUsr', 'name', 'email', 'city', 'info', 'benefits', 'payment', 'course', 'term', 'cost', 'dateInsert']

    $tableName - имя таблицы в БД
	
	$dbAttributes - содержит строки, которые используются для выбора полей из БД с использованием as
		- используется для того. чтобы наименование полей таблицы БД в приложении отличалось от наименования полей таблицы в самой БД
			- к примеру может помочь в том случае, если затем необходимо будет поменять названия полей в БД и этого не придется делать во всем приложении,
				а только в данном классе

    $appFields - ключи - названия полей из таблицы БД, а значения - конкретные названия полей таблицы БД
        - ключи могут отличаться от наименования полей в БД за счет применения as и появления дополнительных полей при объединении таблиц
		- главным образом данное свойство следует использовать при внесении изменений в запись таблицы БД (используется для того, чтобы указать какие поля следует изменять)
		
	$baseSort - сортировка, используемая по умолчанию (используется при постраничной навигации)
	
	Таблицы, используемые для объединения
		- speciality - содержит перечень групп сециальностей (SpecialityRepository)
		- variantSp - сожержит конкретную специальность определенной группы (VsRepository)
		- studyOption - содержит варианты обучения (StudyOptionRepository)
		- educationVariant - содержит вариант получения образования (EducationVariantRepository)
		- technology - содержит технологию обучения (EducationTechnologyRepository)
		- users - содержит зарегистрированных пользователей

*/

class ApplicationRepository extends BaseRepository implements IDataPages
{

    protected static $tableName = 'application';

    protected static $appFields = [
		'id' => 'id', 
		'idSpeciality' => 'idSp', 
		'idStudyOption' => 'idSo', 
		'idEducationVariant' => 'idEv', 
		'idTechnologyType' => 'idTech', 
		'idUser' => 'idUsr', 
		'userName' => 'name', 
		'email' => 'email', 
		'city' => 'city', 
		'secondaryInformation' => 'info', 
		'benefits' => 'benefits', 
		'payment' => 'payment', 
		'countCourses' => 'course', 
		'educationTerm' => 'term', 
		'educationCost' => 'cost',
		'dateInsert' => 'dateInsert', 
	];
	
	private $baseOrder = '{{application}}.[[dateInsert]] DESC';
	
	public function __construct() 
	{
		$this->dbAttributes = [
			'{{application}}.[[id]] as [[id]]',
			'{{application}}.[[idSp]] as [[idSpeciality]]',
			'{{application}}.[[idSo]] as [[idStudyOption]]',
			'{{application}}.[[idEv]] as [[idEducationVariant]]',
			'{{application}}.[[idTech]] as [[idTechnologyType]]',
			'{{application}}.[[idUsr]] as [[idUser]]',
			'{{application}}.[[name]] as [[userName]]',
			'{{application}}.[[email]] as [[email]]',
			'{{application}}.[[city]] as [[city]]',
			'{{application}}.[[info]] as [[secondaryInformation]]',
			'{{application}}.[[benefits]] as [[benefits]]',
			'{{application}}.[[payment]] as [[payment]]',
			'{{application}}.[[course]] as [[countCourses]]',
			'{{application}}.[[term]] as [[educationTerm]]',
			'{{application}}.[[cost]] as [[educationCost]]',
			'{{application}}.[[dateInsert]] + '.date('Z').' as [[dateInsert]]',
		];
		parent::__construct();
	}
	
	/*
		В случае, если $full == true, мы выбираем данные читабельно (т.е. название специальности и прочее)
			Делаем за счет объединения таблиц (причем делаем несколько запросов в силу того, что рекомендуется делать объединение не более 3 таблиц)
			$dataPart1 - должна всегда выбираться в качестве первого запроса
	*/
	public function getOne($id, $full = false) 
	{
		$fields = implode(',', $this->dbAttributes);
		if(!$full) {
			return (new \yii\db\Query())->select($fields)->from('application')->where(['id' => $id])->one();
		}
		else {
			$spTableName = VsRepository::getTableName();
			$spGroupTableName = SpecialityRepository::getTableName();
			$soTableName = StudyOptionRepository::getTableName(); 
			$evTableName = EducationVariantRepository::getTableName();
			$etTableName = EducationTechnologyRepository::getTableName();
			$userTableName = User::tableName();
			$dataPart1 = (new \yii\db\Query())
				->select($fields.', {{'.$spTableName.'}}.[[name]] as [[speciality]], {{'.$spGroupTableName.'}}.[[name]] as [[specialityGroup]]')->from('application')
				->innerJoin('{{'.$spTableName.'}}', '{{application}}.[[idSp]] = {{'.$spTableName.'}}.[[id]]')
				->innerJoin('{{'.$spGroupTableName.'}}', '{{'.$spTableName.'}}.[[idSp]] = {{'.$spGroupTableName.'}}.[[id]]')
				->where(['{{application}}.[[id]]' => $id])
				->one();
            if(!$dataPart1) {
                return null;
            }
			$unionQuery1 = (new \yii\db\Query())->select('[[name]]')->from($evTableName)->where(['id' => $dataPart1['idEducationVariant']]);
			$unionQuery2 = (new \yii\db\Query())->select('[[name]]')->from($etTableName)->where(['id' => $dataPart1['idTechnologyType']]);
			$unionQuery3 = (new \yii\db\Query())->select('[[name]]')->from($soTableName)->where(['id' => $dataPart1['idStudyOption']]);
			$unionQuery1->union($unionQuery2);
			$unionQuery1->union($unionQuery3);
			$dataPart2 = $unionQuery1->all();
			$dataPart1['educationVariant'] = $dataPart2[0]['name'];
			$dataPart1['technologyType'] = $dataPart2[1]['name'];
			$dataPart1['studyOption'] = $dataPart2[2]['name'];
			$dataPart3 = (new \yii\db\Query())->select('[[login]]')->from($userTableName)->where(['idUsr' => $dataPart1['idUser']])->one();
			$dataPart1['userLogin'] = $dataPart3['login'];
			return $dataPart1;
		}
	}
	
	/*
		Если $full == true, то выбираем данные, отличающиеся от $full == false только тем,
			что присутствует Login пользователя, заполнившего анкету
	*/
	public function getAll($full = false) {
		return $this->prepareGetAll($full)->all();
	}
	
	/*
		данный метод используется при постраничной навигации
		возвращает объект Query
	*/
	public function getAllByPage() {
		return $this->prepareGetAll(true)->orderBy($this->baseOrder);
	}
	
	public function getAllWhere(array $where, $full = false) {
		$where = $this->prepareData($where);
        return $this->prepareGetAll($full)->where($where)->all();
	}
	
	public function getCount(array $where = null) 
	{
        $where = $this->prepareData($where);
        $query = (new \yii\db\Query())->from('application');
		if($where) {
			$query = $query->where($where);
		}
		return $query->count();
	}

    public function addOne(Model $data) 
	{
        $gmtTime = $this->getGmtTime();
        $data = $this->prepareAddApplication($data, $gmtTime);
        $transaction = $this->db->beginTransaction();
        try {
            if(!$this->db->createCommand()->insert('application', $data)->execute()) {
                throw new \Exception;
            }
            $last = (new \yii\db\Query())->select('MAX([[id]]) as lastEl')->from('{{application}}')->one();
            if(!$last) {
                throw new \Exception;
            }
            $transaction->commit();
        }
        catch(\Exception $e) {
            $transaction->rollBack();
            return false;
        }
        return $last['lastEl'];
    }

    public function addOneWithPassedSubjects(Model $data) 
	{
        $gmtTime = $this->getGmtTime();
        $dataApp = $this->prepareAddApplication($data, $gmtTime);
        $transaction = $this->db->beginTransaction();
        try {
            if(!$this->db->createCommand()->insert('application', $dataApp)->execute()) {
                throw new \Exception;
            }
            $last = (new \yii\db\Query())->select('MAX([[id]]) as lastEl')->from('{{application}}')->one();
            if(!$last) {
                throw new \Exception;
            }
            if(!$this->addPassedSubjects($data, $last['lastEl'])) {
                throw new \Exception;
            }
            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            return false;
        }
        return $last['lastEl'];
    }

    // далее вспомогательные private методы *************************************************************************
	
	private function prepareGetAll($full)
	{
		$query = new \yii\db\Query();
		$fields = implode(',', $this->dbAttributes);
		if(!$full) {
			return $query->select($fields)->from('application');
		}
		else {
			$userTableName = User::tableName();
			return $query
				->select($fields.', {{'.$userTableName.'}}.[[login]] as [[userLogin]]')->from('application')
				->innerJoin('{{'.$userTableName.'}}', '{{application}}.[[idUsr]] = {{'.$userTableName.'}}.[[idUsr]]');
		}
	}

    private function addPassedSubjects(Model $data, $appId) 
	{
        $aps = new AppPassedSubjectsRepository();
        $values = [];
        foreach($data->soPassed as $value) {
            $values[] = [$appId, $value];
        }
        return $aps->addMany($values);
    }

    private function prepareAddApplication(Model $data, $gmtTime) 
	{
        $data = [
            'idSp' => $data->variantSpeciality,
            'idSo' => $data->studyOption,
            'idEv' => $data->educationVariant,
            'idTech' => $data->technologyType,
            'idUsr' => $data->user,
            'name' => $data->username,
            'email' => $data->email,
            'city' => $data->city,
            'info' => $data->info,
            'benefits' => $data->benefits,
            'payment' => $data->payment,
            'course' => $data->course,
            'term' => $data->term,
            'cost' => $data->cost,
            'dateInsert' => $gmtTime
        ];
        return $data;
    }

    private function getGmtTime() 
	{
        $localTime = time();
        return $localTime - date("Z", $localTime);
    }

    private function prepareData(array $data) {
        $tableData = [];
        foreach($data as $key => $value) {
            $tableData[self::$appFields[$key]] = $value;
        }
        return $tableData;
    }

}