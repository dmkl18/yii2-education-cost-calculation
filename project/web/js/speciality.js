var SpecialityApp = SpecialityApp || {};

(function($) {

    "use strict";

    function sendAjax(mdata, mobject, msuccess, merror) {
        $.ajax({
            type: 'POST',
            url: mdata.handler,
            dataType: "json",
            headers:{
                "X-Requested-With": "XMLHttpRequest",
            },
            data: {
                sp: mdata.sp,
            },
            success: function(data)
            {
                if(data != "no")
                    msuccess.call(mobject, data);
                else
                    merror.call(mobject);
            },
            error: function()
            {
                merror.call(mobject);
            }
        });
    }

    var Speciality = function(settings) {

        /*
         this.formId - id всей формы
         this.itemName - класс секций
         (Обязательное условие: класс шапки секции - this.itemName + '-header', класс тела секции - this.itemName + '-main')
         this.summaryId - id блока с результатом стоиомсти обучения и ввода контактной информации (последняя часть заявки на поступление)
         this.$stepButtons - jQuery объекты кнопок для перехода к следующим разделам.

         this.blockSubjectsToPassId - id блока с дисциплинами, доступными для перезачета
         this.handler - путь к скрипту, используемому для получения информации о предметах, доступных для перезачета для данной специальности
         this.$buttonSubjectsToPass- jQuery объек кнопки для формирования таблицы с дисциплинами для перезачета
         this.visibleBlockSubjectsToPass - переменная, указывающая на то, виден ли на данный момент блок с дисциплинами для перезачета

         this.$spChoosen = null; this.$soChoosen = null; this.$evChoosen = null; this.$ltChoosen = null;
         - переменные, содержащие jQuery объекты выбранных вариантов для каждой секции (jQuery объект радиокнопки)

         this.cost - объект Cost, используемый для получения информации о расценках
         this.summary - объект Summary, используемый для определения стоимости обучения
         this.subjectsToPass - объект PassedSubjects, используемый для формирования списка объектов, доступных для перезачета и для подсчета количества выбранных объектов

         //далее следует описание свойств, которые используются больше для декоративных свойств
         this.$vcs - JQuery объект элемента, куда в качестве текста будет помещено количество дисциплин, изучаемых по выбранной специальности
         this.loadImg - гифка загрузки

         */

        this.formId = settings.form;
        this.itemName = settings.itemName;
        this.summaryId = settings.summary;
        this.$stepButtons = $('#' + this.formId + ' .' + Speciality.STEP_BUTTONS_CLASS);

        this.blockSubjectsToPassId = settings.individualBlock;
        this.handler = settings.handler;
        this.$buttonSubjectsToPass = $('#' + settings.buttonSubjects);
        this.visibleBlockSubjectsToPass = false;

        this.$spChoosen = null;
        this.$soChoosen = null;
        this.$evChoosen = null;
        this.$ltChoosen = null;

        this.cost = settings.cost;
        this.summary = null;
        this.subjectsToPass = null;

        this.$vcs = settings.vcs ? $('#' + settings.vcs) : null;
        this.loadImg = settings.loadImg;
        $('#' + settings.maxPassed).text(Speciality.MAX_PASSED_SUBJECTS_COUNT);

        //events
        var that = this;
        $('#' + this.formId + ' input[name=CalculateCostForm\\[variantSpeciality\\]]').on('change', function(event) { that.chooseSpeciality(event); });
        $('#' + this.formId + ' input[name=CalculateCostForm\\[studyOption\\]]').on('change', function(event) { that.chooseStudyOption(event); });
        $('#' + this.formId + ' input[name=CalculateCostForm\\[educationVariant\\]]').on('change', function(event) { that.chooseEducationVariant(event); });
        $('#' + this.formId + ' input[name=CalculateCostForm\\[technologyType\\]]').on('change', function(event) { that.chooseLearningTechnology(event); });
        $('#' + this.formId + ' .' + this.itemName + '-header').on('click', function(event){ that.showHideSection(event); });
        $('#' + this.formId).on('submit', function(event){ that.beforeSubmit(event); });
        this.$stepButtons.on('click', function(event) {that.fixSection(event); });
        this.$buttonSubjectsToPass.on('click', function(event) { that.showSubjectsToPass(event); });

    };

    Speciality.HEAD_INFO_CLASS = 'sp-choosen';

    Speciality.STEP_BUTTONS_CLASS = 'sp-step-button';

    Speciality.SLIDE_TIME = 1000;

    Speciality.MAX_PASSED_SUBJECTS_COUNT = 10;

    /*
     Выбор обучения разделен на блоки, каждый из которых становится доступным только после того, как будет выбрано определенное значение в текущем блоке
     До выбора определенного пункта в блоке кнопка перехода к следующему разделу недоступна
     После выбора определенного пункта в текущем блоке, в data jQuery-объекта шапки данного блока заносится параметр confirm = true
     Также меняем класс шапки bg-primary на bg-success
     Кнопка перехода к следующему разделу становится доступной

     $tg - jQuery объект выбранного инпута (пункта)
     $ch - jQuery-объект предыдущего выбранного инпута рассматриваемого блока (если выбор происходит первый раз, то содержит null)
     (если $ch != null удалям у него data('chText'))
     button - номер блока (и соответственно кнопки для перехода к следующему блоку)
     */
    Speciality.prototype.baseChoose = function($tg, $ch, button) {
        if($ch === null) {
            var $section = $tg.parents('.' + this.itemName), $sectionButton = this.$stepButtons.eq(button);
            $section.children('.' + this.itemName + '-header').data('confirm', true).removeClass('bg-primary').addClass('bg-success');
            $sectionButton.prop('disabled', false).removeClass("btn-warning disabled").addClass("btn-success").text($sectionButton.attr('title'));
        } else {
            $ch.removeData('chText');
        }
        return $tg;
    };

    /*
     Срабатывает при выборе специальности
     В случае, если специальность выбирается не первый раз есть вероятность того, что при предыдущем выборе специальности выбирались дисциплины для перезачета
     В этом случае мы должны очистить блок с дисциплинами для перезачета
     */
    Speciality.prototype.chooseSpeciality = function(event) {
        this.$spChoosen = this.baseChoose($(event.target), this.$spChoosen, 0);
        if(this.visibleBlockSubjectsToPass) {
            this.clearBlocSubjectsToPass();
        }
        this.$spChoosen.data('chText', 'Специальность / профиль: ' + $.trim($(this.$spChoosen.data('target')).text()) + ' / ' + $.trim($('label[for=' + this.$spChoosen.attr('id') + ']').text()));
        //далее дополнительные моменты, которые в принципе не так уж и важны
        if(this.$vcs) {
            this.$vcs.text(this.$spChoosen.data('countSubjects'));
        }
        //секция с дисциплинами для перезачета зависит от данной секции, поэтому при внесении изменений мы выводим текст с пояснениями
        var $dependentInfo = $(this.$spChoosen.data('dependentSectionHeader') + ' .' + Speciality.HEAD_INFO_CLASS);
        if($dependentInfo.length) {
            $dependentInfo.text('Изменен перечень дисциплин. Дисциплины для перезачета при необходимости требуется выбрать заново').addClass('alert alert-warning');
        }
    };

    /*
     Выбор программы обучения
     если выбираем полный учебный курс и при этом активен блок с дисциплинами для перезачета, скрываем его
     */
    Speciality.prototype.chooseStudyOption = function(event) {
        this.$soChoosen = this.baseChoose($(event.target), this.$soChoosen, 1);
        var chText = 'Дисциплин к изучению: ' + $.trim(this.$spChoosen.data('countSubjects'));
        chText += this.visibleBlockSubjectsToPass ? ' (индивидуальная программа)' : ' (стандартная программа)';
        this.$soChoosen.data('chText', chText);
        if(this.$soChoosen.val() == 1 && this.visibleBlockSubjectsToPass == true) {
            this.clearBlocSubjectsToPass();
        }
    };

    /*
     Выбор варианта обучения
     */
    Speciality.prototype.chooseEducationVariant = function(event) {
        this.$evChoosen = this.baseChoose($(event.target), this.$evChoosen, 2);
        this.$evChoosen.data('chText', 'Вид обучения: ' + $.trim($('label[for=' + this.$evChoosen.attr('id') + ']').text()));
    };

    /*
     Выбор технологии обучения
     */
    Speciality.prototype.chooseLearningTechnology = function(event) {
        this.$ltChoosen = this.baseChoose($(event.target), this.$ltChoosen, 3);
        var chText = 'Технология обучения: ' + $.trim($('label[for=' + this.$ltChoosen.attr('id') + ']').text());
        chText += ' (' + this.$evChoosen.data('countSubjects') + ' дисциплин / год)';
        this.$ltChoosen.data('chText', chText);
    };

    /*
     Данный метод вызывается при клике на кнопку перехода к следующему разделу
     */
    Speciality.prototype.fixSection = function(event) {
        event.preventDefault();
        var $target = $($(event.target).data('place'));
        var showNext = $target.data('last') ? false : true;
        this.SectionSlideUp($target, showNext);
    };

    /*
     Данный метод вызывается при нажатии на шапку секции (при этом секция уже должна быть активной)
     - Если это шапка текущей секции, то скрываем ее (и если это последняя или если уже отображен блок со стоиомстью, то не открываем следующую, иначе следующую открываем)
     - Если нет, то отображаем ее и скрываем бывшую текущую секцию
     */
    Speciality.prototype.showHideSection = function(event) {
        var $tg = $(event.currentTarget);
        if($tg.data('confirm')) {
            if($tg.parent().attr('data-current') == 'current-item') {
                var showNext = (this.summary || $tg.data('last')) ? false : true;
                this.SectionSlideUp($tg, showNext);
            }
            else {
                var $currentItem = $('#' + this.formId + ' .' + this.itemName + '[data-current=current-item] .' + this.itemName + '-header');
                if($currentItem) {
                    if($currentItem.data('confirm')) {
                        this.SectionSlideUp($currentItem, false);
                    } else {
                        $currentItem.next().slideUp(Speciality.SLIDE_TIME, function() {
                            $(this).parent().removeAttr('data-current');
                        });
                    }
                }
                this.SectionSlideDown($tg);
            }
        }
    };

    /*
     $target - jQuery объект шапки
     используется для открытия секции
     */
    Speciality.prototype.SectionSlideDown = function($target) {
        $target.children('.' + Speciality.HEAD_INFO_CLASS).remove();
        $target.next().slideDown(Speciality.SLIDE_TIME, function() {
            $(this).parent().attr('data-current', 'current-item').end().find('input:radio').prop('disabled', false);
        });
    };

    /*
     $target - jquery объект элемента, в конец которого мы будем помещать текст с выбранным значением (в данном случае это header секции)
     showNext - параметр, указывающий необходимо ли открывать следующую секцию
     Перед сворачиванием секции все радио-инпуты этой секции disabled
     Текущая открытая секция имеет атрибут data-current=current-item, поэтому после закрытия текущей и открытия следующей
     у текущей удаляем данный атрибут и добавляем его следующей
     В случае, если данная секция последняя - показываем блок с сообщением о стоимости обучения
     */
    Speciality.prototype.SectionSlideUp = function($target, showNext) {
        var that = this;
        var $ri = $target.next().find('input:radio').prop('disabled', true), chText = $ri.filter(':checked').data('chText');
        $target.next().slideUp(Speciality.SLIDE_TIME, function() {
            $target.append('<p class="' + Speciality.HEAD_INFO_CLASS + '">' + chText + '</p>').parent().removeAttr('data-current');
            if(+$target.data('last') === 1 && !that.summary) {
                that.showSummary();
            }
        });
        if(showNext) {
            this.SectionSlideDown($target.parent().next().children('.' + this.itemName + '-header'));
        }
    };

    /*
     Событие возникает при клике на кнопку по отображению дисциплин для перезачета
     При этом помечаем активным радио-инпут, который указан в data-target кнопки
     */
    Speciality.prototype.showSubjectsToPass = function(event) {
        event.preventDefault();
        $($(event.target).data('target')).prop('checked', true).trigger('change');
        if(this.$spChoosen) {
            if(this.subjectsToPass === null) {
                this.subjectsToPass = new PassedSubjects(this.blockSubjectsToPassId, 'CalculateCostForm[soPassed]', Speciality.MAX_PASSED_SUBJECTS_COUNT);
            }
            var value = this.$spChoosen.val();
            $(event.target).hide().after(this.loadImg);
            sendAjax({handler: this.handler, sp: value}, this, this.successShowSubjectsToPass, this.errorShowSubjectsToPass);
        }
    };

    /*
     очищаем данные блока с дисциплинами для перезачета
     Это необходимо выполнять в случае смены специальности или при выборе полного курса обучения
     */
    Speciality.prototype.clearBlocSubjectsToPass = function() {
        this.visibleBlockSubjectsToPass = false;
        this.subjectsToPass.clear();
        this.$buttonSubjectsToPass.css('display', 'inline-block');
    };

    /*
     Метод вызывается в случае успешного Ajax-запроса на получение дисциплин для перезачета
     Осуществляется подготовка для отображения блока с дисциплинами для перезачета
     Затем данный блок отображается
     */
    Speciality.prototype.successShowSubjectsToPass = function(data) {
        this.$buttonSubjectsToPass.next().remove();
        this.visibleBlockSubjectsToPass = true;
        if(this.subjectsToPass.errorMessageStatus) {
            this.subjectsToPass.hideErrorMessage(true);
        }
        this.subjectsToPass.createView(data, this.$spChoosen.data('countSubjects'), this, 'changeCountSubjects');
        this.subjectsToPass.$bl.css('display', 'block');
    };

    /*
     Метод, вызываемый в случае неудачного Ajax-запроса на получение дисциплин для перезачета
     */
    Speciality.prototype.errorShowSubjectsToPass = function() {
        this.$buttonSubjectsToPass.show().next().remove();
        this.subjectsToPass.errorOfData();
    };

    /*
     Обработчик события клика по чекбоксу
     В случае выбора максимального количества дисциплин для перезачета Speciality.MAX_PASSED_SUBJECTS_COUNT
     чекбоксы оставшихся дисциплин получают свойство readonly
     */
    Speciality.prototype.changeCountSubjects = function(event) {
        this.subjectsToPass.changeCountSubjects(event);
        var chText = 'Дисциплин к изучению: ' + (this.$spChoosen.data('countSubjects') - this.subjectsToPass.passedCount) + ' (индивидуальная программа)';
        this.$soChoosen.data('chText', chText);
    };

    /*
     Отображаем блок со стоиомстью
     */
    Speciality.prototype.showSummary = function($target) {
        var summary = this.getSummary();
        var countSubjectsByYear = +this.$evChoosen.data('countSubjects');
        var countPassedSubjects = this.visibleBlockSubjectsToPass ? this.subjectsToPass.passedCount : 0;
        var countSubjects = +this.$spChoosen.data('countSubjects') - countPassedSubjects;
        var info = summary.createData(countSubjects, countSubjectsByYear, +this.$ltChoosen.val(), +this.$evChoosen.val());
        if(summary.$summary.css('display') == 'none') {
            var that = this;
            summary.$summary.fadeIn(Speciality.SLIDE_TIME, function () {
                $('#' + that.formId).on('change', '.' + that.itemName + '-main input', function() { var type = $(this).attr("type"); if(type == 'radio' || type == 'checkbox') that.showSummary(); });
            });
        } else {
            summary.showMessage();
        }
        //помещаем в скрытые инпуты необходимую информацию
        $('#' + this.formId + ' input[name=CalculateCostForm\\[course\\]]').val(info[0]);
        $('#' + this.formId + ' input[name=CalculateCostForm\\[term\\]]').val(info[1]);
        $('#' + this.formId + ' input[name=CalculateCostForm\\[cost\\]]').val(info[2]);
    };

    Speciality.prototype.getSummary = function() {
        if(this.summary === null) {
            this.summary = new Summary(this.summaryId, this.cost.getCostData());
        }
        return this.summary;
    };

    Speciality.prototype.beforeSubmit = function(event) {
        $('#' + this.formId + ' input:radio').prop('disabled', false);
    };

    /*
     Класс используется для подсчета стоимоти и продолжительности обучения
     В случае пересчета стоимости появляется соответствующее сообщение
     */
    var Summary = function(id, costData) {
        this.$summary = $('#' + id);
        this.costData = costData;
        this.$ammount1 = $('#' + id + 'Amount1');
        this.$ammount2 = $('#' + id + 'Amount2');
        this.$period = $('#' + id + 'Period');
        this.$cost = $('#' + id + 'Cost');
        this.messageStatus = false;
    };

    Summary.MESSAGE_CLASS = "recalculation";

    Summary.MESSAGE_TIME = 7000;

    Summary.MAX_SUBJECT_COUNT = 45;

    Summary.prototype.createData = function(count, countForYear, typeTechnology, edVariant) {
        var amount = Math.ceil(count/countForYear), amountText, period = amount, baseCost;
        this.$ammount1.text(amount);
        if(count != Summary.MAX_SUBJECT_COUNT) {
            amountText = "1 курс - " + countForYear + " дисциплин";
        }
        else {
            var bc = Math.floor(Summary.MAX_SUBJECT_COUNT / countForYear);
            var rc = count - bc * countForYear;
            var bcText = bc < 5 ? bc + " курса" : bc + " курсов";
            amountText = bcText + " - " + countForYear + " дисциплин, " + (bc+1) + " курс - " + rc + " дисциплин";
        }
        this.$ammount2.text(amountText);
        this.$period.text(period);
        for(var i= 0, lt = this.costData.length - 1; i<lt; i++) {
            if(typeTechnology == this.costData[i].tech && (edVariant == this.costData[i].ed || !this.costData[i].ed)) {
                baseCost = this.costData[i].value;
                break;
            }
        }
        if(!baseCost) {
            baseCost = this.costData[lt-1].value;
        }
        var cost = baseCost * count;
        this.$cost.text(cost);
        return [amount, period, cost];
    };

    Summary.prototype.showMessage = function() {
        if(this.messageStatus) {
            this.clearMessage();
            clearTimeout(this.h);
        }
        var that = this;
        var message = "Был выполнен пересчет стоимости.";
        this.messageStatus = true;
        this.$summary.find('.' + Summary.MESSAGE_CLASS).text(message).removeClass('hide').addClass('show');
        this.h = setTimeout(function(){ that.clearMessage() }, Summary.MESSAGE_TIME);
    };

    Summary.prototype.clearMessage = function() {
        this.messageStatus = false;
        this.$summary.find('.' + Summary.MESSAGE_CLASS).text('').removeClass('show').addClass('hide');
    };

    /*
     конструктор класса, используемый для работы с дисциплинами для перзачета
     В целом данный класс используется для формирования таблицы предметов для перезачета,
     подсчета предметов для перезачета
     */
    var PassedSubjects = function(id, rowName, maxPassedSubjects) {
        this.$bl = $('#' + id);
        this.$table = $('#' + id + ' table');
        this.$subjectsPassed = $('#' + id + '-passed');
        this.$subjectsLeft = $('#' + id + '-left');
        this.tableTemplate = '<tr><td><input type="checkbox" id="subject" name="' + rowName + '[]" value="" /></td><td></td></tr>';
        this.maxPassedSubjects = maxPassedSubjects;
        this.errorMessageStatus = false;
        this.passedMessageStatus = false;
    };

    PassedSubjects.ERROR_CLASS = 'error-passed';

    PassedSubjects.ERROR_SHOW_TIME = 7000;

    PassedSubjects.MAX_PASSED_MESSAGE_CLASS = 'max-passed';

    PassedSubjects.MAX_PASSED_SUBJECT_MESSAGE = '<p class="' + PassedSubjects.MAX_PASSED_MESSAGE_CLASS + ' alert alert-warning">Выбрано максимальное число дисциплин для перезачета</p>';

    /*
     data - список предметов, доступных для перезачета
     maxSubjects - всего объектов по данной специальности
     ob - объект класса Speciality
     По шаблону формируем строки таблицы
     Задаем начальные значения количества отмеченных дисциплин и дисциплин в целом
     Задаем обработчик события клика по чекбоксу
     */
    PassedSubjects.prototype.createView = function(data, maxSubjects, ob, obMethod) {
        var template = this.tableTemplate, tmp = '', index = template.indexOf('subject') + 7, index2 = template.indexOf('value') + 7, index3 = template.lastIndexOf('<td>') + 4;
        for(var i = 0, lh = data.length; i<lh; i++) {
            tmp += template.slice(0, index) + data[i]['id'] + template.slice(index, index2) + data[i]['id'] + template.slice(index2, index3) + '<label for="subject' + data[i]['id'] + '">' + data[i]['name'] + '</label>' + template.slice(index3);
        }
        this.passedCount = 0;
        this.leftCount = maxSubjects;
        this.$table.append(tmp);
        this.$subjectsPassed.text(this.passedCount);
        this.$subjectsLeft.text(this.leftCount);
        var that = this;
        this.$table.on('change', 'input:checkbox', function(event) { ob ? ob[obMethod](event) : that.changeCountSubjects(event); });
    };

    /*
     В случае ошибки получения предметов для перезачета отображаем сообщение
     */
    PassedSubjects.prototype.errorOfData = function() {
        if(this.errorMessageStatus) {
            this.hideErrorMessage(true);
        }
        var that = this;
        var message = "Извините, но при попытке получить данные возникла ошибка. Попробуйте получить данные еще раз.";
        this.errorMessageStatus = true;
        this.$bl.before('<p class="' + PassedSubjects.ERROR_CLASS + ' bg-danger">' + message + '</p>');
        this.h = setTimeout(function(){ that.hideErrorMessage(false); }, PassedSubjects.ERROR_SHOW_TIME);
    };

    PassedSubjects.prototype.hideErrorMessage = function(clear) {
        if(clear) {
            clearTimeout(this.h);
        }
        var $errorMessage = this.$bl.prev();
        if($errorMessage.attr('class').indexOf(PassedSubjects.ERROR_CLASS) !== -1) {
            $errorMessage.remove();
        }
        this.errorMessageStatus = false;
    };

    PassedSubjects.prototype.changeCountSubjects = function(event) {
        var i = event.target.checked ? 1 : -1;
        if(this.passedCount < this.maxPassedSubjects || (this.passedCount == this.maxPassedSubjects && i == -1)) {
            this.passedCount += i;
            this.leftCount -= i;
            this.$subjectsPassed.text(this.passedCount);
            this.$subjectsLeft.text(this.leftCount);
            if(this.passedCount == this.maxPassedSubjects) {
                this.$table.find('input:not(:checked)').prop('disabled', true);
                this.$bl.prepend(PassedSubjects.MAX_PASSED_SUBJECT_MESSAGE);
                this.passedMessageStatus = true;
            } else if(this.passedCount == (this.maxPassedSubjects - 1) && i == -1) {
                this.$table.find('input').prop('disabled', false);
                if(this.passedMessageStatus) {
                    this.$bl.children('.' + PassedSubjects.MAX_PASSED_MESSAGE_CLASS).remove();
                    this.passedMessageStatus = false;
                }
            }
        }
    };

    PassedSubjects.prototype.clear = function() {
        this.$table.empty();
        this.$table.off('change');
        this.$bl.css('display', 'none');
        this.leftCount += this.passedCount;
        this.passedCount = 0;
        this.$bl.find('.' + PassedSubjects.MAX_PASSED_MESSAGE_CLASS).remove();
    };

    /*
     Конструктор класса, используемого для отображения при необходимости расценок на обучение,
     а также для формирования списка стоимости обучения в зависимости от изучаемой специальности
     */
    var Cost = function(id) {
        this.costId = id;
        var that = this;
        $('#' + this.costId + ' button').on('click', function(event) { that.showHide(event); });
    };

    Cost.prototype.showHide = function(event) {
        event.preventDefault();
        var $target = $(event.target), $area;
        if(!$target.data('areaVisible')) {
            $area = $('#' + this.costId + ' .hide');
            $target.data('areaVisible', true);
            $area.removeClass('hide').addClass('show');
        }
        else {
            $area = $('#' + this.costId + ' .show');
            $target.data('areaVisible', false);
            $area.removeClass('show').addClass('hide');
        }
        var ch = $target.text();
        $target.text($target.attr('data-change-text')).attr('data-change-text', ch);
    };

    Cost.prototype.getCostData = function() {
        var prices = [];
        $('#' + this.costId + ' .price').each(function() {
            var price = {};
            price.tech = +$(this).data('techType');
            price.ed = +$(this).data('edView');
            price.value = +$(this).text();
            prices.push(price);
        });
        return prices;
    };

    /*
     settings - объект, содержащий:
     settings.form - id всей формы
     settings.itemName - класс секций
     settings.individualBlock - id блока с дисциплинами для перезачета
     settings.buttonSubjects - id кнопки для отображения блока с дисциплинами для перезачета
     settings.handler - обработчик, который возвращает список возможных дисциплин для перезачета в соответствии с выбранной специальностью
     settings.costBlock - id блока с расценками за обучение

     //дополнительные настройки
     settings.vcs
     settings.maxPassed
     */
    function initializeSpeciality(settings) {

        //загрузим изображение гифки
        var loadImg = new Image();
        loadImg.setAttribute('src', settings.loadImg);
        loadImg.setAttribute('alt', 'Loading...');

        settings.loadImg = loadImg;
        settings.cost = new Cost(settings.costBlock);

        return new Speciality(settings);

    }

    SpecialityApp.initializeSpeciality = initializeSpeciality;

}(jQuery));


$(document).ready(function(){
    "use strict";
	
	var mySpeciality = SpecialityApp.initializeSpeciality({
        form: 'calc-form',
        individualBlock: 'so-individual',
        buttonSubjects: 'so2-passed',
        itemName: 'calc-item',
        summary: 'calcSummary',
        handler: 'http://speciality/web/index.php/speciality/show-passed-subjects',
        costBlock: 'calcPriceInfo',
		loadImg: "http://speciality/web/images/loader.gif",
        //далее следуют дополнительные (необязательные настройки)
        vcs: 'vcs1',
        maxPassed: 'maxPassed',
    });

});