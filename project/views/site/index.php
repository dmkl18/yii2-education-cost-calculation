<?php

use yii\helpers\Html;

$this->title = 'Главная';
?>

<div class="site-index">

    <?php if($message = \Yii::$app->getSession()->getFlash('success')){ ?>

        <div class="row">
			<div class="col-lg-offset-1 col-lg-10">
				<p class="alert alert-success"><?=$message; ?></p>
			</div>
		</div>

    <?php } ?>

    <div class="jumbotron">
        <h1>Приветствуем Вас!</h1>

        <p class="lead">Вы зашли на сайт учебного заведения &lt;&lt;ZZZ&gt;&gt;.</p>

        <p>На нашем сайте Вы можете заполнить заявку на поступление.<br /> Выбирайте специальность, которая Вам наиболее интересна, и отправляйте анкету</p>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-md-6">
                <p>Обучение платное. Его стоимость, а также продолжительность зависит от выбранной специальности</p>

                <p>Количество дисциплин зависит от выбранной Вами специальности</p>

                <p><strong class="text-warning">Обращаем Ваше внимание</strong>, что в том случае, если у Вас уже есть диплом о Высшем образовании,
                    Вы можете перезачесть часть дисциплин, сократив тем самым как срок, так и стоимость обучения</p>

                <p><a class="btn btn-success" href="<?=\Yii::$app->urlManager->createUrl(["speciality/calculate-cost"]); ?>">Заполнить заявку</a></p>
            </div>
        </div>

    </div>
</div>
