<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\RegistryForm */

$this->title = 'Регистрация';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="site-signin">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if(\Yii::$app->getSession()->getFlash('success')){ ?>
        <div class="col-lg-offset-1 col-lg-10">
            <p class="alert alert-success">
                Вы зарегистрировались под логином <?=Html::encode($model->username); ?>.
                Для того, чтобы войти на сайт, Вам необходимо авторизоваться.
            </p>
        </div>
    <?php } else{ ?>
		<div>
			<p>Для регистрации необходимо заполнить все поля:</p>

			<?php $form = ActiveForm::begin([
				'id' => 'regist-form',
				'options' => ['class' => 'form-horizontal'],
				'fieldConfig' => [
					'template' => "<div class=\"col-md-5 col-lg-2\">{label}</div>\n<div class=\"col-md-7 col-lg-5\">{input}</div>\n<div class=\"col-md-12 col-lg-5\">{error}</div>",
					'labelOptions' => ['class' => 'control-label'],
				],
			]); ?>

			<?= $form->field($model, 'username', ['enableAjaxValidation' => true])->textInput(['placeholder' => 'Введите Ваш логин']); ?>

			<?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Введите пароль']); ?>

			<?= $form->field($model, 'password2')->passwordInput(['placeholder' => 'Подтвердите пароль']); ?>

			<?= $form->field($model, 'email', ['enableAjaxValidation' => true])->input('email', ['placeholder' => 'Введите Ваш email']); ?>

			<?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
				'template' => '<div class="row"><div class="col-md-4 captcha">{image}</div><div class="col-md-8">{input}</div></div>',
				'imageOptions' => [
					'data-toggle' => "tooltip", 
					'data-placement' => "top",
					'title' => 'Нажмите для смены изображения',
				]
			]) ?>

			<div class="form-group">
				<div class="col-lg-offset-1 col-lg-11">
					<?= Html::submitButton('Регистрация', ['class' => 'btn btn-success', 'name' => 'regist-button']) ?>
				</div>
			</div>

			<?php ActiveForm::end(); ?>
		</div>

    <?php } ?>

</div>
