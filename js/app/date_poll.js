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

$(document).ready(function () {

    // Global variables

    var $selected_days = $('#selected-days');
    var $removeaday = $('#remove-a-day');
    var $copyhours = $('#copyhours');
    var $next = $('button[name="choixheures"]');


    var updateButtonState = function () {
        $removeaday.toggleClass('disabled', $selected_days.find('fieldset').length <= 1);
        $copyhours.toggleClass('disabled', !hasFirstDayFilledHours());
        $next.toggleClass('disabled', countFilledDays() < 1)
    };

    // at least 1 day filled and you can submit
    var isSubmitDaysAvaillable = function() {
        return (countFilledDays() >= 1);
    };

    var countFilledDays = function () {
        var nb_filled_days = 0;
        $selected_days.find('fieldset legend input').each(function () {
            if ($(this).val() != '') {
                nb_filled_days++;
            }
        });
        return nb_filled_days;
    };


    var hasFirstDayFilledHours = function () {
        var hasFilledHours = false;
        $selected_days.find('fieldset').first().find('.hours').each(function () {
            if ($(this).val() != '') {
                hasFilledHours = true;
            }
        });
        return hasFilledHours;
    };



    /**
     * Parse a string date
     * @param dateStr The string date
     * @param format The format PHP style (allowed: %Y, %m and %d)
     */
    var parseDate = function (dateStr, format) {
        var dtsplit = dateStr.split(/[\/ .:-]/);
        var dfsplit = format.split(/[\/ .:-]/);

        if (dfsplit.length != dtsplit.length) {
            return null;
        }

        // creates assoc array for date
        var df = [];
        for (var dc = 0; dc < dtsplit.length; dc++) {
            df[dfsplit[dc]] = dtsplit[dc];
        }

        // Build date
        return new Date(parseInt(df['%Y']), parseInt(df['%m']) - 1, parseInt(df['%d']), 0, 0, 0, 0);
    };

    var formatDate = function (date, format) {
        return format
            .replace('%d', ("00" +date.getDate()).slice(-2))
            .replace('%m', ("00" + (date.getMonth() + 1)).slice(-2))
            .replace('%Y', ("0000" + date.getFullYear()).slice(-4));
    };

    function getLastDayNumber(last_day) {
        if (last_day == null) {
            last_day = $selected_days.find('fieldset').filter(':last');
        }
        return parseInt(/^d([0-9]+)-h[0-9]+$/.exec($(last_day).find('.hours').filter(':first').attr('id'))[1])
    }

    function newDateFields(dateStr) {
        var last_day = $selected_days.find('fieldset').filter(':last');
        var last_day_title = last_day.find('legend input').attr('title');
        var new_day_number = getLastDayNumber(last_day) + 1;

        var re_id_hours = new RegExp('"d' + (new_day_number - 1) + '-h', 'g');
        var re_name_hours = new RegExp('name="horaires' + (new_day_number - 1), 'g');

        var new_day_html = last_day.html().replace(re_id_hours, '"d' + new_day_number + '-h')
            .replace('id="day' + (new_day_number - 1) + '"', 'id="day' + new_day_number + '"')
            .replace('for="day' + (new_day_number - 1) + '"', 'for="day' + new_day_number + '"')
            .replace(re_name_hours, 'name="horaires' + new_day_number)
            .replace(/value="(.*?)"/g, 'value=""')
            .replace(/hours" title="(.*?)"/g, 'hours" title="" p')
            .replace('title="' + last_day_title + '"', 'title="' + last_day_title.substring(0, last_day_title.indexOf(' ')) + ' ' + (new_day_number + 1) + '"');

        last_day
            .after('<fieldset>' + new_day_html + '</fieldset>')
            .next().find('legend input').val(dateStr);
        $('#day' + (new_day_number)).focus();
        updateButtonState();
    }

    function manageRemoveadayAndCopyhoursButtons() {
        var nb_days = $selected_days.find('fieldset').length;
        $('#day' + (getLastDayNumber() - 1)).focus();
        if (nb_days == 1) {
            $removeaday_and_copyhours.addClass('disabled');
        }
    }

    var useFirstEmptyDateField = function (dateStr) {
        var used = false;
        $selected_days.find('fieldset legend input').each(function () {
            if (!used) {
                if ($(this).val() == '') {
                    $(this).val(dateStr);
                    used = true;
                }
            }
        });

        return used;
    };

    // Handle form submission
    $(document.formulaire).on('submit', function (e) {
        if (!isSubmitDaysAvaillable()) {
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
        $('#day0').focus();
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

    $(document).on('click', '.add-an-hour', function () {
        var last_hour = $(this).parent('div').parent('div').prev();
	    addHour(last_hour, $(this));
    });

    // Buttons "Remove an hour"

    $(document).on('click', '.remove-an-hour', function () {
        var last_hour = $(this).parent('div').parent('div').prev();
        // for and id
        var di_hj = last_hour.children('.hours').attr('id').split('-');
        var di = parseInt(di_hj[0].replace('d', ''));
        var hj = parseInt(di_hj[1].replace('h', ''));

        // The first hour must not be removed
        if (hj > 0) {
            last_hour.remove();
            $('#d' + di + '-h' + (hj - 1)).focus();
            $(this).next('.add-an-hour').removeClass('disabled');
            if (hj == 1) {
                $(this).addClass('disabled');
            }
        }
        updateButtonState();
    });

    // Button "Add a day"

    $('#add-a-day').on('click', function () {
        newDateFields();
    });

    // Button "Remove a day"

    $removeaday.on('click', function () {
        $selected_days.find('fieldset:last').remove();
        $('#day' + (getLastDayNumber() - 1)).focus();

        updateButtonState();
    });

    // Button "Remove the current day"

    $(document).on('click', '.remove-day', function () {
        if ($('#days_container').find('fieldset').length > 1) {
            $(this).parents('fieldset').remove();
        }
        updateButtonState();
    });

    // Add an range of dates

    $('#interval_add').on('click', function (ev) {
        var startDateField = $('#range_start');
        var endDateField = $('#range_end');
        var startDate = parseDate(startDateField.val(), window.date_formats.DATE);
        var endDate = parseDate(endDateField.val(), window.date_formats.DATE);

        // Clear error classes
        startDateField.parent().removeClass('has-error');
        endDateField.parent().removeClass('has-error');

        var maxDates = 123; // 123 = 4 months
        var tooMuchDates = endDate - startDate > maxDates * 86400 * 1000;

        if (startDate != null && endDate != null && !tooMuchDates) {
            if (startDate <= endDate) {
                while (startDate <= endDate) {
                    var dateStr = formatDate(startDate, window.date_formats.DATE);
                    if (!useFirstEmptyDateField(dateStr)) {
                        newDateFields(dateStr);
                    }
                    startDate.setDate(startDate.getDate() + 1);
                }

                // Hide modal
                startDateField.val('');
                endDateField.val('');
                $('#add_days').modal('hide');
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
