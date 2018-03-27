/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of Framadate/OpenSondage: Framasoft (https://github.com/framasoft)
 *
 * =============================
 *
 * Ce logiciel est régi par la licence CeCILL-B. Si une copie de cette licence
 * ne se trouve pas avec ce fichier vous pouvez l'obtenir sur
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 *
 * Auteurs de STUdS (projet initial) : Guilhem BORGHESI (borghesi@unistra.fr) et Raphaël DROZ
 * Auteurs de Framadate/OpenSondage : Framasoft (https://github.com/framasoft)
 */

import moment from 'moment';

$(document).ready(function () {

    // Global variables

    const $selected_days = $('#selected-days');
    const $removeaday = $('#remove-a-day');
    const $copyhours = $('#copyhours');
    const $next = $('button[name="choixheures"]');

    const $collectionHolder = $('#poll_date_choices_choices');


    const updateButtonState = () => {
        $removeaday.toggleClass('disabled', $selected_days.find('fieldset').length <= 1);
        $copyhours.toggleClass('disabled', !hasFirstDayFilledHours());
        $next.toggleClass('disabled', countFilledDays() < 1)
    };

    // at least 1 day filled and you can submit
    const isSubmitDaysAvailable = () => {
        return (countFilledDays() >= 1);
    };

    const countFilledDays = () => {
        let nb_filled_days = 0;
        $selected_days.find('fieldset legend input').each(() => {
            if ($(this).val() !== '') {
                nb_filled_days++;
            }
        });
        return nb_filled_days;
    };


    const hasFirstDayFilledHours = () => {
        let hasFilledHours = false;
        $selected_days.find('fieldset').first().find('.hours').each((index, element) => {
            if ($(element).val() !== '') {
                hasFilledHours = true;
            }
        });
        return hasFilledHours;
    };

    function addDayForm($collectionHolder, value) {

        let newForm = $collectionHolder.data('prototype');
        const index = $collectionHolder.find('input[type="date"]').length;
        newForm = newForm.replace(/__name__/g, index);

        const last_day = $selected_days.find('fieldset').filter(':last');
        const new_day = last_day.after(newForm).next();
        if (value) {
            new_day.find('input[type="date"]').val(value);
        }
        const moments_div = new_day.find('div#poll_date_choices_choices_'+ index +'_moments');

        const hour_form = moments_div.data('prototype');
        const new_hour = moments_div.html(hour_form);
        new_hour.after(hour_form);
        new_hour.after(hour_form);

    }

    function addHourForm($collectionHolder) {
        // Get the data-prototype explained earlier
        let newForm = $collectionHolder.data('prototype');

        newForm = newForm.replace(/__name__/g, $collectionHolder.find('input.hours').length);

        // Display the form in the page in an li, before the "Add a tag" link li
        const last_hour = $collectionHolder.find('.col-sm-2').filter(':last');
        last_hour.after(newForm);
    }

    function newDateFields(date) {
        addDayForm($collectionHolder, date);
    }

    function manageRemoveadayAndCopyhoursButtons() {
        var nb_days = $selected_days.find('fieldset').length;
        $('#day' + (getLastDayNumber() - 1)).focus();
        if (nb_days == 1) {
            $removeaday_and_copyhours.addClass('disabled');
        }
    }

    /**
     * Fills the first field
     *
     * @param dateStr
     * @returns {boolean}
     */
    const useFirstEmptyDateField = (dateStr) => {
        let used = false;
        $selected_days.find('fieldset legend input[type="date"]').each((index, elem) => {
            if (!used) {
                if ($(elem).val() == '') {
                    $(elem).val(dateStr);
                    used = true;
                }
            }
        });

        return used;
    };

    // Handle form submission
    $('form[name="formulaire-classic"]').on('submit', function (e) {
        if (!isSubmitDaysAvailable()) {
            e.preventDefault();
            e.stopPropagation();
        }
    });

    // Button "Remove all hours"

    $(document).on('click', '#resethours', function () {
        $selected_days.find('fieldset').each(function () {
            $(this).find('.hours:gt(2)').parent().remove();
        });
        $('#d0-h0').focus();
        $selected_days.find('fieldset .hours').val('');
    });

    // Button "Remove all days"

    $('#resetdays').on('click', function () {
        $selected_days.find('fieldset:gt(0)').remove();
        $('input[type="date"]').val('').focus();
        updateButtonState();
    });

    // Button "Copy hours of the first day"

    function addHour(last_hour, add_button) {

		// for and id
		var di_hj = last_hour.children('.hours').attr('id').split('-');
		var di = parseInt(di_hj[0].replace('d', ''));
		var hj = parseInt(di_hj[1].replace('h', ''));

		// label, title and placeholder
		var last_hour_label = last_hour.children('.hours').attr('placeholder');
		var hour_text = last_hour_label.substring(0, last_hour_label.indexOf(' '));

		// RegEx for multiple replace
		var re_label = new RegExp(last_hour_label, 'g');
		var re_id = new RegExp('"d' + di + '-h' + hj + '"', 'g');

		// HTML code of the new hour
		var new_hour_html =
		    '<div class="col-sm-2">' +
		    last_hour.html().replace(re_label, hour_text + ' ' + (hj + 2))
		        .replace(re_id, '"d' + di + '-h' + (hj + 1) + '"')
		        .replace(/value="(.*?)"/g, 'value=""') +
		    '</div>';

		// After 11 + button is disabled
		if (hj < 99) {
		    last_hour.after(new_hour_html);
		    $('#d' + di + '-h' + (hj + 1)).focus();
		    add_button.prev('.remove-an-hour').removeClass('disabled');
		    if (hj === 98) {
		        add_button.addClass('disabled');
		    }
		}
    }

    $copyhours.on('click', function () {
        var first_day_hours = $selected_days.find('fieldset:eq(0) .hours').map(function () {
            return $(this).val();
        });

        $selected_days.find('fieldset:gt(0)').each(function () {

            while($(this).find('.hours').length < first_day_hours.length){
                var last_hour = $(this).children('div').children('div:last').prev();
                var add_button = $(this).find('.add-an-hour');
                addHour(last_hour, add_button);
            }

            for (var i = 0; i < first_day_hours.length; i++) {
                $(this).find('.hours:eq(' + i + ')').val(first_day_hours[i]); // fill hours
            }
        });
    });

    // Buttons "Add an hour"

    $(document).on('click', '.add-an-hour', function (e) {
        const last_hour = $(e.target).parent('div').parent('div').prev();
        addHourForm(last_hour);
    });

    // Buttons "Remove an hour"

    $(document).on('click', '.remove-an-hour', function (e) {
        const last_hour = $(e.target).parent('div').parent('div').prev().children('.col-sm-2').last();
        // for and id
        const di_hj = last_hour.find('input').attr('id').split('_');
        const hj = parseInt(di_hj[di_hj.length - 1]);
        const di = parseInt(di_hj[di_hj.length - 3]);

        // The first hour must not be removed
        if (hj > 0) {
            last_hour.remove();
            $('#d' + di + '-h' + (hj - 1)).focus();
            $(e.target).next('.add-an-hour').removeClass('disabled');
            if (hj === 1) {
                $(e.target).addClass('disabled');
            }
        }
        updateButtonState();
    });

    // Button "Add a day"

    $('#add-a-day').on('click', function () {
        //newDateFields();
        addDayForm($collectionHolder);
    });

    // Button "Remove a day"

    $removeaday.on('click', function () {
        $selected_days.find('fieldset:last').remove();

        updateButtonState();
    });

    // Button "Remove the current day"

    $(document).on('click', '.remove-day', (e) => {
        if ($('#poll_date_choices_choices').find('fieldset').length > 1) {
            $(e.target).parents('fieldset').remove();
        }
        updateButtonState();
    });

    // Add an range of dates

    $('#interval_add').on('click', function (ev) {
        var startDateField = $('#range_start');
        var endDateField = $('#range_end');
        const startDate = moment(startDateField.val());
        const endDate = moment(endDateField.val());

        // Clear error classes
        startDateField.parent().removeClass('has-error');
        endDateField.parent().removeClass('has-error');

        const maxDates = 123; // 123 = 4 months
        const tooMuchDates = endDate.diff(startDate) > moment.duration(maxDates, 'days');

        if (startDate != null && endDate != null && !tooMuchDates) {
            if (startDate.isSameOrBefore(endDate)) {
                while (startDate.isSameOrBefore(endDate)) {
                    if (!useFirstEmptyDateField(startDate.format('YYYY-MM-DD'))) {
                        newDateFields(startDate.format('YYYY-MM-DD'));
                    }
                    startDate.add(1, 'day');
                }

                // Hide modal
                startDateField.val('');
                endDateField.val('');
                $('#add_days').modal('hide');
                // Dirty fix for modal not hiding
                updateButtonState();

            } else {
                setTimeout(function () {
                    startDateField.parent().addClass('has-error');
                    endDateField.parent().addClass('has-error');
                }, 200);

            }
        } else {
            setTimeout(function () {
                if (startDate == null || tooMuchDates) {
                    startDateField.parent().addClass('has-error');
                }
                if (endDate == null || tooMuchDates) {
                    endDateField.parent().addClass('has-error');
                }
            }, 200);

        }

    });

    $('.modal').on('hide.bs.modal', (e) => {
       console.log(e);
    });

    $('.modal').on('hidden.bs.modal', (e) => {
        console.log(e);
    });

    // Title update on hours and buttons -/+ hours

    $(document).on('change', '.input-group.date input', function () {
        // Define title on hours fields using the value of the new date
        $selected_days.find('.hours').each(function () {
            $(this).attr('title', $(this).parents('fieldset').find('legend input').val() + ' - ' + $(this).attr('placeholder'));
        });
        // Define title on buttons that add/remove hours using the value of the new date
        $('#selected-days .add-an-hour, #selected-days .remove-an-hour').each(function () {
            var title = $(this).attr('title');

            if (title.indexOf('-') > 0) {
                title = title.substring(title.indexOf('-') + 2, title.length);
            }
            $(this).attr('title', $(this).parents('fieldset').find('legend input').val() + ' - ' + title);
        });
    });

    $(document).on('keyup change', '.hours, #selected-days fieldset legend input', function () {
        updateButtonState();
    });
    updateButtonState();
});
