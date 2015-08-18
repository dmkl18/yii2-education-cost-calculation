<?php

namespace app\models\dbmodels;

use yii\db\ActiveRecord; //является наследником класса Component, поэтому можно навешивать события и поведения
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;

class User extends ActiveRecord implements IdentityInterface
{
    
	//задаем роли пользователей (пока временные)
    const ROLE_DEFAULT_USER = 0;
    const ROLE_ADMIN = 1;

	const MY_TABLE_NAME = 'users';

    public static function tableName() //если бы название таблицы совпадало с именем контроллера, то явно указывать название таблицы бд было бы не нужно
	{
		return self::MY_TABLE_NAME;
	}
	
	//реализация интерфейса IdentityInterface-----------------------------------------------------------------------------------------------------------
	//реализация данного интерфейса необходима для того, чтобы иметь возможность реализовать роли для пользователей
	
	public static function findIdentity($id)
    {
        return self::find()->where(['idUsr' => $id])->one();
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('findIdentityByAccessToken не поддерживается.');
    }

    public static function findByUsername($username)
    {
	   return self::find()->where(['login' => $username])->one();
    }

    public function getId()
    {
		return $this->idUsr;
    }

    public function getAuthKey()
    {
        return $this->curCheck;
    }

    public function validateAuthKey($authKey)
    {
        return $this->curCheck === $authKey;
    }
	
	//конец реализации интерфейса------------------------------------------------------------------------------------------------------------

	public function getRegistDate() {
		return $this->date + date('Z');
	}
	
	public function rules() //для возможности задания значений через свойство attributes
	{
		return [
			[['login', 'password', 'mail', 'date', 'resetToken'], 'safe'],
		];
	}
	
}