<h2>Приветствуем Вас <?=$data->username; ?></h2>
<div>
    <p>Вами была заполнена заявка на поступление.</p>
    <p>В данной заявке содержались следующие сведения:</p>
    <ul>
        <li>Специальность: <?=$application['specialityGroup']; ?></li>
        <li>Профиль: <?=$application['speciality']; ?></li>
        <li>Вид обучения: <?=$application['educationVariant']; ?></li>
        <li>План обучения: <?=$application['studyOption']; ?></li>
        <li>Объем обучения: <?=$data->course; ?> курса(ов)</li>
        <li>
            <p>Дисциплины к изучению согласно выбранного профиля:</p>
            <ul>
                <?php foreach($subjects as $subject) { ?>
                    <li><?=$subject['name']; ?></li>
                <?php } ?>
            </ul>
            <?php if($passedSubjects) { ?>
                <p>При этом следующие дисциплины были Вами отмечены для перезачета:</p>
                <ul>
                    <?php foreach($passedSubjects as $subject) { ?>
                        <li><?=$subject['name']; ?></li>
                    <?php } ?>
                </ul>
            <?php } ?>
            <p>Таким образом количество платных дисциплин к изучению - <?= $paidSubjectsCount; ?></p>
        </li>
        <li>Технология обучения - <?= $application['technologyType']; ?></li>
        <li>Полная стоимость обучения - <?= $data->cost; ?> руб</li>
        <li>Срок обучения - <?= $data->term; ?> года(лет)</li>
    </ul>
    <?php if($data->benefits || $data->payment) { ?>
        <div>
            <?php if($data->benefits) { ?>
                <p>Вами было отмечено желание предоставления льгот во время учебы.</p>
            <?php } ?>
            <?php if($data->payment) { ?>
                <p>Вами было отмечено желание оплачивать обучение в рассрочку.</p>
            <?php } ?>
            <p>Ваши желания будут учтены</p>
        </div>
    <?php } ?>
    <div>
        <p>В ближайшее время Ваша заявка будет рассмотрена и мы с Вами свяжемся</p>
    </div>
</div>