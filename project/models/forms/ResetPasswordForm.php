<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use yii\base\InvalidParamException;

class ResetPasswordForm extends Model
{

    public $password;
    public $password2;

    public function rules()
    {
        return [
            [['password', 'password2'], 'required', 'message' => 'Данное поле является обязательным для заполнения'],
            ['password', 'match', 'pattern' => '/^[a-z0-9][a-z0-9-\+!@\$\(\)\?]{4,49}$/ui', 'message' => 'Пароль должен иметь не менее пяти символов и начинаться с латинской буквы или цифры'],
            ['password2', 'compare', 'compareAttribute' => 'password', 'operator' => '==='],
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => 'Пароль',
            'password2' => 'Подтвердите пароль',
        ];
    }

}