$(document).ready(function() {
    var lang = $('html').attr('lang');

    // Datepicker
    $('.input-group.date').datepicker({
        format: "dd/mm/yyyy",
        todayBtn: "linked",
        orientation: "top left",
        autoclose: true,
        language: lang
    });

    /**
     *  choix_date.php
     **/
    // start focus on first field day
    $('#day0').focus();

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
        var last_hour_html = last_hour.html();

        // RegEx for multiple replace
        var re_label = new RegExp(last_hour_label, 'g');
        var re_id = new RegExp('"d'+di+'-h'+hj+'"', 'g');

        // HTML code of the new hour
        var new_hour_html =
            '<div class="col-md-2">'+
                last_hour_html.replace(re_label, hour_text+' '+(hj+2))
                              .replace(re_id,'"d'+di+'-h'+(hj+1)+'"')
                              .replace(/value="(.*)" n/g, 'value="" n')+
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
    });

    // Button "Add a day"
    $('#add-a-day').on('click', function() {
        var nb_days = $('#selected-days fieldset').length;
        var last_day = $('#selected-days fieldset:last');

        var new_day = last_day.html();
        var re_id_hours = new RegExp('"d'+(nb_days-1)+'-h', 'g');
        var re_id_day = new RegExp('id="day'+(nb_days-1)+'"', 'g');
        var re_name_day = new RegExp('name="horaires'+(nb_days-1), 'g');

        var new_day_html = new_day.replace(re_id_hours, '"d'+nb_days+'-h')
                                  .replace(re_id_day, 'id="day'+nb_days+'"')
                                  .replace(re_name_day, 'name="horaires'+nb_days)
                                  .replace(/value="(.*)" s/g, 'value="" s')
                                  .replace(/hours" title="(.*)" p/g, 'hours" title="" p');

        last_day.after('<fieldset>'+new_day_html+'</fieldset>');
        $('#day'+(nb_days)).focus();
        $('#remove-a-day, #copyhours').removeClass('disabled');

        // Repeat datepicker init (junk code but it works for added days)
        $('.input-group.date').datepicker({
            format: "dd/mm/yyyy",
            todayBtn: "linked",
            orientation: "top left",
            autoclose: true,
            language: lang
        });

    });

    // Button "Remove a day"
    $('#remove-a-day').on('click', function() {
        var nb_days = $('#selected-days fieldset').length;

        $('#selected-days fieldset:last').remove();
        $('#day'+(nb_days-1)).focus();
        if ( nb_days == 1) {
            $('#remove-a-day, #copyhours').addClass('disabled');
        };

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

    // 1 day and 2 hours or 2 days and you can submit
    function SubmitDaysAvalaible() {
        var nb_filled_days = 0;
        var nb_filled_hours = 0;

        $('#selected-days fieldset legend input').each(function() {
            if($(this).val()!='') {
                nb_filled_days++;
            }
        });
        $('#selected-days .hours').each(function() {
            if($(this).val()!='') {
                nb_filled_hours++;
            }
        });

        if (nb_filled_days>1) {
            $('button[name="choixheures"]').removeClass('disabled');
        } else if (nb_filled_hours>1 && nb_filled_days==1)  {
            $('button[name="choixheures"]').removeClass('disabled');
        } else {
            $('button[name="choixheures"]').addClass('disabled');
        }
    }

    $(document).on('change','.hours, #selected-days fieldset legend input', function() {
        SubmitDaysAvalaible()
    });
    SubmitDaysAvalaible();

    // 2 days and you can remove a day or copy hours
    if($('#selected-days fieldset').length>1) {
        $('#remove-a-day, #copyhours').removeClass('disabled');
    }

    /**
     *  choix_autre.php
     **/
    // start focus on first field choice
    $('#choice0').focus();

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
                                        .replace(/value="(.*)" i/g, 'value="" i');

        last_choice.after('<div class="form-group choice-field">'+new_choice_html+'</div>');
        $('#choice'+nb_choices).focus();
        $('#remove-a-choice').removeClass('disabled');

    });

    // Button "Remove a choice"
    $('#remove-a-choice').on('click', function() {
        var nb_choices = $('.choice-field').length;

        $('.choice-field:last').remove();
        $('#choice'+(nb_choices-2)).focus();
        if (nb_choices == 3) {
            $('#remove-a-choice, button[name="fin_sondage_autre"]').addClass('disabled');
        };

    });

    // 2 choices filled and you can submit
    function SubmitChoicesAvalaible() {
        var nb_filled_choices = 0;
        $('.choice-field input').each(function() {
            if($(this).val()!='') {
                nb_filled_choices++;
            }
        });
        if(nb_filled_choices>1) {
            $('button[name="fin_sondage_autre"]').removeClass('disabled');
        } else {
            $('button[name="fin_sondage_autre"]').addClass('disabled');
        }
    }

    $(document).on('change','.choice-field input', function() {
        SubmitChoicesAvalaible()
    });
    SubmitChoicesAvalaible();

});
