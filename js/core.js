$(document).ready(function() {
    var lang = $('html').attr('lang');

    // Datepicker
    var framadatepicker = function() {
        $('.input-group.date').datepicker({
            format: "dd/mm/yyyy",
            todayBtn: "linked",
            orientation: "top left",
            autoclose: true,
            language: lang,
            todayHighlight: true,
            beforeShowDay: function (date){
                var $selected_days = new Array();
                $('#selected-days input[id^="day"]').each(function() {
                    if($(this).val()!='') {
                        $selected_days.push($(this).val());
                    }
                });
                for(i = 0; i < $selected_days.length; i++){
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


    var datepickerfocus = false; // a11y : datepicker not display on focus until there is one click on the button

    $(document).on('click','.input-group.date .input-group-addon', function() {
        datepickerfocus = true;
        // Re-init datepicker config before displaying
        $(this).parent().datepicker(framadatepicker());
        $(this).parent().datepicker('show');

        // Trick to refresh calendar
        $('.datepicker-days .prev').trigger('click');
        $('.datepicker-days .next').trigger('click');
        // .active must be clicable in order to unfill the form
        $('.datepicker-days .active').removeClass('disabled');
    });

    $(document).on('focus','.input-group.date input', function() {
        if(datepickerfocus) {
            $(this).parent('.input-group.date').datepicker(framadatepicker());
            $(this).parent('.input-group.date').datepicker('show');
        }
    });
    /**
     *  choix_date.php
     **/
    // Button "Remove all hours"
    $(document).on('click','#resethours', function() {
        $('#selected-days fieldset').each(function() {
            $(this).find('.hours:gt(2)').parent().remove();
        });
        $('#d0-h0').focus();
        $('#selected-days fieldset .hours').attr('value','');
    });

    // Button "Remove all days"
    $('#resetdays').on('click', function() {
        $('#selected-days fieldset:gt(0)').remove();
        $('#day0').focus();
        $('#remove-a-day, #copyhours').addClass('disabled');
    });

    // Button "Copy hours of the first day"
    $('#copyhours').on('click', function() {
        var first_day_hours = $('#selected-days fieldset:eq(0) .hours').map(function() {
            return $(this).val();
        });

        $('#selected-days fieldset:gt(0)').each(function() {
            for ($i = 0; $i < first_day_hours.length; $i++) {
                $(this).find('.hours:eq('+$i+')').val(first_day_hours[$i]); // fill hours
            }
        });
        $('#d0-h0').focus();
    });

    // Buttons "Add an hour"
    $(document).on('click','.add-an-hour', function() {
        var last_hour = $(this).parent('div').parent('div').prev();

        // for and id
        var di_hj = last_hour.children('.hours').attr('id').split('-');
        var di = parseInt(di_hj[0].replace('d','')); var hj = parseInt(di_hj[1].replace('h',''));

        // label, title and placeholder
        var last_hour_label = last_hour.children('.hours').attr('placeholder');
        var hour_text = last_hour_label.substring(0, last_hour_label.indexOf(' '));

        // RegEx for multiple replace
        var re_label = new RegExp(last_hour_label, 'g');
        var re_id = new RegExp('"d'+di+'-h'+hj+'"', 'g');

        // HTML code of the new hour
        var new_hour_html =
            '<div class="col-sm-2">'+
                last_hour.html().replace(re_label, hour_text+' '+(hj+2))
                              .replace(re_id,'"d'+di+'-h'+(hj+1)+'"')
                              .replace(/value="(.*?)"/g, 'value=""')+
            '</div>';

        // After 11 + button is disable
        if (hj<10) {
            last_hour.after(new_hour_html);
            $('#d'+di+'-h'+(hj+1)).focus();
            $(this).prev('.remove-an-hour').removeClass('disabled');
            if (hj==9) {
                $(this).addClass('disabled');
            }
        };

    });

    // Buttons "Remove an hour"
    $(document).on('click', '.remove-an-hour', function() {
        var last_hour = $(this).parent('div').parent('div').prev();
        // for and id
        var di_hj = last_hour.children('.hours').attr('id').split('-');
        var di = parseInt(di_hj[0].replace('d','')); var hj = parseInt(di_hj[1].replace('h',''));

        // The first hour must not be removed
        if (hj>0) {
            last_hour.remove();
            $('#d'+di+'-h'+(hj-1)).focus();
            $(this).next('.add-an-hour').removeClass('disabled');
            if (hj==1) {
                $(this).addClass('disabled');
            }
        };
        SubmitDaysAvalaible();
    });

    // Button "Add a day"
    $('#add-a-day').on('click', function() {
        var nb_days = $('#selected-days fieldset').length;
        var last_day = $('#selected-days fieldset:last');
        var last_day_title = last_day.find('legend input').attr('title');

        var re_id_hours = new RegExp('"d'+(nb_days-1)+'-h', 'g');
        var re_name_hours = new RegExp('name="horaires'+(nb_days-1), 'g');

        var new_day_html = last_day.html().replace(re_id_hours, '"d'+nb_days+'-h')
                                  .replace('id="day'+(nb_days-1)+'"', 'id="day'+nb_days+'"')
                                  .replace('for="day'+(nb_days-1)+'"', 'for="day'+nb_days+'"')
                                  .replace(re_name_hours, 'name="horaires'+nb_days)
                                  .replace(/value="(.*?)"/g, 'value=""')
                                  .replace(/hours" title="(.*?)"/g, 'hours" title="" p')
                                  .replace('title="'+last_day_title+'"', 'title="'+last_day_title.substring(0, last_day_title.indexOf(' '))+' '+(nb_days+1)+'"');

        last_day.after('<fieldset>'+new_day_html+'</fieldset>');
        $('#day'+(nb_days)).focus();
        $('#remove-a-day, #copyhours').removeClass('disabled');
    });

    // Button "Remove a day"
    $('#remove-a-day').on('click', function() {
        $('#selected-days fieldset:last').remove();
        var nb_days = $('#selected-days fieldset').length;
        $('#day'+(nb_days-1)).focus();
        if ( nb_days == 1) {
            $('#remove-a-day, #copyhours').addClass('disabled');
        };
        SubmitDaysAvalaible();
    });

    // Title update on hours and buttons -/+ hours
    $(document).on('change','#selected-days legend input', function() {
        $('#selected-days .hours').each(function () {
            $(this).attr('title', $(this).parents('fieldset').find('legend input').val()+' - '+$(this).attr('placeholder'));
        });
        $('#selected-days .add-an-hour, #selected-days .remove-an-hour').each(function () {
            var old_title = $(this).attr('title');

            if(old_title.indexOf('-')>0) {
                old_title = old_title.substring(old_title.indexOf('-')+2,old_title.length);
            }
            $(this).attr('title', $(this).parents('fieldset').find('legend input').val()+' - '+old_title);
        });
    });

    // 1 day filled and you can submit
    function SubmitDaysAvalaible() {
        var nb_filled_days = 0;

        $('#selected-days fieldset legend input').each(function() {
            if($(this).val()!='') {
                nb_filled_days++;
            }
        });

        if (nb_filled_days>0) {
            $('button[name="choixheures"]').removeClass('disabled');
        } else {
            $('button[name="choixheures"]').addClass('disabled');
        }
    }

    $(document).on('keyup, change','.hours, #selected-days fieldset legend input', function() {
        SubmitDaysAvalaible();
    });
    SubmitDaysAvalaible();

    // 1 days and you can remove a day or copy hours
    if($('#selected-days fieldset').length>0) {
        $('#remove-a-day, #copyhours').removeClass('disabled');
    }

    /**
     *  choix_autre.php
     **/
    // Button "Add a choice"
    $('#add-a-choice').on('click', function() {
        var nb_choices = $('.choice-field').length;
        var last_choice = $('.choice-field:last');

        var new_choice = last_choice.html();

        // label
        var last_choice_label = last_choice.children('label').text();
        var choice_text = last_choice_label.substring(0, last_choice_label.indexOf(' '));

        // for and id
        var re_id_choice = new RegExp('"choice'+(nb_choices-1)+'"', 'g');

        var last_choice_label = last_choice.children('label').text();
        var new_choice_html = new_choice.replace(re_id_choice, '"choice'+nb_choices+'"')
                                        .replace(last_choice_label, choice_text+' '+(nb_choices+1))
                                        .replace(/value="(.*?)"/g, 'value=""');

        last_choice.after('<div class="form-group choice-field">'+new_choice_html+'</div>');
        $('#choice'+nb_choices).focus();
        $('#remove-a-choice').removeClass('disabled');

    });

    // Button "Remove a choice"
    $('#remove-a-choice').on('click', function() {
        $('.choice-field:last').remove();
        var nb_choices = $('.choice-field').length;
        $('#choice'+(nb_choices-1)).focus();
        if (nb_choices == 1) {
            $('#remove-a-choice').addClass('disabled');
        };
        SubmitChoicesAvalaible();
    });

    // 1 choice filled and you can submit
    function SubmitChoicesAvalaible() {
        var nb_filled_choices = 0;
        $('.choice-field input').each(function() {
            if($(this).val()!='') {
                nb_filled_choices++;
            }
        });
        if(nb_filled_choices>0) {
            $('button[name="fin_sondage_autre"]').removeClass('disabled');
        } else {
            $('button[name="fin_sondage_autre"]').addClass('disabled');
        }
    }

    $(document).on('keyup, change','.choice-field input', function() {
        SubmitChoicesAvalaible();
    });
    SubmitChoicesAvalaible();

    $(document).on('click', '.md-a-img', function() {
        $('#md-a-imgModal').modal('show');
        $('#md-a-imgModal .btn-primary').attr('value',$(this).prev().attr('id'));
        $('#md-a-imgModalLabel').text($(this).attr('title'));
    });
    $('#md-a-imgModal .btn-primary').on('click', function() {
        if($('#md-img').val()!='' && $('#md-a').val()!='') {
            $('#'+$(this).val()).val('[!['+$('#md-text').val()+']('+$('#md-img').val()+')]('+$('#md-a').val()+')');
        } else if ($('#md-img').val()!='') {
            $('#'+$(this).val()).val('!['+$('#md-text').val()+']('+$('#md-img').val()+')');
        } else if ($('#md-a').val()!='') {
            $('#'+$(this).val()).val('['+$('#md-text').val()+']('+$('#md-a').val()+')');
        } else {
            $('#'+$(this).val()).val($('#md-text').val());
        }
        $('#md-a-imgModal').modal('hide');
        $('#md-img').val(''); $('#md-a').val('');$('#md-text').val('');
        SubmitChoicesAvalaible();
    });



    /**
     *  adminstuds.php
     **/

    $('#title-form .btn-edit').on('click', function() {
        $('#title-form h3').hide();
        $('.js-title').removeClass("hidden");
        $('.js-title input').focus();
        return false;
    });

    $('#title-form .btn-cancel').on('click', function() {
        $('#title-form h3').show();
        $('#title-form .js-title').addClass("hidden");
        $('#title-form .btn-edit').focus();
        return false;
    });

    $('#email-form .btn-edit').on('click', function() {
        $('#email-form p').hide();
        $('#email-form .js-email').removeClass("hidden");
        $('.js-email input').focus();
        return false;
    });

    $('#email-form .btn-cancel').on('click', function() {
        $('#email-form p').show();
        $('#email-form .js-email').addClass("hidden");
        $('#email-form .btn-edit').focus();
        return false;
    });

    $('#description-form .btn-edit').on('click', function() {
        $('#description-form .well').hide();
        $('#description-form .js-desc').removeClass("hidden");
        $('.js-desc textarea').focus();
        return false;
    });

    $('#description-form .btn-cancel').on('click', function() {
        $('#description-form .well').show();
        $('#description-form .js-desc').addClass("hidden");
        $('.js-desc .btn-edit').focus();
        return false;
    });

    $('#poll-rules-form .btn-edit').on('click', function() {
        $('#poll-rules-form p').hide();
        $('#poll-rules-form .js-poll-rules').removeClass("hidden");
        $('.js-poll-rules select').focus();
        return false;
    });

    $('#poll-rules-form .btn-cancel').on('click', function() {
        $('#poll-rules-form p').show();
        $('#poll-rules-form .js-poll-rules').addClass("hidden");
        $('.js-poll-rules .btn-edit').focus();
        return false;
    });

    // Horizontal scroll buttons
    if($('.results').width() > $('.container').width()) {
        $('.scroll-buttons').removeClass('hidden');
    }

    var $scroll_page = 1;
    var $scroll_scale = $('#tableContainer').width()*2/3;

    $('.scroll-left').addClass('disabled');

    $('.scroll-left').click(function(){
        $('.scroll-right').removeClass('disabled');
        $( "#tableContainer" ).animate({
            scrollLeft: $scroll_scale*($scroll_page-1)
        }, 1000);
        if($scroll_page == 1) {
            $(this).addClass('disabled');
        } else {
            $scroll_page = $scroll_page-1;
        }
        return false;
    });
    $('.scroll-right').click(function(){
        $('.scroll-left').removeClass('disabled');
        $( "#tableContainer" ).animate({
            scrollLeft: $scroll_scale*($scroll_page)
        }, 1000);

        if($scroll_scale*($scroll_page+1) > $( ".results" ).width()) {
            $(this).addClass('disabled');
        } else {
            $scroll_page++;
        }
        return false;
    });

});

// Vote form moving to the top or to the bottom
$(window).scroll(function() {
    var $table_offset = $('.results thead').offset();
    if(($table_offset == undefined || $(window).scrollTop() > $table_offset.top+150) && ($('table.results').height()-150 > $(window).height())) {
        $('#addition').before($('#vote-form'));
        $('#tableContainer').after($('.scroll-buttons'));
    } else {
        $('.results tbody').prepend($('#vote-form'));
        $('#tableContainer').before($('.scroll-buttons'));
    }
});
