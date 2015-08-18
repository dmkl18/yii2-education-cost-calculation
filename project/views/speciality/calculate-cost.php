<?php
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\bootstrap\ActiveForm;

$this->title = 'Определение стоимости обучения';
$this->params['breadcrumbs'][] = $this->title;
?>

<div>
    <?php if($applicationSent) { ?>

        <div class="alert alert-success text-center">
            <h3>Ваша заявка принята</h3>
            <p>Вам на почту выслано письмо с содержанием заполненной Вами заявки.</p>
            <p>Мы в ближайшее время Вам перезвоним.</p>
        </div>
        <div class="alert alert-warning">
            <p>Если вдруг письмо до Вас не дошло, Вы можете нам перезвонить и уточнить есть ли Ваша заявка.</p>
        </div>

    <?php } else { ?>

    <h3>Определение стоимости обучения</h3>

    <?php if($errorMessage = \Yii::$app->session->getFlash('error')) { ?>

            <div class="alert alert-danger">
                <p><?=$errorMessage; ?></p>
            </div>

    <?php } ?>

    <div id="calcPriceInfo">
        <div>
            <button type="button" class="btn btn-primary" data-change-text="Скрыть расценки" data-area-visible="false">Показать расценки</button>
        </div>
        <div class="hide bg-warning bg-imp-rounded bg-imp-warning">
            <?php foreach($cost as $ct) { ?>
				<p>
					<?php if($ct['idTechnologyType']) { ?> Технология обучения - <strong><?= strtolower($ct['technology']); ?></strong><?php } ?>
					<?php if($ct['idEducationVariant']) { ?>, вид обучения - <strong><?= strtolower($ct['educationVariant']); ?></strong><?php } ?>
					<?php if(!$ct['idTechnologyType'] && !$ct['idEducationVariant']) { ?>В остальных случаях<?php } ?>
					: <span class="price text-primary" data-tech-type="<?= $ct['idTechnologyType']; ?>" data-ed-view="<?= $ct['idEducationVariant']; ?>"><strong><?= $ct['cost']; ?></strong></span> для одной учебной дисциплины</p>
			<?php } ?>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">
                <?php $form = ActiveForm::begin([
                    'id' => 'calc-form',
                    'options' => ['class' => 'form-horizontal'],
                ]); ?>

                <section class="calc-item" data-current="current-item"> <!-- data-current - текущая открытая секция -->
                    <div id="item1" class="bg bg-primary calc-item-header">
                        <h3>Выбор специальности и профиля обучения</h3>
                    </div>
                    <div class="calc-item-main">
                        <div class="calc-full-text">
                            <p>
                                Пожалуйста, будьте внимательны при выборе профиля обучения (специальности).&nbsp;
                                Вы должны четко представлять себе, в чем будет состоять Ваша работа и почему Ваш выбор не является ошибкой.
                            </p>
                        </div>

                        <div>
                            <?php
                                for($i=0, $lh = count($speciality); $i < $lh; $i++) {

                                    if(!$i || $speciality[$i]['spname'] != $speciality[$i-1]['spname']) {
										$target = 'spn'.($i+1);
                            ?>

                                        <div class="sp">
                                            <h4>Специальность <span id="<?=$target; ?>"><?= Html::encode($speciality[$i]['spname']); ?></span></h4>

                            <?php
                                    }

                                    echo $form->field($model, 'variantSpeciality', [
                                        'template' => '<div>{input}<label for="sp'.($i+1).'">{label}</label></div><div>{error}</div><div>'.HtmlPurifier::process($speciality[$i]['description']).'</div>',
                                        'enableClientValidation' => false,
                                    ])->radio([
                                        'value' => Html::encode($speciality[$i]['id']),
                                        'id' => 'sp'.($i+1),
                                        'data-target' => '#'.$target,
                                        'data-count-subjects' => Html::encode($speciality[$i]['subjects']),
                                        'data-dependent-section-header' => '#item2',
                                        'uncheck' => null,
                                        'label' => '&nbsp;&nbsp;'.Html::encode($speciality[$i]['name']),
                                    ], false);

                                    if($i == $lh-1 || $speciality[$i]['spname'] != $speciality[$i+1]['spname']) {
                            ?>

                                        </div>

                            <?php
                                    }
                                }
                            ?>
                        </div>
                        <div class="calc-step">
                            <button type="button" class="btn btn-warning disabled sp-step-button" data-place="#item1" title="Перейти к расчету платных учебных дисциплин" disabled>
                                Для перехода к следующему разделу выберите специальность 
                            </button>
                        </div>
                    </div>
                </section>

                <section class="calc-item">
                    <div id="item2" class="bg bg-primary calc-item-header">
                        <h3>Расчет количества платных учебных дисциплин</h3>
                    </div>
                    <div class="calc-item-main">
                        <div class="calc-full-text">
                            <p>Мы с радостью перезачтем Вам до 15 учебных дисциплин, если у Вас имеется диплом о среднем или высшем профессиональном образовании.</p>
                            <p>Пожалуйста, будьте внимательны при сопоставлении учебных дисциплин.</p>
                        </div>

                        <div>
                            <?php
                            for($i=0, $lh = count($options); $i < $lh; $i++) {

                                $template = '<div>{input}<label for="so'.($i+1).'">{label}</label></div>';
                                if($options[$i]['passed']) {
                                    $template .= '<div><button type="button" id="so'.($i+1).'-passed" class="btn btn-primary" data-target="#so'.($i+1).'" data-toggle="tooltip" data-placement="left" title="Нажмите для просмотра дисциплин, доступных для перезачета">Перезачесть дисциплины</button></div>';
                                }
                                $template .= '<div>{error}</div><div>'.HtmlPurifier::process($options[$i]['description']).'</div>';

                                echo $form->field($model, 'studyOption', [
                                    'template' => $template,
                                    'enableClientValidation' => false,
                                ])->radio([
                                    'value' => Html::encode($options[$i]['id']),
                                    'id' => 'so'.($i+1),
                                    'uncheck' => null,
                                    'label' => '&nbsp;&nbsp;'.Html::encode($options[$i]['name']).($options[$i]['passed'] ? ' (индивидуальная программа обучения)' : ' (<span id="vcs'.($i+1).'"></span> учебных дисциплин)'),
                                ], false);

                            }
                            ?>
                        </div>

                        <div id="so-individual">
                            <p>Перезачтено дисциплин - <span id="so-individual-passed" class="text-primary"></span></p>
                            <p>(дисциплин к изучению <span id="so-individual-left" class="text-primary"></span>)</p>
                            <div class="row">
                                <div class="col-sm-10 col-sm-offset-1">
                                    <ul>
                                        <li>Для расчета учебных дисциплин Вам понадобиться Приложение (с оценками) к документу об образовании (диплом Техникума или ВУЗа) имеющегося у Вас на данный момент.</li>
                                        <li>В таблице перечислены учебные дисциплины возможные к перезачету по выбранному Вами образовательному профилю.</li>
                                        <li>Сопоставьте изученные дисциплины из Вашего документа о предыдущем образовании с перечнем учебных дисциплин указанных в таблице.</li>
                                        <li>Поставьте метку напротив совпавшей дисциплины.</li>
                                        <li>Вам может быть перезачтено не более <span id="maxPassed" class="text-primary"></span> учебных дисциплин.</li>
                                    </ul>
                                </div>
                                <div class="col-sm-10 col-sm-offset-1">
                                    <table class="table table-bordered table-striped"></table>
                                </div>
                            </div>
                        </div>
                        <div class="calc-step">
                            <button type="button" class="btn btn-warning disabled sp-step-button" data-place="#item2" title="Перейти к выбору вида обучения" disabled>
                                Для перехода к следующему разделу выберите вариант обучения
                            </button>
                        </div>
                    </div>
                </section>

                <section class="calc-item">
                    <div id="item3" class="bg bg-primary calc-item-header">
                        <h3>Выбор вида обучения</h3>
                    </div>
                    <div class="calc-item-main">
                        <div class="calc-full-text">
                            <p>Мы предлагаем Вам выбрать вид обучения. Делая свой выбор, Вы должны исходить из темпа и предполагаемого срока Вашего обучения</p>
                        </div>
                        <div>

                            <?php
                            for($i=0, $lh = count($variant); $i < $lh; $i++) {

                                echo $form->field($model, 'educationVariant', [
                                    'template' => '<div>{input}<label for="ve'.($i+1).'">{label}</label></div><div>{error}</div><div>'.HtmlPurifier::process($variant[$i]['description']).'</div>',
                                    'enableClientValidation' => false,
                                ])->radio([
                                    'value' => Html::encode($variant[$i]['id']),
                                    'id' => 've'.($i+1),
                                    'data-count-subjects' => Html::encode($variant[$i]['subjectsYear']),
                                    'uncheck' => null,
                                    'label' => '&nbsp;&nbsp;'.Html::encode($variant[$i]['name']),
                                ], false);

                            }
                            ?>

                        </div>
                        <div class="calc-step">
                            <button type="button" class="btn btn-warning disabled sp-step-button" title="Перейти к выбору технологии обучения" data-place="#item3" disabled>
                                Для перехода к следующему разделу выберите вид обучения
                            </button>
                        </div>

                    </div>
                </section>

                <section class="calc-item">
                    <div id="item4" class="bg bg-primary calc-item-header" data-last="1">
                        <h3>Выбор технологии обучения</h3>
                    </div>
                    <div class="calc-item-main">
                        <div class="calc-full-text">
                            <p>Студент сам выбирает одну из двух дистанционных технологий обучения.</p>
                            <p>Пожалуйста, выберите ту образовательную технологию, которая будет для Вас наиболее комфортной.</p>
                        </div>
                        <div>

                            <?php
                            for($i=0, $lh = count($technology); $i < $lh; $i++) {

                                echo $form->field($model, 'technologyType', [
                                    'template' => '<div>{input}<label for="lt'.($i+1).'">{label}</label></div><div>{error}</div><div>'.HtmlPurifier::process($technology[$i]['description']).'</div>',
                                    'enableClientValidation' => false,
                                ])->radio([
                                    'value' => Html::encode($technology[$i]['id']),
                                    'id' => 'lt'.($i+1),
                                    'uncheck' => null,
                                    'label' => '&nbsp;&nbsp;'.Html::encode($technology[$i]['name']),
                                ], false);

                            }
                            ?>

                        </div>
                        <div class="calc-step">
                            <button type="button" class="btn btn-warning disabled sp-step-button" data-place="#item4" title="Посмотреть стоимость и сроки обучения" disabled>
                                Для просмотра стоимости обучения выберите технологию обучения
                            </button>
                        </div>
                    </div>
                </section>

                <section id="calcSummary" class="calc-item">
                    <div>
                        <p class="alert alert-success recalculation hide"></p>
                    </div>
                    <div class="alert alert-warning price-info">
                        <p>
                            Объеем обучения: <span id="calcSummaryAmount1"></span> курса(ов) (<span id="calcSummaryAmount2"></span>)
                        </p>
                        <p>
                            Срок обучения: <span id="calcSummaryPeriod"></span> года(лет) (+ 1/2 года итоговая аттестация)
                        </p>
                        <p>
                            Стоимость: <span id="calcSummaryCost"></span> руб
                        </p>
                    </div>
                    <div>
                        <h3>Отправить заявку на поступление</h3>
                        <?= $form->field($model, 'username', [
                            'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>',
                        ])->textInput(['placeholder' => 'Имя']); ?>
                        <?= $form->field($model, 'email', [
                            'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>',
                        ])->input('email', ['placeholder' => 'Email', 'value' => $user->mail]); ?>
                        <?= $form->field($model, 'city', [
                            'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>',
                        ])->textInput(['placeholder' => 'Город']); ?>
                        <?= $form->field($model, 'info')->textarea(['placeholder' => 'Дополнительная информация', 'rows' => 5]); ?>
                        <p><strong>Отметьте, если Вы:</strong></p>
                        <?= $form->field($model, 'benefits', [ 'enableClientValidation' => false ])->checkbox(); ?>
                        <?= $form->field($model, 'payment', [ 'enableClientValidation' => false ])->checkbox(); ?>
                        <div>
                            <input type="hidden" name="CalculateCostForm[course]" value="" />
                            <input type="hidden" name="CalculateCostForm[term]" value="" />
                            <input type="hidden" name="CalculateCostForm[cost]" value="" />
                            <input type="hidden" name="CalculateCostForm[user]" value="<?= $user->idUsr; ?>" />
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-1 col-sm-2">
                                <?= Html::submitButton('Подать заявку', ['class' => 'btn btn-success', 'name' => 'speciality-button', 'id' => 'mybt']); ?>
                            </div>
                        </div>
                    </div>
                </section>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

    <?php } ?>

</div>

<?php
    $this->registerJsFile( '/web/js/speciality.min.js', ['depends' => ['yii\web\YiiAsset', 'yii\bootstrap\BootstrapAsset', 'yii\bootstrap\BootstrapPluginAsset'] ]);
?>