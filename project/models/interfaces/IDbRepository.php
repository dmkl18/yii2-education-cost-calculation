<?php

namespace app\models\interfaces;

use yii\base\Model;

interface IDbRepository
{

    public static function getTableName();

    public static function getAttributes();

    public function getOne($id);

    public function getAll();

    public function addOne(Model $data);

    public function addMany(array $data);

    public function update($data);

    public function delete($id);

}