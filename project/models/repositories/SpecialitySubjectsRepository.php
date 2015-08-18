<?php

namespace app\models\repositories;

use yii\base\Model;

/*
    Класс, предназначенный для взаимодействия с таблицей БД 'specSubjects'
        Таблица содержит предметы, которые изучаются на конкретной специальности
        Поля таблицы - ['id', 'idSp', 'idSub', 'canPassed']
            idSp - id специальности, к которой относится данный предмет
            idSub - id предмета
            canPassed - доступен ли данный предмет для перезачета

    $tableName - имя таблицы в БД

    $dbAttributes - содержит строки, которые используются для выбора полей из БД с использованием as
		- используется для того. чтобы наименование полей таблицы БД в приложении отличалось от наименования полей таблицы в самой БД
			- к примеру может помочь в том случае, если затем необходимо будет поменять названия полей в БД и этого не придется делать во всем приложении,
				а только в данном классе

    $appFields - ключи - названия полей из таблицы БД, а значения - конкретные названия полей таблицы БД
        - ключи могут отличаться от наименования полей в БД за счет применения as и появления дополнительных полей при объединении таблиц
		- главным образом данное свойство следует использовать при внесении изменений в запись таблицы БД (используется для того, чтобы указать какие поля следует изменять)
		
	При объединении используются таблицы:
		- subjects - таблица, где содержатся все возможные предметы для изучения (SubjectsRepository)

*/

class SpecialitySubjectsRepository extends BaseRepository
{
	
	protected static $tableName = 'specSubjects';

    protected static $appFields = [
		'id' => 'id', 
		'idSpeciality' => 'idSp', 
		'idSubject' => 'idSub', 
		'passed' => 'canPassed', 
		//связанные таблицы
		'name' => 'name', //таблица subjects (поле name)
	];
	
	public function __construct() 
	{
		$this->dbAttributes = [
			'{{specSubjects}}.[[id]] as [[id]]',
			'{{specSubjects}}.[[idSp]] as [[idSpeciality]]',
			'{{specSubjects}}.[[idSub]] as [[idSubject]]',
			'{{specSubjects}}.[[canPassed]] as [[passed]]',
		];
		parent::__construct();
	}

    public function getOne($id, $full = false) {
        return $this->prepareGet($full)->where(['id' => $id])->one();
    }

    public function getAll($full = false) {
        return $this->prepareGet($full)->all();
    }

    /*
        $canPassed - используется для фильтрования предметов
            -1 - выберутся все предметы данной специальности
            0 - выберутся только те, которые недоступны для перезачета
            1 - -//- - доступны для перезачета
    */
    public function getSpSubjects($sp, $canPassed = -1, $full = true) 
	{
		$query = $this->prepareGet($full);
		$where = ['idSp' => $sp];
        if($canPassed == 0) {
            $where['canPassed'] = 0;
        } else if($canPassed == 1) {
            $where['canPassed'] = 1;
        }
        $query = $query->where($where);
        return $query->all();
    }
	
	private function prepareGet($full) 
	{
		$query = new \yii\db\Query();
        $fields = implode(',', $this->dbAttributes);
		if(!$full) {
			return $query->select($fields)->from('{{specSubjects}}');
		}
		else {
			$subTableName = SubjectsRepository::getTableName();
			return $query->select($fields.', {{'.$subTableName.'}}.[[name]] as [[name]]')
				->from('{{specSubjects}}')
				->innerJoin('{{'.$subTableName.'}}', '{{specSubjects}}.[[idSub]] = {{'.$subTableName.'}}.[[id]]');
		}
	}

}