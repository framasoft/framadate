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
(function () {

    // 2 choices filled and you can submit

    var submitChoicesAvailable = function () {
        var nb_filled_choices = 0;
        $('.choice-field input').each(function () {
            if ($(this).val() !== '') {
                nb_filled_choices++;
            }
        });
        if (nb_filled_choices >= 1) {
            $('button[name="fin_sondage_autre"]').removeClass('disabled');
            return true;
        } else {
            $('button[name="fin_sondage_autre"]').addClass('disabled');
            return false;
        }
    };

    // Handle form submission
    $('form[name="formulaire-classic"]').on('submit', (e) => {
        console.log("Submit form");
        console.log(e);
        if (!submitChoicesAvailable()) {
            e.preventDefault();
            e.stopPropagation();
        }
    });

    // Button "Add a choice"

    $('#add-a-choice').on('click', function () {
        var nb_choices = $('.choice-field').length;
        var last_choice = $('.choice-field:last');

        var new_choice = last_choice.html();

        // label
        var last_choice_label = last_choice.children('label').text();
        var choice_text = last_choice_label.substring(0, last_choice_label.indexOf(' '));

        // for and id
        var re_id_choice = new RegExp('"choice' + (nb_choices - 1) + '"', 'g');

        var new_choice_html = new_choice.replace(re_id_choice, '"choice' + nb_choices + '"')
            .replace(last_choice_label, choice_text + ' ' + (nb_choices + 1))
            .replace(/value="(.*?)"/g, 'value=""');

        last_choice.after('<div class="form-group choice-field">' + new_choice_html + '</div>');
        $('#choice' + nb_choices).focus();
        $('#remove-a-choice').removeClass('disabled');

    });

    // Button "Remove a choice"

    $('#remove-a-choice').on('click', function () {
        $('.choice-field:last').remove();
        var nb_choices = $('.choice-field').length;
        $('#choice' + (nb_choices - 1)).focus();
        if (nb_choices === 1) {
            $('#remove-a-choice').addClass('disabled');
        }
        submitChoicesAvailable();
    });

    $(document).on('keyup, change', '.choice-field input', () => {
        submitChoicesAvailable();
    });
    submitChoicesAvailable();

    // Button to build markdown from: link + image-url + text

    const md_a_imgModal = $('#md-a-imgModal');
    const md_text = $('#md-text');
    const md_img = $('#md-img');
    const md_val = $('#md-a');

    $(document).on('click', '.md-a-img', function () {
        md_a_imgModal.modal('show');
        md_a_imgModal.find('.btn-primary').attr('value', $(this).prev().attr('id'));
        $('#md-a-imgModalLabel').text($(this).attr('title'));
    });
    md_a_imgModal.find('.btn-primary').on('click', function () {
        const text = md_text.val();
        const img = md_img.val();
        const link = md_val.val();
        const element = $('#' + $(this).val());
        
        if (img !== '' && link !== '') {
            element.val('[![' + text + '](' + img + ')](' + link + ')');
        } else if (img !== '') {
            element.val('![' + text + '](' + img + ')');
        } else if (link !== '') {
            element.val('[' + (text?text:link) + '](' + link + ')');
        } else {
            element.val(text);
        }
        md_a_imgModal.modal('hide');
        md_img.val('');
        md_val.val('');
        md_text.val('');
        submitChoicesAvailable();
    });
})();
