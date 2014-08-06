$(document).ready(function() {
    var lang = $('html').attr('lang');

    // Datepicker
    $('#selected-days .input-group.date').datepicker({ // choix_date
        format: "dd/mm/yyyy", //"DD d MM yyyy" would be better
        todayBtn: "linked",
        orientation: "top left",
        autoclose: true,
        language: lang
    });
    $('.alert-info .input-group.date').datepicker({ // expiration date in choix_autre
        format: "dd/mm/yyyy",
        todayBtn: "linked",
        orientation: "top left",
        autoclose: true,
        language: lang
    });

    // Button "Remove all hours"
    $(document).on('click','#resethours', function() {
        $('#selected-days fieldset').each(function() {
            $(this).find('.hours:gt(2)').parent().remove();
        });
        $('#selected-days fieldset .hours').attr('value','');
    });

    // Button "Remove all days"
    $('#resetdays').on('click', function() {
        $('#selected-days fieldset:gt(0)').remove();
        $('#remove-a-day, #copyhours').addClass('disabled');
    });

    // Button "Copy hours of the first day"
    $('#copyhours').on('click', function() {
        var first_day_hours = $('#selected-days fieldset:eq(0) .hours').map(function() {
            return $(this).val();
        });

        $('#selected-days fieldset:gt(0)').each(function() {
            for ($i = 0; $i < first_day_hours.length; $i++) {
                if(first_day_hours[$i]!="") { // only copy not empty hours
                    if($(this).find('.hours:eq('+$i+')')==undefined) {
                        // addHour();
                    }
                    $(this).find('.hours:eq('+$i+')').val(first_day_hours[$i]); // fill hours
                }
            }
        });
    });

    // Buttons "Add an hour"
    $(document).on('click','.add-an-hour', function() {
        var last_hour = $(this).parent('div').parent('div').prev();

        // for and id
        var di_hj = last_hour.children('.hours').attr('id').split('-');
        var di = parseInt(di_hj[0].replace('d','')); var hj = parseInt(di_hj[1].replace('h',''));

        // label, title and placeholder
        var hour_text = last_hour.children('.hours').attr('placeholder').substring(0, last_hour.children('.hours').attr('placeholder').indexOf(' '));
        var last_hour_label = hour_text+' '+(hj+1);
        var last_hour_html = last_hour.html();

        // RegEx for multiple replace
        var re_label = new RegExp(last_hour_label, 'g');
        var re_id = new RegExp('"d'+di+'-h'+hj+'"', 'g');

        // HTML code of the new hour
        var new_hour_label = hour_text+' '+(hj+2);
        var new_hour_html =
            '<div class="col-md-2">'+
                last_hour_html.replace(re_label, new_hour_label)
                              .replace(re_id,'"d'+di+'-h'+(hj+1)+'"')
                              .replace(/value="(.*)" n/g, 'value="" n')+
            '</div>';

        // After 11 + button is disable
        if (hj<10) {
            last_hour.after(new_hour_html);
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

        var new_day = last_day.clone();
        var re_id_hours = new RegExp('"d'+(nb_days-1)+'-h', 'g');
        var re_id_day = new RegExp('id="day'+(nb_days-1)+'"', 'g');
        var re_name_day = new RegExp('name="horaires'+(nb_days-1), 'g');

        var new_day_html = new_day.html().replace(re_id_hours, '"d'+nb_days+'-h')
                                         .replace(re_id_day, 'id="day'+nb_days+'"')
                                         .replace(re_name_day, 'name="horaires'+nb_days)
                                         .replace(/value="(.*)" s/g, 'value="" s')
                                         .replace(/hours" title="(.*)" p/g, 'hours" title="" p');

        last_day.after('<fieldset>'+new_day_html+'</fieldset>');
        $('#remove-a-day, #copyhours').removeClass('disabled');

        // Repeat datepicker init (junk code but it works for added days)
        $('#selected-days .input-group.date').datepicker({
            format: "dd/mm/yyyy", //"DD d MM yyyy" would be better
            todayBtn: "linked",
            orientation: "top left",
            autoclose: true,
            language: lang
        });

    });

    // Button "Remove a day"
    $('#remove-a-day').on('click', function() {
        $('#selected-days fieldset:last').remove();
        if ($('#selected-days fieldset').length == 1) {
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

    if($('#selected-days fieldset').length>1) {
        $('#remove-a-day, #copyhours').removeClass('disabled');
    }


});
