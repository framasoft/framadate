<?php /* Smarty version Smarty-3.1.21, created on 2017-09-27 17:16:48
         compiled from "/var/www/framadate//tpl/header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:136458898159cbc0e02e9ee3-12838139%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b09e21ab7f4f2910f61b5fa7a4e81e11a07e7489' => 
    array (
      0 => '/var/www/framadate//tpl/header.tpl',
      1 => 1506522320,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '136458898159cbc0e02e9ee3-12838139',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'langs' => 0,
    'lang_key' => 0,
    'locale' => 0,
    'lang_value' => 0,
    'SERVER_URL' => 0,
    'APPLICATION_NAME' => 0,
    'TITLE_IMAGE' => 0,
    'title' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.21',
  'unifunc' => 'content_59cbc0e02f6d06_23873049',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59cbc0e02f6d06_23873049')) {function content_59cbc0e02f6d06_23873049($_smarty_tpl) {?>    <header role="banner" class="clearfix">
    <?php if (count($_smarty_tpl->tpl_vars['langs']->value)>1) {?>
        <form method="post" action="" class="hidden-print">
            <div class="input-group input-group-sm pull-right col-xs-12 col-sm-2">
                <select name="lang" class="form-control" title="<?php echo __('Language selector','Select the language');?>
" >
                <?php  $_smarty_tpl->tpl_vars['lang_value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['lang_value']->_loop = false;
 $_smarty_tpl->tpl_vars['lang_key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['langs']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['lang_value']->key => $_smarty_tpl->tpl_vars['lang_value']->value) {
$_smarty_tpl->tpl_vars['lang_value']->_loop = true;
 $_smarty_tpl->tpl_vars['lang_key']->value = $_smarty_tpl->tpl_vars['lang_value']->key;
?>
                    <option lang="<?php echo substr($_smarty_tpl->tpl_vars['lang_key']->value,0,2);?>
" <?php if (substr($_smarty_tpl->tpl_vars['lang_key']->value,0,2)==$_smarty_tpl->tpl_vars['locale']->value) {?>selected<?php }?> value="<?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['lang_key']->value);?>
"><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['lang_value']->value);?>
</option>
                <?php } ?>
                </select>
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-default btn-sm" title="<?php echo __('Language selector','Change the language');?>
">OK</button>
                </span>
            </div>
        </form>
    <?php }?>

        <h1 class="row col-xs-12 col-sm-10">
            <a href="<?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['SERVER_URL']->value);?>
" title="<?php echo __('Generic','Home');?>
 - <?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['APPLICATION_NAME']->value);?>
" >
                <img src="<?php echo smarty_modifier_resource($_smarty_tpl->tpl_vars['TITLE_IMAGE']->value);?>
" alt="<?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['APPLICATION_NAME']->value);?>
" class="img-responsive"/>
            </a>
        </h1>
        <?php if (!empty($_smarty_tpl->tpl_vars['title']->value)) {?><h2 class="lead col-xs-12"><i><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['title']->value);?>
</i></h2><?php }?>
        <div class="trait col-xs-12" role="presentation"></div>
    </header>
    <main role="main">
<?php }} ?>