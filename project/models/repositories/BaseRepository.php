<?php

namespace app\models\repositories;

use yii\base\Model;
use app\models\interfaces\IDbRepository;
use yii\base\NotSupportedException;
/*

    Класс является базовым классом для Репозиториев, которые работают с БД НЕ через ActiveRecord

    $tableName - имя таблицы в БД
    $appFields - название столбцов таблицы БД в приложении

*/

abstract class BaseRepository implements IDbRepository
{

    protected $db;
	
	protected $dbAttributes;

    public function __construct() {
        $this->db = \Yii::$app->db;
    }

    public static function getTableName() {
        return static::$tableName;
    }

    public static function getAttributes() {
        return static::$appFields;
    }

    public function getOne($id) {
        throw new NotSupportedException('getOne не поддерживается.');
    }

    public function getAll() {
        throw new NotSupportedException('getAll не поддерживается.');
    }

    public function addOne(Model $data) {
        throw new NotSupportedException('addOne не поддерживается.');
    }

    public function addMany(array $data) {
        throw new NotSupportedException('addMany не поддерживается.');
    }

    public function update($data) {
        throw new NotSupportedException('update не поддерживается.');
    }

    public function delete($id) {
        throw new NotSupportedException('delete не поддерживается.');
    }

}