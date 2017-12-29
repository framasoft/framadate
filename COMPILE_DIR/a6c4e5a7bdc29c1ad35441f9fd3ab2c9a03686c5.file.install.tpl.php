<?php /* Smarty version Smarty-3.1.21, created on 2017-12-28 18:45:19
         compiled from "/opt/lampp/htdocs/framadate//tpl/admin/install.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6337436915a452daf6119e9-02899397%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a6c4e5a7bdc29c1ad35441f9fd3ab2c9a03686c5' => 
    array (
      0 => '/opt/lampp/htdocs/framadate//tpl/admin/install.tpl',
      1 => 1514483059,
      2 => 'file',
    ),
    '3f7403047d85e050c91b61dcceeea00f187da60b' => 
    array (
      0 => '/opt/lampp/htdocs/framadate//tpl/admin/admin_page.tpl',
      1 => 1514478150,
      2 => 'file',
    ),
    '4b437822b6d425cb09496a011c7b188097e16f5c' => 
    array (
      0 => '/opt/lampp/htdocs/framadate//tpl/page.tpl',
      1 => 1514478150,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6337436915a452daf6119e9-02899397',
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
  'unifunc' => 'content_5a452daf69d976_75956208',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5a452daf69d976_75956208')) {function content_5a452daf69d976_75956208($_smarty_tpl) {?><!DOCTYPE html>
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

    <div class="col-md-12">
        <form action="" method="POST">

            <?php if ($_smarty_tpl->tpl_vars['error']->value) {?>
                <div id="result" class="alert alert-danger"><?php echo $_smarty_tpl->tpl_vars['error']->value;?>
</div>
            <?php }?>

            <fieldset>
                <legend><?php echo __('Installation','General');?>
</legend>

                <div class="form-group">
                    <div class="form-group">
                        <div class="input-group">
                            <label for="appNam" class="input-group-addon"><?php echo __('Generic','ASTERISK');?>
 <?php echo __('Installation','AppNam');?>
</label>
                            <input type="text" class="form-control" id="appNam" name="appNam" value="<?php echo $_smarty_tpl->tpl_vars['fields']->value['appName'];?>
" autofocus required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <label for="appMail" class="input-group-addon"><?php echo __('Generic','ASTERISK');?>
 <?php echo __('Installation','AppMail');?>
</label>
                            <input type="email" class="form-control" id="appMail" name="appMail" value="<?php echo $_smarty_tpl->tpl_vars['fields']->value['appMail'];?>
" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <label for="responseMail" class="input-group-addon"><?php echo __('Installation','ResponseMail');?>
</label>
                            <input type="email" class="form-control" id="responseMail" name="responseMail" value="<?php echo $_smarty_tpl->tpl_vars['fields']->value['responseMail'];?>
">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <label for="defaultLanguage" class="input-group-addon"><?php echo __('Generic','ASTERISK');?>
 <?php echo __('Installation','DefaultLanguage');?>
</label>
                            <select type="email" class="form-control" id="defaultLanguage" name="defaultLanguage" required>
                                <?php  $_smarty_tpl->tpl_vars['label'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['label']->_loop = false;
 $_smarty_tpl->tpl_vars['lang'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['langs']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['label']->key => $_smarty_tpl->tpl_vars['label']->value) {
$_smarty_tpl->tpl_vars['label']->_loop = true;
 $_smarty_tpl->tpl_vars['lang']->value = $_smarty_tpl->tpl_vars['label']->key;
?>
                                    <option value="<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['lang']->value==$_smarty_tpl->tpl_vars['fields']->value['defaultLanguage']) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['label']->value;?>
</option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="cleanUrl" class="input-group-addon"><?php echo __('Installation','CleanUrl');?>
</label>

                        <div class="form-control">
                            <input type="checkbox" id="cleanUrl" name="cleanUrl" <?php echo $_smarty_tpl->tpl_vars['fields']->value['cleanUrl'] ? 'checked' : '';?>
>
                        </div>
                    </div>
                </div>
            </fieldset>
mysql<input type = "radio" name = "base" value = "mysql">
pgsql<input type = "radio" name = "base" value = "pgsql">
           
<fieldset>
                <legend><?php echo __('Installation','Database');?>
</legend>


      <div class="form-group">
                    <div class="input-group">
                        <label for="dbConnectionString" class="input-group-addon"></label>
                        <input type="text" class="form-control" id="dbConnectionString" name="dbConnectionString" value="<?php echo $_smarty_tpl->tpl_vars['fields']->value['dbConnectionString'];?>
" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <label for="dbConnectionString" class="input-group-addon"><?php echo __('Generic','ASTERISK');?>
 <?php echo __('Installation','DbConnectionString');?>
</label>
                        <input type="text" class="form-control" id="dbConnectionString" name="dbConnectionString" value="<?php echo $_smarty_tpl->tpl_vars['fields']->value['dbConnectionString'];?>
" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <label for="dbUser" class="input-group-addon"><?php echo __('Generic','ASTERISK');?>
 <?php echo __('Installation','DbUser');?>
</label>
                        <input type="text" class="form-control" id="dbUser" name="dbUser" value="<?php echo $_smarty_tpl->tpl_vars['fields']->value['dbUser'];?>
" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <label for="dbPassword" class="input-group-addon"><?php echo __('Installation','DbPassword');?>
</label>
                        <input type="password" class="form-control" id="dbPassword" name="dbPassword" value="<?php echo $_smarty_tpl->tpl_vars['fields']->value['dbPassword'];?>
">
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <label for="dbPrefix" class="input-group-addon"><?php echo __('Installation','DbPrefix');?>
</label>
                        <input type="text" class="form-control" id="dbPrefix" name="dbPrefix" value="<?php echo $_smarty_tpl->tpl_vars['fields']->value['dbPrefix'];?>
">
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <label for="migrationTable" class="input-group-addon"><?php echo __('Generic','ASTERISK');?>
 <?php echo __('Installation','MigrationTable');?>
</label>
                        <input type="text" class="form-control" id="migrationTable" name="migrationTable" value="<?php echo $_smarty_tpl->tpl_vars['fields']->value['migrationTable'];?>
" required>
                    </div>
                </div>
            </fieldset>

            <div class="text-center form-group">
                <button type="submit" class="btn btn-primary"><?php echo __('Installation','Install');?>
</button>
            </div>

        </form>
    </div>
</div>


</main>
</div> <!-- .container -->
</body>
</html>
<?php }} ?>
