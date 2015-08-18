<?php
namespace app\commands;

use Yii;
use yii\console\Controller;
use app\components\rbac\UserGroupRule;
use app\components\rbac\UserOwnRule;

class RbacController extends Controller
{
    public function actionInit()
    {

        $auth = \Yii::$app->authManager;
        $auth->removeAll();

        //создаем роли
        $user = $auth->createRole('user');
        $admin = $auth->createRole('admin');

        $userGroupRule = new UserGroupRule();
        $auth->add($userGroupRule);

        $user->ruleName = $userGroupRule->name;
        $admin->ruleName = $userGroupRule->name;

        $fullViewUsers = $auth->createPermission('fullViewUsers');
        $fullViewUsers->description = 'Возможность детального просмотра кабинета любого пользователя';
        $auth->add($fullViewUsers);

        $userRule = new UserOwnRule();
        $auth->add($userRule);
        $fullViewOwnUser = $auth->createPermission('fullViewOwnUser');
        $fullViewOwnUser->description = 'Возможность детального просмотра своего кабинета';
        $fullViewOwnUser->ruleName = $userRule->name;
        $auth->add($fullViewOwnUser);
        $auth->addChild($fullViewOwnUser, $fullViewUsers);

        $auth->add($user);
        $auth->add($admin);

        $auth->addChild($user, $fullViewOwnUser);
        $auth->addChild($admin, $fullViewUsers);
        $auth->addChild($admin, $user);

    }
}