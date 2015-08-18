<?php

namespace app\models\repositories;

use yii\base\Model;

/*
	На данный момент при выборе из таблицы БД выбираются все поля, в дальнейшем сделаю так, чтобы выбирались поля по желанию
*/

/*
    Класс, предназначенный для взаимодействия с таблицей БД 'variantSp'
        Таблица содержит конкретный профиль специальности
        Поля таблицы - ['id', 'name', 'description', 'countSubjects', 'idSp']

    $tableName - имя таблицы в БД
	
	$dbAttributes - содержит строки, которые используются для выбора полей из БД с использованием as
		- используется для того. чтобы наименование полей таблицы БД в приложении отличалось от наименования полей таблицы в самой БД
			- к примеру может помочь в том случае, если затем необходимо будет поменять названия полей в БД и этого не придется делать во всем приложении,
				а только в данном классе

    $appFields - ключи - названия полей из таблицы БД, а значения - конкретные названия полей таблицы БД
        - ключи могут отличаться от наименования полей в БД за счет применения as и появления дополнительных полей при объединении таблиц
		- главным образом данное свойство следует использовать при внесении изменений в запись таблицы БД (используется для того, чтобы указать какие поля следует изменять)
		
	При объединении таблиц используются следующие:
		speciality - таблица, в которой перечислены блоки специальностей (SpecialityRepository)
			а уже в таблице variantSp - перечислены специальности, кторые могут относиться к одному из блоков
			
			
*/

class VsRepository extends BaseRepository
{
	
	protected static $tableName = 'variantSp';

    protected static $appFields = [
		'id' => 'id', 
		'name' => 'name', 
		'description' => 'description', 
		'subjects' => 'countSubjects', 
		'idSpeciality' => 'idSp', 
		//поля из других таблиц
		'spname' => 'spname', //таблица speciality (поле name)
	];
	
	public function __construct() 
	{
		$this->dbAttributes = [
			'{{variantSp}}.[[id]] as [[id]]',
			'{{variantSp}}.[[name]] as [[name]]',
			'{{variantSp}}.[[description]] as [[description]]',
			'{{variantSp}}.[[countSubjects]] as [[subjects]]',
			'{{variantSp}}.[[idSp]] as [[idSpeciality]]',
		];
		parent::__construct();
	}

    public function getOne($id, $full = true) {
        return $this->prepareGet($full)->where(['{{variantSp}}.[[id]]' => $id])->one();
    }

    public function getAll($full = true) {
        return $this->prepareGet($full)->all();
    }
	
	private function prepareGet($full) 
	{
		$query = new \yii\db\Query();
        $fields = implode(',', $this->dbAttributes);
		if(!$full) {
			return $query->select($fields)->from('{{variantSp}}');
		}
		else {
			$spTableName = SpecialityRepository::getTableName();
            return $query
                ->select($fields.', {{'.$spTableName.'}}.[[name]] as spname')
                ->from('{{variantSp}}')->innerJoin('{{'.$spTableName.'}}', '{{variantSp}}.[[idSp]] = {{'.$spTableName.'}}.[[id]]');
		}
	}

}