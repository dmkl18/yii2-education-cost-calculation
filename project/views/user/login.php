<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

$this->title = 'Вход';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h3>Войти на сайт</h3>

    <p>Пожалуйста введите Ваш логин и пароль:</p>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "<div class=\"col-sm-1\">{label}</div>\n<div class=\"col-sm-5 col-md-3\">{input}</div>\n<div class=\"col-sm-6 col-md-8\">{error}</div>",
            'labelOptions' => ['class' => 'control-label'],
        ],
    ]); ?>

    <?= $form->field($model, 'username') ?>

    <?= $form->field($model, 'password')->passwordInput() ?>

    <?= $form->field($model, 'rememberMe')->checkbox() ?>

    <div class="form-group">
        <div class="col-xs-offset-1">
            <?= Html::submitButton('Войти', ['class' => 'btn btn-success', 'name' => 'login-button']) ?>&nbsp;&nbsp;
			<a href="<?=\Yii::$app->urlManager->createUrl('user/request-reset-password') ?>" data-toggle="tooltip" data-placement="right" title="Нажмите, чтобы сменить пароль">Забыли пароль?</a>
		</div>
    </div>

    <?php ActiveForm::end(); ?>
	
</div>
