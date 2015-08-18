<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Кабинет пользователя '.$material['login'];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="user">

	<?php if($message = \Yii::$app->session->getFlash('warning')) { ?>
		<div>
			<p class="alert alert-warning text-center"><?= $message; ?></p>
		</div>
	<?php } ?>

    <h3><?=Html::encode($material['login']); ?></h3>

    <div class="row">

        <div class="col-sm-6">
            <p>Зарегистрировался: <?=Html::encode($material['registDate']); ?></p>
			<?php if($applications) { ?>
				<?php if(!\Yii::$app->user->can('admin')) { ?>
                <p>
					<a href="<?= \Yii::$app->urlManager->createUrl(['speciality/view', 'num' => $applications[0]['id']]) ?>" class="btn btn-success" data-toggle="tooltip" data-placement="right" title="Нажмите для просмотра заявки">Посмотреть заявку</a>
				</p>
                <?php } else { ?>
                    <p>Список заявок администратора</p>
                    <ul>
                        <?php for($i = 0, $lh = count($applications); $i < $lh; $i++) { ?>
                            <li>
                                <a href="<?= \Yii::$app->urlManager->createUrl(['speciality/view', 'num' => $applications[$i]['id']]) ?>" class="link">Заявка №<?php echo $i + 1; ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
			<?php } ?>
		</div>
        <?php if(\Yii::$app->user->can('fullViewUsers', ['identity' => $material['id']])) { ?>
            <div class="col-sm-6">
                <p class="text-right">Email: <?=Html::encode($material['email']); ?></p>
            </div>
        <?php } ?>
    </div>

</div>
