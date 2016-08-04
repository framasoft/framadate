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

    /**
     * Error check when submitting form
     */
    $("#formulaire").submit(function (event) {
        var isHidden = $("#hidden").prop('checked');
        var isOptionAllUserCanModifyEverything = $("#editableByAll").is(":checked");

        if (isHidden && isOptionAllUserCanModifyEverything) {
            event.preventDefault();
            $("#hiddenWithBadEditionModeError").removeClass("hidden");
        } else {
            $("#hiddenWithBadEditionModeError").addClass("hidden");
        }
    });

    /**
     * Enable/Disable custom id options
     */
    var $pollId = $("#poll_id");
    var $customizeId = $("#customize_id");

    // Init checkbox + input
    if (($pollId.val() || $pollId.attr('value') || "").length > 0) {
        $customizeId.attr('checked', 'checked');
        $pollId.removeAttr("disabled");
    }
    // Listen for checkbox changes
    $customizeId.change(function () {
        if ($(this).prop("checked")) {
            $pollId
                .removeAttr("disabled")
                .val($pollId.attr("tmp") || $pollId.attr('value'));
        } else {
            $pollId
                .attr("disabled", "disabled")
                .attr("tmp", $pollId.val())
                .val("");
        }
    });

    /**
     * Hide/Show password options
     */
    $("#use_password").change(function(){
        if ($(this).prop("checked")) {
            $("#password_options").removeClass("hidden");
        } else {
            $("#password_options").addClass("hidden");
        }
    });

    // Check cookies are enabled too
    var cookieEnabled = function () {
        var cookieEnabled = navigator.cookieEnabled;

        // if not IE4+ nor NS6+
        if (!cookieEnabled && typeof navigator.cookieEnabled === "undefined") {
            document.cookie = "testcookie";
            cookieEnabled = document.cookie.indexOf("testcookie") != -1;
        }

        return cookieEnabled;
    };

    if (cookieEnabled()) {
        // Show the form block
        document.getElementById("form-block").setAttribute("style", "");
    } else {
        // Show the warning about cookies
        document.getElementById("cookie-warning").setAttribute("style", "");
    }

    var wrapper = new MDEWrapper($('#poll_comments')[0], $('#rich-editor-button'), $('#simple-editor-button'));
    if ($('#rich-editor-button').hasClass('active')) {
        wrapper.enable();
    }

});