<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use yii\web\Application;

class CalculateCostForm extends Model
{
    public $variantSpeciality;
    public $studyOption;
    public $educationVariant;
    public $technologyType;
    public $soPassed;
    public $username;
    public $email;
    public $city;
    public $info;
    public $benefits;
    public $payment;
    public $course;
    public $term;
    public $cost;
    public $user;

    private $min1 = 3;
    private $max1 = 254;
    private $min2 = 10;
    private $max2 = 65400;

    public function rules()
    {
        return [
            [['variantSpeciality', 'studyOption', 'educationVariant', 'technologyType', 'username', 'email', 'city'], 'required', 'message' => 'Поле не может быть пустым'],
            [['username', 'email', 'city', 'info'], 'filter', 'filter' => function($value) { return htmlspecialchars(trim($value)); }],
            [['variantSpeciality', 'studyOption', 'educationVariant', 'technologyType', 'course', 'cost', 'user'], 'integer'],
            [['benefits', 'payment'], 'default', 'value' => '0'],
            [['benefits', 'payment'], 'boolean'],
            ['email', 'email', 'message' => 'Вы должны указать правильный адрес электронной почты'],
            [['username', 'city'], 'match', 'pattern' => '/^[a-zа-я][a-zа-я-\s]{'.($this->min1 - 1).','.($this->max1 - 1).'}$/iu', 'message' => 'Данное поле может состоять только из букв, тире или пробелов и должно состоять минимум из 3 символов'],
            ['info', 'string', 'length' => [$this->min2, $this->max2], 'tooShort' => 'Данное поле должно состоять минимум из '.$this->min2.' символов', 'tooLong' => 'Данное поле не должно состоять более чем из '.$this->max2.' символов'],
            ['term', 'double'],
            ['soPassed', 'validatePassedSubjects'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'variantSpeciality' => '',
            'studyOption' => '',
            'educationVariant' => '',
            'technologyType' => '',
            'soPassed' => '',
            'username' => 'Имя',
            'email' => 'Email',
            'city' => 'Город',
            'info' => 'Дополнительная информация',
            'benefits' => 'рассчитываете на предоставление льготы при оплате за обучение',
            'payment' => 'хотите оплачивать обучение в рассрочку',
        ];
    }

    public function validatePassedSubjects($attribute, $params) {
        if($this->$attribute) {
            foreach($this->$attribute as $value) {
                if(!is_numeric($value) || $value < 1) {
                    $this->addError($attribute, 'Поле обязательно должно быть целым числом');
                    break;
                }
            }
        }
    }

}