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

var init_datepicker = function() {
    $('.input-group.date').datepicker({
        format: "dd/mm/yyyy",
        todayBtn: "linked",
        orientation: "top left",
        autoclose: true,
        language: lang,
        todayHighlight: true,
        beforeShowDay: function (date){
            var $selected_days = [];
            $('#selected-days').find('input[id^="day"]').each(function() {
                if($(this).val()!='') {
                    $selected_days.push($(this).val());
                }
            });
            for(var i = 0; i < $selected_days.length; i++){
                var $selected_date = $selected_days[i].split('/');

                if (date.getFullYear() == $selected_date[2] && (date.getMonth()+1) == $selected_date[1] && date.getDate() == $selected_date[0]){
                    return {
                        classes: 'disabled selected'
                    };
                }
            }
        }
    });
};