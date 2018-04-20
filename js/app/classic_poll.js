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
    $(document.formulaire).on('submit', function (e) {
        if (!isSubmitChoicesAvalaible()) {
            e.preventDefault();
            e.stopPropagation();
        }
    });
    
    var $next = $('button[name="fin_sondage_autre"]');
    var $removeAChoice = $('#remove-a-choice');
    var $addAChoice = $('#add-a-choice');

    var updateButtonState = function() {
        var $choiceFields = $('.choice-field');
        $removeAChoice.prop('disabled', function() { return $choiceFields.length <= 2; });
        $next.prop('disabled', !isSubmitChoicesAvalaible());
    }

    var isSubmitChoicesAvalaible = function () {
        return (countFilledChoices() >= 2);
    };

    var countFilledChoices = function() {
        var nb_filled_choices = 0;
        $('.choice-field input').each(function () {
            if ($(this).val() != '') {
                nb_filled_choices++;
            }
        });
        return nb_filled_choices;
    };

    $removeAChoice.on('click', function () {
        $('.choice-field:last').remove();
        updateButtonState();
    });

    $addAChoice.on('click', function () {
        var $choiceFields = $('.choice-field');
        var nb_choices = $choiceFields.length;
        var last_choice = $choiceFields.last();
    
        var new_choice = last_choice.html();
    
        // label
        var last_choice_label = last_choice.children('label').text();
        var choice_text = last_choice_label.substring(0, last_choice_label.indexOf(' '));
    
        // for and id
        var re_id_choice = new RegExp('"choice' + (nb_choices - 1) + '"', 'g');
    
        var new_choice_html = new_choice.replace(re_id_choice, '"choice' + nb_choices + '"')
            .replace(last_choice_label, choice_text + ' ' + (nb_choices + 1))
            .replace(/value="(.*?)"/g, 'value=""');
    
        last_choice.after('<div class="form-group choice-field row">' + new_choice_html + '</div>');
        $('#choice' + nb_choices).focus();
        updateButtonState()
    
    });

    $(document).on('keyup, change', '.choice-field input', function () {
        updateButtonState();
    });
    
    updateButtonState();

    // Button to build markdown from: link + image-url + text

    var md_a_imgModal = $('#md-a-imgModal');
    var md_text = $('#md-text');
    var md_img = $('#md-img');
    var md_val = $('#md-a');

    $('.md-a-img').on('click' , function () {
        md_a_imgModal.modal('show');
        md_a_imgModal.find('.btn-primary').attr('value', $(this).prev().attr('id'));
        $('#md-a-imgModalLabel').text($(this).attr('title'));
    });
    md_a_imgModal.find('.btn-primary').on('click', function () {
        var text = md_text.val();
        var img = md_img.val();
        var link = md_val.val();
        var element = $('#' + $(this).val());
        
        if (img != '' && link != '') {
            element.val('[![' + text + '](' + img + ')](' + link + ')');
        } else if (img != '') {
            element.val('![' + text + '](' + img + ')');
        } else if (link != '') {
            element.val('[' + (text?text:link) + '](' + link + ')');
        } else {
            element.val(text);
        }
        md_a_imgModal.modal('hide');
        md_img.val('');
        md_val.val('');
        md_text.val('');
        updateButtonState();
    });
});
