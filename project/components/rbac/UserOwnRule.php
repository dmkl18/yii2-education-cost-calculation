<?php
namespace app\components\rbac;

use yii\rbac\Rule;

class UserOwnRule extends Rule
{
    public $name = 'isOwnUser';

    public function execute($user, $item, $params)
    {
        return isset($params['identity']) ? $params['identity'] == $user : false;
    }
}