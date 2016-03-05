/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of Framadate/OpenSondate: Framasoft (https://github.com/framasoft)
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
    var $removeaday_and_copyhours = $('#remove-a-day, #copyhours');

    // at least 1 day filled and you can submit

    var submitDaysAvalaible = function () {
        var nb_filled_days = 0;

        $selected_days.find('fieldset legend input').each(function () {
            if ($(this).val() != '') {
                nb_filled_days++;
            }
        });

        if (nb_filled_days >= 1) {
            $('button[name="choixheures"]').removeClass('disabled');
            return true;
        } else {
            $('button[name="choixheures"]').addClass('disabled');
            return false;
        }
    };

    // Handle form submission
    $(document.formulaire).on('submit', function (e) {
        if (!submitDaysAvalaible()) {
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
        $selected_days.find('fieldset .hours').attr('value', '');
    });

    // Button "Remove all days"
    $('#resetdays').on('click', function () {
        $selected_days.find('fieldset:gt(0)').remove();
        $('#day0').focus();
        $removeaday_and_copyhours.addClass('disabled');
    });

    // Button "Copy hours of the first day"

    $('#copyhours').on('click', function () {
        var first_day_hours = $selected_days.find('fieldset:eq(0) .hours').map(function () {
            return $(this).val();
        });

        $selected_days.find('fieldset:gt(0)').each(function () {
            for (var i = 0; i < first_day_hours.length; i++) {
                $(this).find('.hours:eq(' + i + ')').val(first_day_hours[i]); // fill hours
            }
        });
    });

    // Buttons "Add an hour"

    $(document).on('click', '.add-an-hour', function () {
        var last_hour = $(this).parent('div').parent('div').prev();

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

        // After 11 + button is disable
        if (hj < 99) {
            last_hour.after(new_hour_html);
            $('#d' + di + '-h' + (hj + 1)).focus();
            $(this).prev('.remove-an-hour').removeClass('disabled');
            if (hj == 98) {
                $(this).addClass('disabled');
            }
        }

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
        submitDaysAvalaible();
    });

    // Button "Add a day"

    $('#add-a-day').on('click', function () {
        var nb_days = $selected_days.find('fieldset').length;
        var last_day = $selected_days.find('fieldset:last');
        var last_day_title = last_day.find('legend input').attr('title');

        var re_id_hours = new RegExp('"d' + (nb_days - 1) + '-h', 'g');
        var re_name_hours = new RegExp('name="horaires' + (nb_days - 1), 'g');

        var new_day_html = last_day.html().replace(re_id_hours, '"d' + nb_days + '-h')
            .replace('id="day' + (nb_days - 1) + '"', 'id="day' + nb_days + '"')
            .replace('for="day' + (nb_days - 1) + '"', 'for="day' + nb_days + '"')
            .replace(re_name_hours, 'name="horaires' + nb_days)
            .replace(/value="(.*?)"/g, 'value=""')
            .replace(/hours" title="(.*?)"/g, 'hours" title="" p')
            .replace('title="' + last_day_title + '"', 'title="' + last_day_title.substring(0, last_day_title.indexOf(' ')) + ' ' + (nb_days + 1) + '"');

        last_day.after('<fieldset>' + new_day_html + '</fieldset>');
        $('#day' + (nb_days)).focus();
        $removeaday_and_copyhours.removeClass('disabled');
    });

    // Button "Remove a day"

    $('#remove-a-day').on('click', function () {
        $selected_days.find('fieldset:last').remove();
        var nb_days = $selected_days.find('fieldset').length;
        $('#day' + (nb_days - 1)).focus();
        if (nb_days == 1) {
            $removeaday_and_copyhours.addClass('disabled');
        }
        submitDaysAvalaible();
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

    $(document).on('keyup, change', '.hours, #selected-days fieldset legend input', function () {
        submitDaysAvalaible();
    });
    submitDaysAvalaible();

    // 2 days and you can remove a day or copy hours

    if ($selected_days.find('fieldset').length > 1) {
        $removeaday_and_copyhours.removeClass('disabled');
    }
});