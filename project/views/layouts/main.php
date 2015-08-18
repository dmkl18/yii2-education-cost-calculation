<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>
    <div class="wrap">
        <?php

            $items = [
                ['label' => 'Главная', 'url' => Yii::$app->homeUrl],
            ];

            if(Yii::$app->user->isGuest) {
                $items[] = ['label' => 'Войти', 'url' => ['/user/login']];
                $items[] = ['label' => 'Регистрация', 'url' => ['/user/sign-in']];
            } else {
                if(\Yii::$app->user->can('admin')) {
					$items[] = ['label' => 'Заявки пользователей', 'url' => ['speciality/']];
				}
				$items[] = ['label' => 'Заявка на поступление', 'url' => ['speciality/calculate-cost']];
                $items[] = ['label' => 'Профиль',
                    'url' => Yii::$app->urlManager->createUrl(['user/profile', 'num' => Yii::$app->params['userSettings']['diffBetweenId'] + Yii::$app->session->get('__id')])
                ];
                $items[] = ['label' => 'Выйти (' . Yii::$app->user->identity->login . ')',
                    'url' => ['/user/logout'],
                    'linkOptions' => ['data-method' => 'post']
                ];
            }

            NavBar::begin([
                'brandLabel' => '&lt;&lt;ZZZ&gt;&gt;',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right', 'id' => 'main-menu'],
                'items' => $items,
				'activateItems' => false, //для того, чтобы по умолчанию ссылки с URL, совпадающим с текущим, не подсвечивались
            ]);
            NavBar::end();
        ?>

        <div class="container">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; &lt;&lt;ZZZ&gt;&gt; <?= date('Y') ?></p>
        </div>
    </footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
