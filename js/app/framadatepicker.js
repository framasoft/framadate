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
    var before_show_day = function (date) {
        // Retrieve selected dates from text fields
        var selected_days = [];
        $('#selected-days').find('input[id^="day"]').each(function () {
            if ($(this).val() != '') {
                selected_days.push($(this).val());
            }
        });

        // Disable selected dates in DatePicker
        for (var i = 0; i < selected_days.length; i++) {
            var selected_date = selected_days[i].split('/');

            if (date.getFullYear() == selected_date[2] && (date.getMonth() + 1) == selected_date[1] && date.getDate() == selected_date[0]) {
                return {
                    classes: 'disabled selected'
                };
            }
        }
    };

    var init_datepicker = function () {
        $('.input-group.date').datepicker({
            format: window.date_formats.DATEPICKER || "dd/mm/yyyy",
            todayBtn: "linked",
            orientation: "top left",
            autoclose: true,
            language: lang,
            todayHighlight: true,
            beforeShowDay: before_show_day
        });
    };

    $(document).on('click', '.input-group.date .input-group-addon, .input-group.date input', function () {
        // Re-init datepicker config before displaying
        init_datepicker();

        if (isNaN($(this).parent().datepicker('getDate').getTime()))
        {

            var last_date = $('#selected-days').find('input[id^="day"]').filter(function() {
                return $(this).val() != '';
            }).last().val();

            if (last_date)
            {
                last_date = last_date.split('/');
                last_date = new Date(last_date[2], last_date[1] - 1, last_date[0]);

                while(before_show_day(last_date) != null)
                {
                    last_date.setDate(last_date.getDate() + 1);
                }

                // Set date as the next available day.
                $(this).parent().datepicker("setDate", last_date);
            }
        }
        $(this).parent().datepicker('show');

        // Trick to refresh calendar
        $('.datepicker-days .prev').trigger('click');
        $('.datepicker-days .next').trigger('click');
    });

    // Complete the date fields when use partialy fill it (eg: 15/01 could become 15/01/2016)

    var leftPad = function (text, pad) {
        return text ? pad.substring(0, pad.length - text.length) + text : text;
    };

    $(document).on('change', '.input-group.date input', function () {
        // Complete field if needed
        var val = $(this).val();
        var capture = /^([0-9]+)(?:\/([0-9]+))?$/.exec(val);

        if (capture) {
            var inputDay = leftPad(capture[1], "00"); // 5->05, 15->15
            var inputMonth = leftPad(capture[2], "00"); // 3->03, 11->11
            var inputDate = null;
            var now = new Date();

            if (inputMonth) {
                inputDate = new Date(now.getFullYear() + '-' + inputMonth + '-' + inputDay);

                // If new date is before now, add 1 year
                if (inputDate < now) {
                    inputDate.setFullYear(now.getFullYear() + 1);
                }
            } else {
                inputDate = new Date(now.getFullYear() + '-' + leftPad("" + (now.getMonth() + 1), "00") + '-' + inputDay);

                // If new date is before now, add 1 month
                if (inputDate < now) {
                    inputDate.setMonth(now.getMonth() + 1);
                }

            }

            $(this).val(inputDate.toLocaleFormat("%d/%m/%Y"));
        }
    });

});
