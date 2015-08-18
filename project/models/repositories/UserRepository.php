<?php

namespace app\models\repositories;

use yii\base\Model;
use app\models\dbmodels\User;
/*
	
	Данный класс при взаимодействии с БД использует класс, производный от ActiveRecord
		
	$dbAttributes - в данном случае используется только при выборке данных из таблицы как массива ($asArray = true)
		Выбирать данные как массив удобно (и впринципе следует) в том случае, если мы просто хотим вывести информацию из БД на экран
		Если мы хотим получить данные из таблицы БД для того, чтобы их отредактировать, то в этом случае данные следует извлекать как объект
		Для того, чтобы указать какие поля мы хотим отредактировать в не рамок этого класса следует использовать массив $attributes
		
	$attributes - содержит массив, где в качестве ключей используются названия полей, которые мы используем в приложении, а в качестве
		значений - наименования полей в соответствующей таблице БД
	
*/

class UserRepository
{
	
	private static $appFields = [
		'id' => 'idUsr',
		'login' => 'login',
		'password' => 'password',
		'email' => 'mail',
		'check' => 'curCheck',
		'role' => 'role',
		'registDate' => 'date',
		'resetToken' => 'resetToken',
	]; 
	
	private $dbAttributes;
	
	public static function getAttributes()
	{
		return self::$appFields;
	}
	
	//public методы------------------------------------------------------------------------

    public function __construct() 
	{
		$this->dbAttributes = [
			'`users`.`idUsr` as `id`',
			'`users`.`login` as `login`',
			'`users`.`password` as `password`',
			'`users`.`mail` as `email`',
			'`users`.`curCheck` as `check`',
			'`users`.`role` as `role`',
			'`users`.`date` + '.date('Z').' as `registDate`',
			'`users`.`resetToken` as `resetToken`',
		]; 
	}
	
	public function getOne($id, $asArray = false)
    {
        $data = $this->selectAllFields(['idUsr' => $id], $asArray);
		return $asArray ? $data->asArray()->one() : $data->one();
    }

    public function getOneByAttrs(array $where, $asArray = false) {
        $where = $this->prepareData($where);
        $data = $this->selectAllFields($where, $asArray);
        return $asArray ? $data->asArray()->one() : $data->one();
    }
	
	public function getOneByAttributes(array $where, $asArray = false) 
	{
		$data = $this->selectAllFields($where, $asArray);
        return $asArray ? $data->asArray()->one() : $data->one();
	}
	
	public function addOne(Model $data)
	{
		$tbl = new User();
		$localTime = time();
		$gmtTime = $localTime - date("Z", $localTime);
		$tbl->attributes = [
			'login' => $data->username,
			'password' => $this->createPassword($data->password),
			'mail' => $data->email,
			'date' => $gmtTime,
		];
		return $tbl->insert();
	}

    public function update(User $user, array $attributes)
    {
        $attributes = $this->prepareData($attributes);
        if($attributes['password']) {
            $attributes['password'] = $this->createPassword($attributes['password']);
        }
        if($attributes['date']) {
            $attributes['date'] -= date("Z", $attributes['date']);
        }
        $user->attributes = $attributes;
        return $user->update();
    }
	
	public function createPassword($password)
    {
        return \Yii::$app->getSecurity()->generatePasswordHash($password);
    }

    public function validatePassword(User $user, $password)
    {
        return \Yii::$app->getSecurity()->validatePassword($password, $user->password);
    }

	/*
		необходимо для авторизации
		каждый раз, когда пользователь авторизуется, для него создается специальная проверочная строка
	*/
    public function createCurrCheck(User $user)
    {
        $user->curCheck = sha1(substr(mt_rand(),0,18));
        return $user->update();
    }

    /*
        $user - объект класса User, для которого необходимо выполнить смену пароля
            $user->resetToken - строка, на конце которой будет время запроса на получение инструкций по смене пароля
    */
    public function createResetToken(User $user)
    {
        $user->resetToken = \Yii::$app->security->generateRandomString() . '-' . time();
        return $user->update() ? $user->resetToken : false;
    }

    /*
        $resetToken - строка, используемая для проверки возможности смены пароля
        \Yii::$app->params['userSettings']['maxResetTokenTime'] - содержит максимальное количество секунд,
            прошедших с момента отправки письма со ссылкой на смену пароля, в течение
            которых можно выполнить смену пароля
    */
    public function validResetToken($resetToken)
    {
        if(!$resetToken)
            return false;
        $data = explode('-', $resetToken);
        $basicTime = (int) end($data);
        return (time() - $basicTime) <= \Yii::$app->params['userSettings']['maxResetTokenTime'];
    }
	
	//private методы------------------------------------------------------------------------
	
	private function selectAllFields(array $where, $asArray) 
	{
		$data = User::find();
		if($asArray) {
			$data = $data->select(implode(',', $this->dbAttributes));
		}
		return $data->where($where);
	}

    private function prepareData(array $data) {
        $tableData = [];
        foreach($data as $key => $value) {
            $tableData[self::$appFields[$key]] = $value;
        }
        return $tableData;
    }
	
}