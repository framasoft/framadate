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

import MDEWrapper from '../mde-wrapper';

$(document).ready(() => {

    /**
     * Error check when submitting form
     */
    $('form[name="poll"]').submit((event) => {
        const isHidden = $("#hidden").prop('checked');
        const isOptionAllUserCanModifyEverything = $("#editableByAll").is(":checked");

        if (isHidden && isOptionAllUserCanModifyEverything) {
            event.preventDefault();
            $("#hiddenWithBadEditionModeError").removeClass("hidden");
        } else {
            $("#hiddenWithBadEditionModeError").addClass("hidden");
        }
    });

    /**
     * Enable/Disable custom url options
     */
    function toggle_customized_url() {
        if ($("#poll_use_customized_url").prop("checked")) {
            $("#customized_url_options").removeClass("hidden");
        } else {
            $("#customized_url_options").addClass("hidden");
        }
    }

    /**
     * Enable/Disable ValueMax options
     */
    function toggle_use_value_max() {
        if ($("#poll_use_ValueMax").prop("checked")) {
            $("#ValueMax").removeClass("hidden");
        } else {
            $("#ValueMax").addClass("hidden");
        }
    }


    /**
     * Hide/Show password options
     */
    function toggle_use_password() {
        if ($("#poll_use_password").prop("checked")) {
            $("#password_options").removeClass("hidden");
        } else {
            $("#password_options").addClass("hidden");
        }
    }

    $("#poll_use_customized_url").change(toggle_customized_url);
    $("#poll_use_ValueMax").change(toggle_use_value_max);
    $("#poll_use_password").change(toggle_use_password);

    toggle_customized_url();
    toggle_use_value_max();
    toggle_use_password();

    // Check cookies are enabled too
    const cookieEnabled = () => {
        let cookieEnabled = navigator.cookieEnabled;

        // if not IE4+ nor NS6+
        if (!cookieEnabled && typeof navigator.cookieEnabled === "undefined") {
            document.cookie = "testcookie";
            cookieEnabled = document.cookie.indexOf("testcookie") !== -1;
        }

        return cookieEnabled;
    };

    if (document.getElementById("form-block")) {
        if (cookieEnabled()) {
            // Show the form block
            document.getElementById("form-block").setAttribute("style", "");
        } else {
            // Show the warning about cookies
            document.getElementById("cookie-warning").setAttribute("style", "");
        }
    }

    const rich_editor_button = document.getElementById('rich-editor-button');
    const wrapper = new MDEWrapper(document.getElementById('poll_description'), rich_editor_button, document.getElementById('simple-editor-button'));
    if ($(rich_editor_button).hasClass('active')) {
        wrapper.enable();
    }

});
