<?php

namespace app\models\repositories;

use yii\base\Model;

/*
    Класс, предназначенный для взаимодействия с таблицей БД 'educationVariant'
        Таблица содержит возможные варианты прохождения обучения (заочное и т.д.)
        Поля таблицы - ['id', 'name', 'description', 'subjectsYear']
            'subjectsYear' - количество сдаваемых дисциплин в год при данном варианте обучения

    $tableName - имя таблицы в БД

    $dbAttributes - содержит строки, которые используются для выбора полей из БД с использованием as
		- используется для того. чтобы наименование полей таблицы БД в приложении отличалось от наименования полей таблицы в самой БД
			- к примеру может помочь в том случае, если затем необходимо будет поменять названия полей в БД и этого не придется делать во всем приложении,
				а только в данном классе

    $appFields - ключи - названия полей из таблицы БД, а значения - конкретные названия полей таблицы БД
        - ключи могут отличаться от наименования полей в БД за счет применения as и появления дополнительных полей при объединении таблиц
		- главным образом данное свойство следует использовать при внесении изменений в запись таблицы БД (используется для того, чтобы указать какие поля следует изменять)

*/

class EducationVariantRepository extends BaseRepository
{
	
	protected static $tableName = 'educationVariant';

    protected static $appFields = [
		'id' => 'id', 
		'name' => 'name', 
		'description' => 'description', 
		'subjectsYear' => 'subjectsYear',
	];
	
	public function __construct() 
	{
		$this->dbAttributes = [
			'{{educationVariant}}.[[id]] as [[id]]',
			'{{educationVariant}}.[[name]] as [[name]]',
			'{{educationVariant}}.[[description]] as [[description]]',
			'{{educationVariant}}.[[subjectsYear]] as [[subjectsYear]]',
		];
		parent::__construct();
	}

    public function getOne($id) {
        return (new \yii\db\Query())->select(implode(',', $this->dbAttributes))->from('educationVariant')->where(['id' => $id])->one();
    }

    public function getAll() {
        return (new \yii\db\Query())->select(implode(',', $this->dbAttributes))->from('educationVariant')->all();
    }

}