<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    public function rules()
    {
        return [
            [['username', 'password'], 'required', 'message' => 'Поле не может быть пустым'],
			[['username', 'password'], 'filter', 'filter' => 'trim'],
            ['username', 'match', 'pattern' => '/^[a-zа-яё][a-zа-яё0-9\s-]{2,49}$/ui', 'message' => 'Логин должен начинаться с буквы и иметь не менее 3 символов'],
			['password', 'match', 'pattern' => '/^[a-z0-9][a-z0-9-\+!@\$\(\)\?]{4,49}$/ui', 'message' => 'Пароль должен иметь не менее пяти символов и латинские буквы'],
			['rememberMe', 'boolean'],
        ];
    }
	
	public function attributeLabels()
    {
        return [
            'username' => 'Логин',
			'password' => 'Пароль',
			'rememberMe' => 'запомнить меня',
        ];
    }

}