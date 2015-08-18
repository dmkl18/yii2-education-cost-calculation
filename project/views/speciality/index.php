<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\components\widgets\PartStringWidget;

$this->title = 'Заявки на поступление';

$this->params['breadcrumbs'][] = $this->title;

?>

<div class="site-apps">
    
	<h2>Список заявок</h2>

	<div class="app-list">
		
		<?php if($materials) foreach($materials as $material){ ?>
	
			<div class="app-item">
				<div class="app-header">
					<h4>
						<a href="<?=\Yii::$app->urlManager->createUrl(["speciality/view", 'app' => HTML::encode($material['id'])]) ?>" title="Просмотр заявки">
							Заявка от <?= HTML::encode($material['userLogin']); ?>
						</a>
					</h4>
				</div>
				
				<div class="row">
					<div class="col-sm-6">
						<p>Имя отправителя заявки: <strong class="text-primary"><?= HTML::encode($material['userName']); ?></strong></p>
						<p>Отправитель проживает в городе: <strong class="text-primary"><?= HTML::encode($material['city']); ?></strong></p>
					</div>
					<div class="col-sm-6">
						<p class="text-right">Полная стоимость обучения (руб): <strong class="text-primary"><?= HTML::encode($material['educationCost']); ?></strong></p>
						<p class="text-right">Срок обучения (лет): <strong class="text-primary"><?= HTML::encode($material['educationTerm']); ?></strong></p>
						<p class="text-right">Количество курсов: <strong class="text-primary"><?= HTML::encode($material['countCourses']); ?></strong></p>
					</div>
				</div>
					
				<div class="row">
					<div class="col-sm-6">
						<p>Email: <span class="text-primary"><?= HTML::encode($material['email']); ?></span></p>
						<p>Заявка добавлена  <span class="text-primary"><?= date('d.m.Y в H:i', HTML::encode($material['dateInsert'])); ?></span></p>
					</div>
					<div class="col-sm-6">
						<p class="text-right">
							<a href="<?=\Yii::$app->urlManager->createUrl(["speciality/view", 'num' => HTML::encode($material['id'])]) ?>" class="btn btn-success" title="Просмотр заявки">Подробнее</a>
						</p>
					</div>
				</div>
			</div>
			
		<?php } else { ?>
			
			<p>В настоящий момент заявок нет</p>
			
		<?php } ?>
		
	</div>
	
	<div>
		<?php
		echo LinkPager::widget([
			'pagination' => $pages,
		]);	
		?>
	</div>
	
</div>