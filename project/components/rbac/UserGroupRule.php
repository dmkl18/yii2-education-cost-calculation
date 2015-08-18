<?php
namespace app\components\rbac;

use Yii;
use yii\rbac\Rule;
use app\models\dbmodels\User;

class UserGroupRule extends Rule
{
    public $name = 'userGroup';

    public function execute($user, $item, $params)
    {
        $user = User::findOne($user);
        if($user){
            $group = $user->role;
            if ($item->name === 'admin') {
                return $group == User::ROLE_ADMIN;
            }
            elseif ($item->name === 'user') {
                return $group == User::ROLE_ADMIN || $group == User::ROLE_DEFAULT_USER;
            }
        }
        return false;
    }
}

/*
    Суть:
        Правило применяется для проверки соответствует ли текущий пользователь роли, для которой в данный момент проверяется правило
        Имя данной роли находится в $item->name
        Мы проверяем id текущего пользователя (оно содержится в аргументе $user)

        В данном случае т.к. мы подрузомеваем, что админ - это тот же пользователь и должен обладать всеми его правами,
        то во втором условии возвращаем true если это обычный пользователь или админ

*/