<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = 'Заявка на поступление пользователя '.$application['userLogin'];

$this->params['breadcrumbs'][] = $this->title;

?>

<div class="site-app">
    
	<h2>Заявка пользователя <?= $application['userLogin']; ?></h2>

	<div class="app">
	
			<div class="row">
				<div class="col-sm-6">
					<p>Заявка заполнена под именем <span class="text-primary"><?= Html::encode($application['userName']); ?></span></p>
					<p>Пользователь проживает в городе: <span class="text-primary"><?= Html::encode($application['city']); ?></span></p>
					<p>Email: <span class="text-primary"><?= $application['email']; ?></span></p>
					<p>Заявка добавлена  <span class="text-primary"><?= date('d.m.Y в H:i', $application['dateInsert']); ?></span></p>
				</div>
				<div class="col-sm-6">
					<p class="text-right">Полная стоимость обучения (руб): <span class="text-primary"><?= Html::encode($application['educationCost']); ?></span></p>
					<p class="text-right">Срок обучения (лет): <span class="text-primary"><?= Html::encode($application['educationTerm']); ?></span></p>
					<p class="text-right">Количество курсов: <span class="text-primary"><?= Html::encode($application['countCourses']); ?></span></p>
				</div>
			</div>
			<h3>Основные сведения о выбранном направлении</h3>
			<div>
				<p>Специальность: <span class="text-primary"><?= Html::encode($application['specialityGroup']); ?></span></p>
				<p>Профиль специальности: <span class="text-primary"><?= Html::encode($application['speciality']); ?></span></p>
				<p>Тип обучения: <span class="text-primary"><?= Html::encode($application['studyOption']); ?></span></p>
				<p>Вариант обучения: <span class="text-primary"><?= Html::encode($application['educationVariant']); ?></span></p>
				<p>Технология обучения: <span class="text-primary"><?= Html::encode($application['technologyType']); ?></span></p>
				<?php if($application['secondaryInformation']) { ?>
					<p>Дополнительная информация:</p>
					<p class="text-primary"><?= $application['secondaryInformation']; ?></p>
				<?php } ?>
			</div>
			<?php if($specialitySubjects) { ?>
				<h3>Предметы, изучаемые в рамках данной специальности:</h3>
				<div class="row">
					<div class="col-sm-<?= $passedSubjects ? '6' : '12'; ?>">
						<p>Далее представлен список все предметов, изучаемых в рамках данной специальности:</p>
						<ul>
						<?php foreach($specialitySubjects as $subject) { ?>
							<li><?= Html::encode($subject['name']); ?></li>
						<?php } ?>
						</ul>
					</div>
					<?php if($passedSubjects) { ?>
						<div class="col-sm-6">
							<p>Пользователем были указаны следующие предметы для перезачета:</p>
							<ul>
								<?php foreach($passedSubjects as $subject) { ?>
									<li><?= Html::encode($subject['name']); ?></li>
								<?php } ?>
							</ul>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
		
	</div>
	
</div>