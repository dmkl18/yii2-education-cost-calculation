<?php

namespace app\models\repositories;

use yii\base\Model;

/*
    Класс, предназначенный для взаимодействия с таблицей БД 'subjectsPassed'
        Таблица содержит прдметы, которые были выбраны для перезачета для конкретной заявки
        Поля таблицы - ['id', 'idApp', 'idSp']

    $tableName - имя таблицы в БД

    $dbAttributes - содержит строки, которые используются для выбора полей из БД с использованием as
		- используется для того. чтобы наименование полей таблицы БД в приложении отличалось от наименования полей таблицы в самой БД
			- к примеру может помочь в том случае, если затем необходимо будет поменять названия полей в БД и этого не придется делать во всем приложении,
				а только в данном классе

    $appFields - ключи - названия полей из таблицы БД, а значения - конкретные названия полей таблицы БД
        - ключи могут отличаться от наименования полей в БД за счет применения as и появления дополнительных полей при объединении таблиц
		- главным образом данное свойство следует использовать при внесении изменений в запись таблицы БД (используется для того, чтобы указать какие поля следует изменять)

	При объединении используются следующие таблицы:
		specSubjects - предметы конкретной специальности (SpecialitySubjectsRepository)
		subjects - список всех специальностей (SubjectsRepository)
		
*/

class AppPassedSubjectsRepository extends BaseRepository
{

    protected static $tableName = 'subjectsPassed';

    protected static $appFields = [
		'id' => 'id', 
		'idApplication' => 'idApp', 
		'idSpecialitySubject' => 'idSp',
	];
	
	public function __construct() 
	{
		$this->dbAttributes = [
			'{{subjectsPassed}}.[[id]] as [[id]]',
			'{{subjectsPassed}}.[[idApp]] as [[idApplication]]',
			'{{subjectsPassed}}.[[idSp]] as [[idSpecialitySubject]]',
		];
		parent::__construct();
	}
	
	public function getOne($id, $full = false) {
		return $this->prepareGet($full)->where(['id' => $id])->one(); 
	}
	
	public function getAll($full = false) {
		return $this->prepareGet($full)->all();
	}
	
	public function getForApplication($idApp, $full = true) {
		return $this->prepareGet($full)->where(['idApp' => $idApp])->all();
	}

    /*
     $data - массив массивов значений для таблицы
        $data[i][0] - idApp,
        $data[i][1] - idSp
    */
    public function addMany(array $data)
    {
        $columns = array_slice(self::$appFields, 1);
        return $this->db->createCommand()->batchInsert('subjectsPassed', $columns, $data)->execute();
    }
	
	private function prepareGet($full) 
	{
		$query = new \yii\db\Query();
        $fields = implode(',', $this->dbAttributes);
		if(!$full) {
			return $query->select($fields)->from('subjectsPassed');
		}
		else {
			$spSubTableName = SpecialitySubjectsRepository::getTableName();
			$subTableName = SubjectsRepository::getTableName();
			return $query->select($fields.', {{'.$subTableName.'}}.[[name]]')
				->from('subjectsPassed')
				->innerJoin($spSubTableName, '{{'.$spSubTableName.'}}.[[id]] = {{subjectsPassed}}.[[idSp]]')
				->innerJoin($subTableName, '{{'.$subTableName.'}}.[[id]] = {{'.$spSubTableName.'}}.[[idSub]]');
		}
	}

}