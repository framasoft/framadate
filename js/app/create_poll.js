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
     * Enable/Disable custom url options
     */
    $("#use_customized_url").change(function () {
        if ($(this).prop("checked")) {
            $("#customized_url_options").removeClass("hidden");
            // Check url pattern
            $('#customized_url').on('input', function() {
                var regex_url = /^[a-zA-Z0-9-]*$/
                if (! regex_url.test(this.value)) {
                    $(this).parent(".input-group").addClass("has-error");
                    $(this).attr("aria-invalid", "true");
                } else {
                    $(this).parent(".input-group").removeClass("has-error");
                    $(this).attr("aria-invalid", "false");
                }
            });
        } else {
            $("#customized_url_options").addClass("hidden");
        }
    });

 /**
     * Enable/Disable ValueMax options
     */
    $("#use_ValueMax").change(function () {
        if ($(this).prop("checked")) {
            $("#valueMaxWrapper").removeClass("hidden");
        } else {
            $("#valueMaxWrapper").addClass("hidden");
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

    /**
     * Hide/Show Warning collect_users_mail + editable by all
     */
    $("input[name='collect_users_mail']").change(function(){
        if (($("input[name='collect_users_mail']:checked").val() != 0) && ($("input[name='editable']:checked").val() == 1)) {
            $("#collect_warning").removeClass("hidden");
        } else {
            $("#collect_warning").addClass("hidden");
        }
    });

    $("input[name='editable']").change(function(){
        if ($("input[name='collect_users_mail']:checked").val() != 0 && $("input[name='editable']:checked").val() == 1) {
            $("#collect_warning").removeClass("hidden");
        } else {
            $("#collect_warning").addClass("hidden");
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
