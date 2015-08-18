<?php

namespace app\models\repositories;

use yii\base\Model;

/*
    Класс, предназначенный для взаимодействия с таблицей БД 'specSubjects'
        Таблица содержит предметы, которые могут изучаться (для разных специальностей предметы могут повторяться)
        Поля таблицы - ['id', 'name']

    $tableName - имя таблицы в БД

    $dbAttributes - содержит строки, которые используются для выбора полей из БД с использованием as
		- используется для того. чтобы наименование полей таблицы БД в приложении отличалось от наименования полей таблицы в самой БД
			- к примеру может помочь в том случае, если затем необходимо будет поменять названия полей в БД и этого не придется делать во всем приложении,
				а только в данном классе

    $appFields - ключи - названия полей из таблицы БД, а значения - конкретные названия полей таблицы БД
        - ключи могут отличаться от наименования полей в БД за счет применения as и появления дополнительных полей при объединении таблиц
		- главным образом данное свойство следует использовать при внесении изменений в запись таблицы БД (используется для того, чтобы указать какие поля следует изменять)

*/

class SubjectsRepository extends BaseRepository
{
	
	protected static $tableName = 'subjects';

    protected static $appFields = [
		'id' => 'name', 
		'name' => 'name',
	];
	
	public function __construct() 
	{
		$this->dbAttributes = [
			'{{subjects}}.[[id]] as [[id]]',
			'{{subjects}}.[[name]] as [[name]]',
		];
		parent::__construct();
	}

    public function getOne($id) {
        return (new \yii\db\Query())->from('{{subjects}}')->where(['id' => $id])->one();
    }

    public function getAll() {
        return (new \yii\db\Query())->from('{{subjects}}')->all();
    }

}