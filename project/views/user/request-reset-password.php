<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Запрос на изменение пароля';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="site-reset-password">

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <?php if($message = \Yii::$app->getSession()->getFlash('warning')){ ?>

        <div class="col-lg-offset-1 col-lg-10">
            <p class="alert alert-warning"><?=Html::encode($message); ?></p>
        </div>

    <?php } else{ ?>
		<div class="row">
			<div class="col-sm-offset-3 col-sm-6">
				<div class="panel panel-warning">
					<div class="panel-heading">
						<p class="panel-title">Введите Ваш <strong>адрес электронной почты</strong>. На данный адрес будет выслано письмо с инмтрукциями по смене пароля.</p>
					</div>
					<div class="panel-body">
						
						<?php $form = ActiveForm::begin([
							'id' => 'request-reset-password-form',
							'fieldConfig' => [
								'labelOptions' => ['class' => 'control-label'],
							],
						]); ?>

						<?= $form->field($model, 'email')->input('email', ['placeholder' => 'Введите Ваш email']); ?>

						<div class="form-group">
							<div class="text-center">
								<?= Html::submitButton('Отправить', ['class' => 'btn btn-success', 'name' => 'reset-button']) ?>
							</div>
						</div>

						<?php ActiveForm::end(); ?>
						
					</div>
				</div>
			</div>
		</div>

    <?php } ?>

</div>