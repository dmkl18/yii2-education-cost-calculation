<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;

/*

    В случае, если мы выполняем ajax-валидацию отдельных полей формы (например проверка существования логина в БД) в
        которой присутствует капча, то если ничего не предпринять, то после отправки формы она валидацию не пройдет, т.к.
        при каждой загрузке и валидации данных на стороне сервера (и не важно через ajax или нет) капча обновляется.
        И если использовать ajax-валидацию отдельных полей, капча, которая остается на странице уже не будет соответствовать обновленной.
        Для решения проблемы я создал дополнительный сценарий, который включает только те поля, которые будут
        проходить ajax-валидацию

*/

class RegistryForm extends Model
{
	
	public $username;
    public $password;
	public $password2;
	public $email;
	public $verifyCode;
	
	private $minl = 3;
	private $minPsd = 5;
	private $maxPL = 50;
	private $maxEmail = 120;
	
	public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['individual'] = ['username', 'email'];
        return $scenarios;
    }
	
	public function rules()
    {
        return [
            ['verifyCode', 'captcha', 'message' => 'Вы ввели не правильный проверочный код. Пожалуйста, исправьте это.'],
            [['username', 'password', 'password2', 'email'], 'required', 'message' => 'Данное поле является обязательным для заполнения'],
			[['username', 'password', 'password2', 'email'], 'filter', 'filter' => function($value){ return htmlspecialchars($value); }],
            ['username', 'match', 'pattern' => '/^[a-zа-яё][a-zа-яё0-9\s-]{'.($this->minl-1).','.($this->maxPL-1).'}$/ui', 'message' => 'Логин должен начинаться с буквы и иметь не менее '.$this->minl.' символов'],
			['username', 'unique', 'targetClass'=>'\app\models\dbmodels\User', 'targetAttribute'=>'login', 'message'=>'Пользователь с таким именем уже существует!'],
			['password', 'match', 'pattern' => '/^[a-z0-9][a-z0-9-\+!@\$\(\)\?]{'.($this->minPsd-1).','.($this->maxPL-1).'}$/ui', 'message' => 'Пароль должен иметь не менее '.$this->minPsd.' символов и начинаться с латинской буквы или цифры'],
			['password2', 'compare', 'compareAttribute' => 'password', 'operator' => '==='],
			['email', 'email'],
			['email', 'string', 'length'=>[$this->minl, $this->maxEmail], 'tooShort' => 'Email должен содержать хотя бы '.$this->minl.' символа.', 'tooLong' => 'Вы ввели слишком длинный email адрес, он должен быть не более чем '.$this->maxEmail.' символов.'],
			['email', 'unique', 'targetClass'=>'\app\models\dbmodels\User', 'targetAttribute'=>'mail', 'message'=>'Пользователь с таким адресом электронной почты уже существует!'],
        ];
    }
	
	public function attributeLabels()
    {
        return [
            'username' => 'Логин',
            'password' => 'Пароль',
            'password2' => 'Подтвердите пароль',
            'email' => 'Email',
			'verifyCode' => 'Проверочный текст',
        ];
    }

    public function sendMail()
    {
		Yii::$app->mailer->compose('user/regist', [
            'user' => $this,
        ])->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($this->email)
            ->setSubject('Сообщение о регистрации')
            ->send();
    }

}