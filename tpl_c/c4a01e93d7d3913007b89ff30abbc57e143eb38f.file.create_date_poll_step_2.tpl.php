<?php /* Smarty version Smarty-3.1.21, created on 2017-11-02 17:35:17
         compiled from "/var/www/framadate//tpl/create_date_poll_step_2.tpl" */ ?>
<?php /*%%SmartyHeaderCode:207056582059fb4945f3f0f7-78034827%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c4a01e93d7d3913007b89ff30abbc57e143eb38f' => 
    array (
      0 => '/var/www/framadate//tpl/create_date_poll_step_2.tpl',
      1 => 1506522320,
      2 => 'file',
    ),
    'cece18edb8bb8323539cb82888af012e22be1acf' => 
    array (
      0 => '/var/www/framadate//tpl/page.tpl',
      1 => 1506522320,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '207056582059fb4945f3f0f7-78034827',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'locale' => 0,
    'title' => 0,
    'APPLICATION_NAME' => 0,
    'use_nav_js' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.21',
  'unifunc' => 'content_59fb4946073579_08257031',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59fb4946073579_08257031')) {function content_59fb4946073579_08257031($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/var/www/framadate/vendor/smarty/smarty/libs/plugins/modifier.date_format.php';
?><!DOCTYPE html>
<html lang="<?php echo $_smarty_tpl->tpl_vars['locale']->value;?>
">
<head>
    <meta charset="utf-8">

    <?php if (!empty($_smarty_tpl->tpl_vars['title']->value)) {?>
        <title><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['title']->value);?>
 - <?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['APPLICATION_NAME']->value);?>
</title>
    <?php } else { ?>
        <title><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['APPLICATION_NAME']->value);?>
</title>
    <?php }?>

    <link rel="stylesheet" href="<?php echo smarty_modifier_resource('css/bootstrap.min.css');?>
">
    <link rel="stylesheet" href="<?php echo smarty_modifier_resource('css/datepicker3.css');?>
">
    <link rel="stylesheet" href="<?php echo smarty_modifier_resource('css/style.css');?>
">
    <link rel="stylesheet" href="<?php echo smarty_modifier_resource('css/frama.css');?>
">
    <link rel="stylesheet" href="<?php echo smarty_modifier_resource('css/print.css');?>
" media="print">
    <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo smarty_modifier_resource('js/jquery-1.11.1.min.js');?>
"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo smarty_modifier_resource('js/bootstrap.min.js');?>
"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo smarty_modifier_resource('js/bootstrap-datepicker.js');?>
"><?php echo '</script'; ?>
>
    <?php if ("en"!=$_smarty_tpl->tpl_vars['locale']->value) {?>
    <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo smarty_modifier_resource("js/locales/bootstrap-datepicker.".((string)$_smarty_tpl->tpl_vars['locale']->value).".js");?>
"><?php echo '</script'; ?>
>
    <?php }?>
    <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo smarty_modifier_resource('js/core.js');?>
"><?php echo '</script'; ?>
>

    
    <?php echo '<script'; ?>
 type="text/javascript">
        window.date_formats = {
            DATE: '<?php echo __('Date','DATE');?>
',
            DATEPICKER: '<?php echo __('Date','datepicker');?>
'
        };
    <?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo smarty_modifier_resource('js/app/framadatepicker.js');?>
"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo smarty_modifier_resource('js/app/date_poll.js');?>
"><?php echo '</script'; ?>
>


</head>
<body>
<?php if ($_smarty_tpl->tpl_vars['use_nav_js']->value) {?>
    <?php echo '<script'; ?>
 src="https://framasoft.org/nav/nav.js" type="text/javascript"><?php echo '</script'; ?>
>
<?php }?>
<div class="container ombre">

<?php echo $_smarty_tpl->getSubTemplate ('header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>



    <form name="formulaire" action="" method="POST" class="form-horizontal" role="form">
        <div class="row" id="selected-days">
            <div class="col-md-10 col-md-offset-1">
                <h3><?php echo __('Step 2 date','Choose the dates of your poll');?>
</h3>

                <?php if ($_smarty_tpl->tpl_vars['error']->value!=null) {?>
                <div class="alert alert-danger">
                    <p><?php echo $_smarty_tpl->tpl_vars['error']->value;?>
</p>
                </div>
                <?php }?>

                <div class="alert alert-info">
                    <p><?php echo __('Step 2 date','To schedule an event you need to propose at least two choices (two hours for one day or two days).');?>
</p>

                    <p><?php echo __('Step 2 date','You can add or remove additionnal days and hours with the buttons');?>

                        <span class="glyphicon glyphicon-minus text-info"></span>
                        <span class="sr-only"><?php echo __('Generic','Remove');?>
</span>
                        <span class="glyphicon glyphicon-plus text-success"></span>
                        <span class="sr-only"><?php echo __('Generic','Add');?>
</span>
                    </p>

                    <p><?php echo __('Step 2 date','For each selected day, you can choose, or not, meeting hours (e.g.: "8h", "8:30", "8h-10h", "evening", etc.)');?>
</p>
                </div>

                <div id="days_container">
                    <?php  $_smarty_tpl->tpl_vars['choice'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['choice']->_loop = false;
 $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['choices']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['choice']->key => $_smarty_tpl->tpl_vars['choice']->value) {
$_smarty_tpl->tpl_vars['choice']->_loop = true;
 $_smarty_tpl->tpl_vars['i']->value = $_smarty_tpl->tpl_vars['choice']->key;
?>
                        <?php if ($_smarty_tpl->tpl_vars['choice']->value->getName()) {?>
                            <?php $_smarty_tpl->tpl_vars['day_value'] = new Smarty_variable(smarty_modifier_date_format($_smarty_tpl->tpl_vars['choice']->value->getName(),$_smarty_tpl->tpl_vars['date_format']->value['txt_date']), null, 0);?>
                        <?php } else { ?>
                            <?php $_smarty_tpl->tpl_vars['day_value'] = new Smarty_variable('', null, 0);?>
                        <?php }?>
                        <fieldset>
                            <div class="form-group">
                                <legend>
                                    <label class="sr-only" for="day<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
"><?php echo __('Generic','Day');?>
 <?php echo $_smarty_tpl->tpl_vars['i']->value+1;?>
</label>

                                    <div class="col-xs-10 col-sm-11">
                                        <div class="input-group date">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar text-info"></i></span>
                                            <input type="text" class="form-control" id="day<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
" title="<?php echo __('Generic','Day');?>
 <?php echo $_smarty_tpl->tpl_vars['i']->value+1;?>
"
                                                   data-date-format="<?php echo __('Date','dd/mm/yyyy');?>
" aria-describedby="dateformat<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
" name="days[]" value="<?php echo $_smarty_tpl->tpl_vars['day_value']->value;?>
"
                                                   size="10" maxlength="10" placeholder="<?php echo __('Date','dd/mm/yyyy');?>
" autocomplete="nope"/>
                                        </div>
                                    </div>
                                    <div class="col-xs-2 col-sm-1">
                                        <button type="button" title="<?php echo __('Step 2 date','Remove this day');?>
" class="remove-day btn btn-sm btn-link">
                                            <span class="glyphicon glyphicon-remove text-danger"></span>
                                            <span class="sr-only"><?php echo __('Step 2 date','Remove this day');?>
</span>
                                        </button>
                                    </div>

                                    <span id="dateformat<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
" class="sr-only">(<?php echo __('Date','dd/mm/yyyy');?>
)</span>
                                </legend>

                                <?php  $_smarty_tpl->tpl_vars['slot'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['slot']->_loop = false;
 $_smarty_tpl->tpl_vars['j'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['choice']->value->getSlots(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['slot']->key => $_smarty_tpl->tpl_vars['slot']->value) {
$_smarty_tpl->tpl_vars['slot']->_loop = true;
 $_smarty_tpl->tpl_vars['j']->value = $_smarty_tpl->tpl_vars['slot']->key;
?>
                                    <div class="col-sm-2">
                                        <label for="d<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
-h<?php echo $_smarty_tpl->tpl_vars['j']->value;?>
" class="sr-only control-label"><?php echo __('Generic','Time');?>
 <?php echo $_smarty_tpl->tpl_vars['j']->value+1;?>
</label>
                                        <input type="text" class="form-control hours" title="<?php echo $_smarty_tpl->tpl_vars['day_value']->value;?>
 - <?php echo __('Generic','Time');?>
 <?php echo $_smarty_tpl->tpl_vars['j']->value+1;?>
"
                                               placeholder="<?php echo __('Generic','Time');?>
 <?php echo $_smarty_tpl->tpl_vars['j']->value+1;?>
" id="d<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
-h<?php echo $_smarty_tpl->tpl_vars['j']->value;?>
" name="horaires<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
[]" value="<?php echo $_smarty_tpl->tpl_vars['slot']->value;?>
"/>
                                    </div>
                                <?php } ?>

                                <div class="col-sm-2">
                                    <div class="btn-group btn-group-xs" style="margin-top: 5px;">
                                        <button type="button" title="<?php echo __('Step 2 date','Remove an hour');?>
" class="remove-an-hour btn btn-default">
                                            <span class="glyphicon glyphicon-minus text-info"></span>
                                            <span class="sr-only"><?php echo __('Step 2 date','Remove an hour');?>
</span>
                                        </button>
                                        <button type="button" title="<?php echo __('Step 2 date','Add an hour');?>
" class="add-an-hour btn btn-default">
                                            <span class="glyphicon glyphicon-plus text-success"></span>
                                            <span class="sr-only"><?php echo __('Step 2 date','Add an hour');?>
</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    <?php } ?>
                </div>


                <div class="col-md-4">
                    <button type="button" id="copyhours" class="btn btn-default disabled" title="<?php echo __('Step 2 date','Copy hours of the first day');?>
"><span
                                class="glyphicon glyphicon-sort-by-attributes-alt text-info"></span><span
                                class="sr-only"><?php echo __('Step 2 date','Copy hours of the first day');?>
</span></button>
                    <div class="btn-group btn-group">
                        <button type="button" id="remove-a-day" class="btn btn-default disabled" title="<?php echo __('Step 2 date','Remove a day');?>
"><span
                                    class="glyphicon glyphicon-minus text-info"></span><span class="sr-only"><?php echo __('Step 2 date','Remove a day');?>
</span></button>
                        <button type="button" id="add-a-day" class="btn btn-default" title="<?php echo __('Step 2 date','Add a day');?>
"><span
                                    class="glyphicon glyphicon-plus text-success"></span><span class="sr-only"><?php echo __('Step 2 date','Add a day');?>
</span></button>
                    </div>
                    <a href="" data-toggle="modal" data-target="#add_days" class="btn btn-default" title="<?php echo __('Date','Add range dates');?>
">
                        <span class="glyphicon glyphicon-plus text-success"></span>
                        <span class="glyphicon glyphicon-plus text-success"></span>
                        <span class="sr-only"><?php echo __('Date','Add range dates');?>
</span>
                    </a>
                </div>
                <div class="col-md-8 text-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            <span class="glyphicon glyphicon-remove text-danger"></span>
                            <?php echo __('Generic','Remove');?>
 <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a id="resetdays" href="javascript:void(0)"><?php echo __('Step 2 date','Remove all days');?>
</a></li>
                            <li><a id="resethours" href="javascript:void(0)"><?php echo __('Step 2 date','Remove all hours');?>
</a></li>
                        </ul>
                    </div>
                    <a class="btn btn-default" href="<?php echo $_smarty_tpl->tpl_vars['SERVER_URL']->value;?>
create_poll.php?type=date"
                       title="<?php echo __('Step 2','Back to step 1');?>
"><?php echo __('Generic','Back');?>
</a>
                    <button name="choixheures" value="<?php echo __('Generic','Next');?>
" type="submit" class="btn btn-success disabled"
                            title="<?php echo __('Step 2','Go to step 3');?>
"><?php echo __('Generic','Next');?>
</button>
                </div>
            </div>
        </div>
    </form>

    <div id="add_days" class="modal fade">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"><?php echo __('Date','Add range dates');?>
</h4>
                </div>
                <div class="modal-body row">
                    <div class="col-xs-12">
                        <div class="alert alert-info">
                            <?php echo __('Date','Max dates count');?>

                        </div>
                    </div>
                    <div class="col-xs-12">
                        <label for="range_start"><?php echo __('Date','Start date');?>
</label>
                        <div class="input-group date">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar text-info"></i></span>
                            <input type="text" class="form-control" id="range_start"
                                   data-date-format="<?php echo __('Date','dd/mm/yyyy');?>
" size="10" maxlength="10"
                                   placeholder="<?php echo __('Date','dd/mm/yyyy');?>
"/>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <label for="range_end"><?php echo __('Date','End date');?>
</label>
                        <div class="input-group date">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar text-info"></i></span>
                            <input type="text" class="form-control" id="range_end"
                                   data-date-format="<?php echo __('Date','dd/mm/yyyy');?>
" size="10" maxlength="10"
                                   placeholder="<?php echo __('Date','dd/mm/yyyy');?>
"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default"><?php echo __('Generic','Cancel');?>
</button>
                    <button id="interval_add" class="btn btn-success"><?php echo __('Generic','Add');?>
</button>
                </div>
            </div>
        </div>
    </div>


</main>
</div> <!-- .container -->
</body>
</html>
<?php }} ?>
