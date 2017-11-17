<?php /* Smarty version Smarty-3.1.21, created on 2017-09-27 17:17:39
         compiled from "/var/www/framadate//tpl/admin/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:161966892459cbc11397af19-15860214%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6bf505be8cd22c4cf4f81b621d5190ec8da2e622' => 
    array (
      0 => '/var/www/framadate//tpl/admin/index.tpl',
      1 => 1506522320,
      2 => 'file',
    ),
    'e40c3a8ce4b90927f1b1e38d12661e9857153019' => 
    array (
      0 => '/var/www/framadate//tpl/admin/admin_page.tpl',
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
  'nocache_hash' => '161966892459cbc11397af19-15860214',
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
  'unifunc' => 'content_59cbc1139a8ca7_68323162',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59cbc1139a8ca7_68323162')) {function content_59cbc1139a8ca7_68323162($_smarty_tpl) {?><!DOCTYPE html>
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

    

</head>
<body>
<?php if ($_smarty_tpl->tpl_vars['use_nav_js']->value) {?>
    <?php echo '<script'; ?>
 src="https://framasoft.org/nav/nav.js" type="text/javascript"><?php echo '</script'; ?>
>
<?php }?>
<div class="container ombre">

<?php echo $_smarty_tpl->getSubTemplate ('header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>



<div class="row">
    <div class="col-md-6 col-xs-12">
        <a href="./polls.php"><h2><?php echo __('Admin','Polls');?>
</h2></a>
    </div>
    <div class="col-md-6 col-xs-12">
        <a href="./migration.php"><h2><?php echo __('Admin','Migration');?>
</h2></a>
    </div>
    <div class="col-md-6 col-xs-12">
        <a href="./purge.php"><h2><?php echo __('Admin','Purge');?>
</h2></a>
    </div>
    <div class="col-md-6 col-xs-12">
        <a href="./check.php"><h2><?php echo __('Check','Installation checking');?>
</h2></a>
    </div>
    <?php if ($_smarty_tpl->tpl_vars['logsAreReadable']->value) {?>
        <div class="col-md-6 col-xs-12">
            <a href="./logs.php"><h2><?php echo __('Admin','Logs');?>
</h2></a>
        </div>
    <?php }?>
</div>


</main>
</div> <!-- .container -->
</body>
</html>
<?php }} ?>
