<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;

class PasswordResetRequestForm extends Model
{

    public $email;

    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            ['email', 'required', 'message' => 'Данное поле является обязательным для заполнения'],
            ['email', 'filter', 'filter' => function($value){ return htmlspecialchars(trim($value)); }],
            ['email', 'email'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => 'Email',
        ];
    }

    public function sendMail($token)
    {
        return Yii::$app->mailer->compose('user/reset-password', [
            'token' => $token,
        ])->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($this->email)
            ->setSubject('Сообщение об изменении пароля')
            ->send();
    }

}