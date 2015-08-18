<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Смена пароля';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="site-login">

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <?php if($message = \Yii::$app->getSession()->getFlash('warning')){ ?>

        <div class="col-lg-offset-1">
            <p class="bg-warning"><?=Html::encode($message); ?></p>
        </div>

    <?php }else{ ?>

        <div class="row">
			<div class="col-md-offset-3 col-md-6">
				<div class="panel panel-warning">
					<div class="panel-heading">
						<p class="panel-title">Введите новый пароль:</p>
					</div>
					<div class="panel-body">

						<?php $form = ActiveForm::begin([
							'id' => 'reset-password-form',
							'fieldConfig' => [
								'labelOptions' => ['class' => 'control-label'],
							],
						]); ?>

						<?= $form->field($model, 'password')->passwordInput(); ?>

						<?= $form->field($model, 'password2')->passwordInput(); ?>

						<div class="form-group">
							<div class="col-lg-offset-1 col-lg-11">
								<?= Html::submitButton('Сменить пароль', ['class' => 'btn btn-success', 'name' => 'reset-button']) ?>
							</div>
						</div>

						<?php ActiveForm::end(); ?>
					</div>
				</div>
			</div>
		</div>
    
	<?php } ?>

</div>
