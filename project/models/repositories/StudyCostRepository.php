<?php

namespace app\models\repositories;

use yii\base\Model;

/*
    Класс, предназначенный для взаимодействия с таблицей БД 'studyCost'
        Таблица содержит возможные варианты прохождения обучения (заочное и т.д.)
        Поля таблицы - ['id', 'cost', 'idTech', 'idEv']
            
    $tableName - имя таблицы в БД

    $dbAttributes - содержит строки, которые используются для выбора полей из БД с использованием as
		- используется для того. чтобы наименование полей таблицы БД в приложении отличалось от наименования полей таблицы в самой БД
			- к примеру может помочь в том случае, если затем необходимо будет поменять названия полей в БД и этого не придется делать во всем приложении,
				а только в данном классе

    $appFields - ключи - названия полей из таблицы БД, а значения - конкретные названия полей таблицы БД
        - ключи могут отличаться от наименования полей в БД за счет применения as и появления дополнительных полей при объединении таблиц
		- главным образом данное свойство следует использовать при внесении изменений в запись таблицы БД (используется для того, чтобы указать какие поля следует изменять)
		
	Таблицы, используемые для объединения
		- technology - содержит перечень технологий обучения
		- educationVariant - сожержит перечень вариантов образования

*/

class StudyCostRepository extends BaseRepository
{
	
	protected static $tableName = 'studyCost';

    protected static $appFields = [
		'id' => 'id', 
		'cost' => 'cost', 
		'idTechnologyType' => 'idTech', 
		'idEducationVariant' => 'idEv',
		//связанные таблицы
		'technology' => 'technology',
		'educationVariant' => 'educationVariant',
	];
	
	public function __construct() 
	{
		$this->dbAttributes = [
			'{{studyCost}}.[[id]] as [[id]]',
			'{{studyCost}}.[[cost]] as [[cost]]',
			'{{studyCost}}.[[idTech]] as [[idTechnologyType]]',
			'{{studyCost}}.[[idEv]] as [[idEducationVariant]]',
		];
		parent::__construct();
	}

    public function getOne($id, $full=false) 
	{
		return $this->prepareGet($full)->where(['{{studyCost}}.[[id]]' => $id])->one();
	}

    public function getAll($full=false) {
        return $this->prepareGet($full)->all();
    }
	
	private function prepareGet($full) 
	{
		$query = new \yii\db\Query();
        $fields = implode(',', $this->dbAttributes);
		if(!$full) {
			return $query->select($fields)->from('studyCost');
		} 
		else {
			$techTableName = EducationTechnologyRepository::getTableName();
			$evTableName = EducationVariantRepository::getTableName();
			return $query->select($fields.', {{'.$techTableName.'}}.[[name]] as [[technology]], {{'.$evTableName.'}}.[[name]] as [[educationVariant]]')
			->from('studyCost')
			->leftJoin($techTableName, '{{'.$techTableName.'}}.[[id]]={{studyCost}}.[[idTech]]')
			->leftJoin($evTableName, '{{'.$evTableName.'}}.[[id]]={{studyCost}}.[[idEv]]');
		}
	}

}