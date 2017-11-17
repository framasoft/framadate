<?php /* Smarty version Smarty-3.1.21, created on 2017-09-27 18:30:24
         compiled from "/var/www/framadate//tpl/create_classic_poll_step3.tpl" */ ?>
<?php /*%%SmartyHeaderCode:171841892659cbd22073e097-64512920%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c9c2302274d8615f1a3a123a09d522a51b60c5cf' => 
    array (
      0 => '/var/www/framadate//tpl/create_classic_poll_step3.tpl',
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
  'nocache_hash' => '171841892659cbd22073e097-64512920',
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
  'unifunc' => 'content_59cbd2207ecce9_11593041',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59cbd2207ecce9_11593041')) {function content_59cbd2207ecce9_11593041($_smarty_tpl) {?><!DOCTYPE html>
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


</head>
<body>
<?php if ($_smarty_tpl->tpl_vars['use_nav_js']->value) {?>
    <?php echo '<script'; ?>
 src="https://framasoft.org/nav/nav.js" type="text/javascript"><?php echo '</script'; ?>
>
<?php }?>
<div class="container ombre">

<?php echo $_smarty_tpl->getSubTemplate ('header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>



    <form name="formulaire" method="POST" class="form-horizontal" role="form">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="well summary">
                    <h4><?php echo __('Step 3','List of your choices');?>
</h4>
                    <?php echo $_smarty_tpl->tpl_vars['summary']->value;?>

                </div>
                <div class="alert alert-info">
                    <p><?php echo __('Step 3','Your poll will automatically be archived');?>
 <?php echo $_smarty_tpl->tpl_vars['default_poll_duration']->value;?>
 <?php echo __('Generic','days');?>
 <?php echo __('Step 3','after the last date of your poll.');?>

                        <br /><?php echo __('Step 3','You can set a closer archiving date for it.');?>
</p>
                    <div class="form-group">
                        <label for="enddate" class="col-sm-5 control-label"><?php echo __('Step 3','Archiving date:');?>
</label>
                        <div class="col-sm-6">
                            <div class="input-group date">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar text-info"></i></span>
                                <input type="text" class="form-control" id="enddate" data-date-format="<?php echo __('Date','dd/mm/yyyy');?>
" aria-describedby="dateformat" name="enddate" value="<?php echo $_smarty_tpl->tpl_vars['end_date_str']->value;?>
" size="10" maxlength="10" placeholder="<?php echo __('Date','dd/mm/yyyy');?>
" />
                            </div>
                        </div>
                        <span id="dateformat" class="sr-only"><?php echo __('Date','dd/mm/yyyy');?>
</span>
                    </div>
                </div>
                <div class="alert alert-warning">
                    <p><?php echo __('Step 3','Once you have confirmed the creation of your poll, you will be automatically redirected on the administration page of your poll.');?>
</p>
                    <?php if ($_smarty_tpl->tpl_vars['use_smtp']->value) {?>
                        <p><?php echo __('Step 3','Then, you will receive quickly two emails: one contening the link of your poll for sending it to the voters, the other contening the link to the administration page of your poll.');?>
</p>
                    <?php }?>
                </div>
                <p class="text-right">
                    <button class="btn btn-default" onclick="javascript:window.history.back();" title="<?php echo __('Step 3','Back to step 2');?>
"><?php echo __('Generic','Back');?>
</button>
                    <button name="confirmation" value="confirmation" type="submit" class="btn btn-success"><?php echo __('Step 3','Create the poll');?>
</button>
                </p>
            </div>
        </div>
    </form>


</main>
</div> <!-- .container -->
</body>
</html>
<?php }} ?>
