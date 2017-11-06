<?php /* Smarty version Smarty-3.1.21, created on 2017-11-02 17:57:24
         compiled from "/var/www/framadate//tpl/studs.tpl" */ ?>
<?php /*%%SmartyHeaderCode:70746031159fb4e741a26e5-28143015%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '22aaf25eb219b12cb5b288c357d10fe7b91e1ac9' => 
    array (
      0 => '/var/www/framadate//tpl/studs.tpl',
      1 => 1506522320,
      2 => 'file',
    ),
    'cece18edb8bb8323539cb82888af012e22be1acf' => 
    array (
      0 => '/var/www/framadate//tpl/page.tpl',
      1 => 1506522320,
      2 => 'file',
    ),
    '3626477cc82f67ad2b2b19b1456320cf820c40a4' => 
    array (
      0 => '/var/www/framadate//tpl/part/messages.tpl',
      1 => 1506522320,
      2 => 'file',
    ),
    'f1c4f5aa3074985b191f2d61fc10981ebd8e7861' => 
    array (
      0 => '/var/www/framadate//tpl/part/password_request.tpl',
      1 => 1506522320,
      2 => 'file',
    ),
    '613838c72cad28b3b8448059cf204a825a1df043' => 
    array (
      0 => '/var/www/framadate//tpl/part/poll_info.tpl',
      1 => 1506522320,
      2 => 'file',
    ),
    'c6a69af040de7a567a9f9c3ba76864201664501c' => 
    array (
      0 => '/var/www/framadate//tpl/part/poll_hint_admin.tpl',
      1 => 1506522320,
      2 => 'file',
    ),
    '8d1e47715ceb842b4762b41a2887132914acbad3' => 
    array (
      0 => '/var/www/framadate//tpl/part/poll_hint.tpl',
      1 => 1506522320,
      2 => 'file',
    ),
    'e4873f40877902c92a0259bd75d4e84389c47659' => 
    array (
      0 => '/var/www/framadate//tpl/part/vote_table_date.tpl',
      1 => 1509641803,
      2 => 'file',
    ),
    '0b952ba394bd77cb8c630a626fb259f5760f2440' => 
    array (
      0 => '/var/www/framadate//tpl/part/vote_table_classic.tpl',
      1 => 1506522320,
      2 => 'file',
    ),
    '81c2008f65dd2f5d8fbcf9b5e45d0b32887e0406' => 
    array (
      0 => '/var/www/framadate//tpl/part/comments_list.tpl',
      1 => 1506522320,
      2 => 'file',
    ),
    'b55e9013e5334fe9e7d49d887ac0604181647c09' => 
    array (
      0 => '/var/www/framadate//tpl/part/comments.tpl',
      1 => 1506522320,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '70746031159fb4e741a26e5-28143015',
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
  'unifunc' => 'content_59fb4e743f1fb6_63553872',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59fb4e743f1fb6_63553872')) {function content_59fb4e743f1fb6_63553872($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/var/www/framadate/vendor/smarty/smarty/libs/plugins/modifier.date_format.php';
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
 src="<?php echo smarty_modifier_resource("js/jquery-ui.min.js");?>
" type="text/javascript"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="<?php echo smarty_modifier_resource("js/Chart.min.js");?>
" type="text/javascript"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="<?php echo smarty_modifier_resource("js/Chart.StackedBar.js");?>
" type="text/javascript"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="<?php echo smarty_modifier_resource("js/app/studs.js");?>
" type="text/javascript"><?php echo '</script'; ?>
>
    <link rel="stylesheet" href="<?php echo smarty_modifier_resource('css/jquery-ui.min.css');?>
">



</head>
<body>
<?php if ($_smarty_tpl->tpl_vars['use_nav_js']->value) {?>
    <?php echo '<script'; ?>
 src="https://framasoft.org/nav/nav.js" type="text/javascript"><?php echo '</script'; ?>
>
<?php }?>
<div class="container ombre">

<?php echo $_smarty_tpl->getSubTemplate ('header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>





    
    <?php /*  Call merged included template "part/messages.tpl" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('part/messages.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0, '70746031159fb4e741a26e5-28143015');
content_59fb4e741cb6a1_04372484($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); 
/*  End of included template "part/messages.tpl" */?>


    <?php if (!$_smarty_tpl->tpl_vars['accessGranted']->value&&!$_smarty_tpl->tpl_vars['resultPubliclyVisible']->value) {?>

        <?php /*  Call merged included template "part/password_request.tpl" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('part/password_request.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('active'=>$_smarty_tpl->tpl_vars['poll']->value->active), 0, '70746031159fb4e741a26e5-28143015');
content_59fb4e741f60a2_57707183($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); 
/*  End of included template "part/password_request.tpl" */?>

    <?php } else { ?>

        
        <?php /*  Call merged included template "part/poll_info.tpl" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('part/poll_info.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('admin'=>$_smarty_tpl->tpl_vars['admin']->value), 0, '70746031159fb4e741a26e5-28143015');
content_59fb4e741fb3f0_44810098($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); 
/*  End of included template "part/poll_info.tpl" */?>

        
        <?php if ($_smarty_tpl->tpl_vars['expired']->value) {?>
            <div class="alert alert-danger">
                <p><?php echo __('studs','The poll is expired, it will be deleted soon.');?>
</p>
                <p><?php echo __('studs','Deletion date:');?>
 <?php echo smarty_modifier_html(smarty_modifier_date_format($_smarty_tpl->tpl_vars['deletion_date']->value,$_smarty_tpl->tpl_vars['date_format']->value['txt_short']));?>
</p>
            </div>
        <?php } else { ?>
            <?php if ($_smarty_tpl->tpl_vars['admin']->value) {?>
                <?php /*  Call merged included template "part/poll_hint_admin.tpl" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('part/poll_hint_admin.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0, '70746031159fb4e741a26e5-28143015');
content_59fb4e7426c5d8_14865441($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); 
/*  End of included template "part/poll_hint_admin.tpl" */?>
            <?php } else { ?>
                <?php /*  Call merged included template "part/poll_hint.tpl" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('part/poll_hint.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('active'=>$_smarty_tpl->tpl_vars['poll']->value->active), 0, '70746031159fb4e741a26e5-28143015');
content_59fb4e7427db81_96786959($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); 
/*  End of included template "part/poll_hint.tpl" */?>
            <?php }?>
        <?php }?>

        
        <div class="hidden row scroll-buttons" aria-hidden="true">
            <div class="btn-group pull-right">
                <button class="btn btn-sm btn-link scroll-left" title="<?php echo __('Poll results','Scroll to the left');?>
">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                </button>
                <button class="btn  btn-sm btn-link scroll-right" title="<?php echo __('Poll results','Scroll to the right');?>
">
                    <span class="glyphicon glyphicon-chevron-right"></span>
                </button>
            </div>
        </div>

        <?php if (!$_smarty_tpl->tpl_vars['accessGranted']->value&&$_smarty_tpl->tpl_vars['resultPubliclyVisible']->value) {?>
            <?php /*  Call merged included template "part/password_request.tpl" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('part/password_request.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('active'=>$_smarty_tpl->tpl_vars['poll']->value->active), 0, '70746031159fb4e741a26e5-28143015');
content_59fb4e741f60a2_57707183($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); 
/*  End of included template "part/password_request.tpl" */?>
        <?php }?>

        
        <?php if ($_smarty_tpl->tpl_vars['poll']->value->format==='D') {?>
            <?php /*  Call merged included template "part/vote_table_date.tpl" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('part/vote_table_date.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('active'=>$_smarty_tpl->tpl_vars['poll']->value->active), 0, '70746031159fb4e741a26e5-28143015');
content_59fb4e7428a156_10100796($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); 
/*  End of included template "part/vote_table_date.tpl" */?>
        <?php } else { ?>
            <?php /*  Call merged included template "part/vote_table_classic.tpl" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('part/vote_table_classic.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('active'=>$_smarty_tpl->tpl_vars['poll']->value->active), 0, '70746031159fb4e741a26e5-28143015');
content_59fb4e74355aa7_76270587($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); 
/*  End of included template "part/vote_table_classic.tpl" */?>
        <?php }?>

        
        <?php /*  Call merged included template "part/comments.tpl" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('part/comments.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('active'=>$_smarty_tpl->tpl_vars['poll']->value->active,'comments'=>$_smarty_tpl->tpl_vars['comments']->value), 0, '70746031159fb4e741a26e5-28143015');
content_59fb4e743df1c7_88532632($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); 
/*  End of included template "part/comments.tpl" */?>

    <?php }?>



</main>
</div> <!-- .container -->
</body>
</html>
<?php }} ?>
<?php /* Smarty version Smarty-3.1.21, created on 2017-11-02 17:57:24
         compiled from "/var/www/framadate//tpl/part/messages.tpl" */ ?>
<?php if ($_valid && !is_callable('content_59fb4e741cb6a1_04372484')) {function content_59fb4e741cb6a1_04372484($_smarty_tpl) {?>
<div id="message-container">
    <?php if (!empty($_smarty_tpl->tpl_vars['message']->value)) {?>
        <div class="alert alert-dismissible alert-<?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['message']->value->type);?>
 hidden-print" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="<?php echo __('Generic','Close');?>
"><span aria-hidden="true">&times;</span></button>
            <?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['message']->value->message);?>

            <?php if ($_smarty_tpl->tpl_vars['message']->value->link!=null) {?>
                <div class="input-group input-group-sm">
                        <span class="input-group-btn">
                            <a <?php if ($_smarty_tpl->tpl_vars['message']->value->linkTitle!=null) {?> title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['message']->value->linkTitle, ENT_QUOTES, 'ISO-8859-1', true);?>
" <?php }?> class="btn btn-default btn-sm" href="<?php echo $_smarty_tpl->tpl_vars['message']->value->link;?>
">
                                <?php if ($_smarty_tpl->tpl_vars['message']->value->linkIcon!=null) {?><i class="glyphicon glyphicon-pencil"></i><?php if ($_smarty_tpl->tpl_vars['message']->value->linkTitle!=null) {?><span class="sr-only"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['message']->value->linkTitle, ENT_QUOTES, 'ISO-8859-1', true);?>
</span><?php }
}?>
                            </a>
                        </span>
                    <input type="text" aria-hidden="true" value="<?php echo $_smarty_tpl->tpl_vars['message']->value->link;?>
" class="form-control" readonly="readonly" >
                </div>
                <?php if ($_smarty_tpl->tpl_vars['message']->value->includeTemplate!=null) {?>
                    <?php echo $_smarty_tpl->tpl_vars['message']->value->includeTemplate;?>

                <?php }?>
            <?php }?>
        </div>
    <?php }?>
</div>
<div id="nameErrorMessage" class="hidden alert alert-dismissible alert-danger hidden-print" role="alert"><?php echo __('Error','The name is invalid.');?>
<button type="button" class="close" data-dismiss="alert" aria-label="<?php echo __('Generic','Close');?>
"><span aria-hidden="true">&times;</span></button></div>
<div id="genericErrorTemplate" class="hidden alert alert-dismissible alert-danger hidden-print" role="alert"><span class="contents"></span><button type="button" class="close" data-dismiss="alert" aria-label="<?php echo __('Generic','Close');?>
"><span aria-hidden="true">&times;</span></button></div>
<div id="genericUnclosableSuccessTemplate" class="hidden alert alert-success hidden-print" role="alert"><span class="contents"></span></div><?php }} ?>
<?php /* Smarty version Smarty-3.1.21, created on 2017-11-02 17:57:24
         compiled from "/var/www/framadate//tpl/part/password_request.tpl" */ ?>
<?php if ($_valid && !is_callable('content_59fb4e741f60a2_57707183')) {function content_59fb4e741f60a2_57707183($_smarty_tpl) {?><?php if (!$_smarty_tpl->tpl_vars['expired']->value&&($_smarty_tpl->tpl_vars['active']->value||$_smarty_tpl->tpl_vars['resultPubliclyVisible']->value)) {?>
    <hr role="presentation" id="password_request" class="hidden-print"/>

    <div class="panel panel-danger password_request alert-danger">
        <div class="col-md-6 col-md-offset-3">
            <form action="" method="POST" class="form-inline">
                <input type="hidden" name="poll" value="<?php echo $_smarty_tpl->tpl_vars['poll_id']->value;?>
"/>
                <div class="form-group">
                    <label for="password" class="control-label"><?php echo __('Password','Password');?>
</label>
                    <input type="password" name="password" id="password" class="form-control" />
                    <input type="submit" value="<?php echo __('Password','Submit access');?>
" class="btn btn-success">
                </div>
            </form>
        </div>
        <div class="clearfix"></div>
    </div>
<?php }?>

<?php }} ?>
<?php /* Smarty version Smarty-3.1.21, created on 2017-11-02 17:57:24
         compiled from "/var/www/framadate//tpl/part/poll_info.tpl" */ ?>
<?php if ($_valid && !is_callable('content_59fb4e741fb3f0_44810098')) {function content_59fb4e741fb3f0_44810098($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/var/www/framadate/vendor/smarty/smarty/libs/plugins/modifier.date_format.php';
?><?php $_smarty_tpl->tpl_vars['admin'] = new Smarty_variable((($tmp = @$_smarty_tpl->tpl_vars['admin']->value)===null||$tmp==='' ? false : $tmp), null, 0);?>

<?php if ($_smarty_tpl->tpl_vars['admin']->value) {?><form action="<?php echo smarty_function_poll_url(array('id'=>$_smarty_tpl->tpl_vars['admin_poll_id']->value,'admin'=>true),$_smarty_tpl);?>
" method="POST"><?php }?>
    <div class="jumbotron<?php if ($_smarty_tpl->tpl_vars['admin']->value) {?> bg-danger<?php }?>">
        <div class="row"> 
            <div id="title-form" class="col-md-7">
                <h3><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['poll']->value->title);
if ($_smarty_tpl->tpl_vars['admin']->value&&!$_smarty_tpl->tpl_vars['expired']->value) {?> <button class="btn btn-link btn-sm btn-edit" title="<?php echo __('PollInfo','Edit the title');?>
"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only"><?php echo __('Generic','Edit');?>
</span></button><?php }?></h3>
                <?php if ($_smarty_tpl->tpl_vars['admin']->value&&!$_smarty_tpl->tpl_vars['expired']->value) {?>
                    <div class="hidden js-title">
                        <label class="sr-only" for="newtitle"><?php echo __('PollInfo','Title');?>
</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="newtitle" name="title" size="40" value="<?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['poll']->value->title);?>
" />
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-success" name="update_poll_info" value="title" title="<?php echo __('PollInfo','Save the new title');?>
"><span class="glyphicon glyphicon-ok"></span><span class="sr-only"><?php echo __('Generic','Save');?>
</span></button>
                                <button class="btn btn-link btn-cancel" title="<?php echo __('PollInfo','Cancel the title edit');?>
"><span class="glyphicon glyphicon-remove"></span><span class="sr-only"><?php echo __('Generic','Cancel');?>
</span></button>
                            </span>
                        </div>
                    </div>
                <?php }?>
            </div>
            <div class="col-md-5 hidden-print">
                <div class="btn-group pull-right">
                    <button onclick="print(); return false;" class="btn btn-default"><span class="glyphicon glyphicon-print"></span> <?php echo __('PollInfo','Print');?>
</button>
                    <?php if ($_smarty_tpl->tpl_vars['admin']->value) {?>
                        <a href="<?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['SERVER_URL']->value);?>
exportcsv.php?admin=<?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['admin_poll_id']->value);?>
" class="btn btn-default"><span class="glyphicon glyphicon-download-alt"></span> <?php echo __('PollInfo','Export to CSV');?>
</a>
                    <?php } else { ?>
                        <?php if (!$_smarty_tpl->tpl_vars['hidden']->value) {?>
                            <a href="<?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['SERVER_URL']->value);?>
exportcsv.php?poll=<?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['poll_id']->value);?>
" class="btn btn-default"><span class="glyphicon glyphicon-download-alt"></span> <?php echo __('PollInfo','Export to CSV');?>
</a>
                        <?php }?>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['admin']->value) {?>
                        <?php if (!$_smarty_tpl->tpl_vars['expired']->value) {?>
                        <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown">
                            <span class="glyphicon glyphicon-trash"></span> <span class="sr-only"><?php echo __('Generic','Remove');?>
</span> <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><button class="btn btn-link" type="submit" name="remove_all_votes"><?php echo __('PollInfo','Remove all the votes');?>
</button></li>
                            <li><button class="btn btn-link" type="submit" name="remove_all_comments"><?php echo __('PollInfo','Remove all the comments');?>
</button></li>
                            <li class="divider" role="presentation"></li>
                            <li><button class="btn btn-link" type="submit" name="delete_poll"><?php echo __('PollInfo','Remove the poll');?>
</button></li>
                        </ul>
                        <?php } else { ?>
                            <button class="btn btn-danger" type="submit" name="delete_poll" title="<?php echo __('PollInfo','Remove the poll');?>
">
                                <span class="glyphicon glyphicon-trash"></span>
                                <span class="sr-only"><?php echo __('PollInfo','Remove the poll');?>
</span>
                            </button>
                        <?php }?>
                    <?php }?>
                </div>
            </div>
        </div>
        <div class="row"> 
            <div class="form-group col-md-4">
                <div id="name-form">
                    <label class="control-label"><?php echo __('PollInfo','Initiator of the poll');?>
</label>
                    <p class="form-control-static"><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['poll']->value->admin_name);
if ($_smarty_tpl->tpl_vars['admin']->value&&!$_smarty_tpl->tpl_vars['expired']->value) {?> <button class="btn btn-link btn-sm btn-edit" title="<?php echo __('PollInfo','Edit the name');?>
"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only"><?php echo __('Generic','Edit');?>
</span></button><?php }?></p>
                    <?php if ($_smarty_tpl->tpl_vars['admin']->value&&!$_smarty_tpl->tpl_vars['expired']->value) {?>
                    <div class="hidden js-name">
                        <label class="sr-only" for="newname"><?php echo __('PollInfo','Initiator of the poll');?>
</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="newname" name="name" size="40" value="<?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['poll']->value->admin_name);?>
" />
                            <span class="input-group-btn">
                            <button type="submit" class="btn btn-success" name="update_poll_info" value="name" title="<?php echo __('PollInfo','Save the new name');?>
"><span class="glyphicon glyphicon-ok"></span><span class="sr-only"><?php echo __('Generic','Save');?>
</span></button>
                            <button class="btn btn-link btn-cancel" title="<?php echo __('PollInfo','Cancel the name edit');?>
"><span class="glyphicon glyphicon-remove"></span><span class="sr-only"><?php echo __('Generic','Cancel');?>
</span></button>
                            </span>
                        </div>
                    </div>
                    <?php }?>
                </div>
                <?php if ($_smarty_tpl->tpl_vars['admin']->value) {?>
                <div id="email-form">
                    <p><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['poll']->value->admin_mail);
if (!$_smarty_tpl->tpl_vars['expired']->value) {?> <button class="btn btn-link btn-sm btn-edit" title="<?php echo __('PollInfo','Edit the email adress');?>
"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only"><?php echo __('Generic','Edit');?>
</span></button><?php }?></p>
                    <?php if (!$_smarty_tpl->tpl_vars['expired']->value) {?>
                        <div class="hidden js-email">
                            <label class="sr-only" for="admin_mail"><?php echo __('PollInfo','Email');?>
</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="admin_mail" name="admin_mail" size="40" value="<?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['poll']->value->admin_mail);?>
" />
                            <span class="input-group-btn">
                                <button type="submit" name="update_poll_info" value="admin_mail" class="btn btn-success" title="<?php echo __('PollInfo','Save the email address');?>
"><span class="glyphicon glyphicon-ok"></span><span class="sr-only"><?php echo __('Generic','Save');?>
</span></button>
                                <button class="btn btn-link btn-cancel" title="<?php echo __('PollInfo','Cancel the email address edit');?>
"><span class="glyphicon glyphicon-remove"></span><span class="sr-only"><?php echo __('Generic','Cancel');?>
</span></button>
                            </span>
                            </div>
                        </div>
                    <?php }?>
                </div>
                <?php }?>
            </div>
            <?php if ($_smarty_tpl->tpl_vars['admin']->value||preg_match('/[^ \r\n]/',$_smarty_tpl->tpl_vars['poll']->value->description)) {?>
                <div class="form-group col-md-8" id="description-form">
                    <label class="control-label"><?php echo __('Generic','Description');
if ($_smarty_tpl->tpl_vars['admin']->value&&!$_smarty_tpl->tpl_vars['expired']->value) {?> <button class="btn btn-link btn-sm btn-edit" title="<?php echo __('PollInfo','Edit the description');?>
"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only"><?php echo __('Generic','Edit');?>
</span></button><?php }?></label>
                    <pre class="form-control-static well poll-description"><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['poll']->value->description);?>
</pre>
                    <?php if ($_smarty_tpl->tpl_vars['admin']->value&&!$_smarty_tpl->tpl_vars['expired']->value) {?>
                        <div class="hidden js-desc text-right">
                            <label class="sr-only" for="newdescription"><?php echo __('Generic','Description');?>
</label>
                            <textarea class="form-control" id="newdescription" name="description" rows="2" cols="40"><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['poll']->value->description);?>
</textarea>
                            <button type="submit" id="btn-new-desc" name="update_poll_info" value="description" class="btn btn-sm btn-success" title="<?php echo __('PollInfo','Save the description');?>
"><span class="glyphicon glyphicon-ok"></span><span class="sr-only"><?php echo __('Generic','Save');?>
</span></button>
                            <button class="btn btn-default btn-sm btn-cancel" title="<?php echo __('PollInfo','Cancel the description edit');?>
"><span class="glyphicon glyphicon-remove"></span><span class="sr-only"><?php echo __('Generic','Cancel');?>
</span></button>
                        </div>
                    <?php }?>
                </div>
            <?php }?>
        </div>
        <div class="row">
        </div>

        <div class="row">
            <div class="form-group form-group <?php if ($_smarty_tpl->tpl_vars['admin']->value) {?>col-md-4<?php } else { ?>col-md-6<?php }?>">
                <label for="public-link"><a class="public-link" href="<?php echo smarty_function_poll_url(array('id'=>$_smarty_tpl->tpl_vars['poll_id']->value),$_smarty_tpl);?>
"><?php echo __('PollInfo','Public link of the poll');?>
 <span class="btn-link glyphicon glyphicon-link"></span></a></label>
                <input class="form-control" id="public-link" type="text" readonly="readonly" value="<?php echo smarty_function_poll_url(array('id'=>$_smarty_tpl->tpl_vars['poll_id']->value),$_smarty_tpl);?>
" onclick="select();"/>
            </div>
            <?php if ($_smarty_tpl->tpl_vars['admin']->value) {?>
                <div class="form-group col-md-4">
                    <label for="admin-link"><a class="admin-link" href="<?php echo smarty_function_poll_url(array('id'=>$_smarty_tpl->tpl_vars['admin_poll_id']->value,'admin'=>true),$_smarty_tpl);?>
"><?php echo __('PollInfo','Admin link of the poll');?>
 <span class="btn-link glyphicon glyphicon-link"></span></a></label>
                    <input class="form-control" id="admin-link" type="text" readonly="readonly" value="<?php echo smarty_function_poll_url(array('id'=>$_smarty_tpl->tpl_vars['admin_poll_id']->value,'admin'=>true),$_smarty_tpl);?>
" onclick="select();"/>
                </div>
                <div id="expiration-form" class="form-group col-md-4">
                    <label class="control-label"><?php echo __('PollInfo','Expiration date');?>
</label>
                    <p><?php echo smarty_modifier_html(smarty_modifier_date_format($_smarty_tpl->tpl_vars['poll']->value->end_date,$_smarty_tpl->tpl_vars['date_format']->value['txt_date']));?>
 <button class="btn btn-link btn-sm btn-edit" title="<?php echo __('PollInfo','Edit the expiration date');?>
"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only"><?php echo __('Generic','Edit');?>
</span></button></p>

                        <div class="hidden js-expiration">
                            <label class="sr-only" for="newexpirationdate"><?php echo __('PollInfo','Expiration date');?>
</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="newexpirationdate" name="expiration_date" size="40" value="<?php echo smarty_modifier_html(smarty_modifier_date_format($_smarty_tpl->tpl_vars['poll']->value->end_date,$_smarty_tpl->tpl_vars['date_format']->value['txt_date']));?>
" />
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-success" name="update_poll_info" value="expiration_date" title="<?php echo __('PollInfo','Save the new expiration date');?>
"><span class="glyphicon glyphicon-ok"></span><span class="sr-only"><?php echo __('Generic','Save');?>
</span></button>
                                    <button class="btn btn-link btn-cancel" title="<?php echo __('PollInfo','Cancel the expiration date edit');?>
"><span class="glyphicon glyphicon-remove"></span><span class="sr-only"><?php echo __('Generic','Cancel');?>
</span></button>
                                </span>
                            </div>
                        </div>

                </div>
            <?php }?>
        </div>
        <?php if ($_smarty_tpl->tpl_vars['admin']->value) {?>
            <div class="row">
                <div class="col-md-4">
                    <div id="password-form">
                        <?php if (!empty($_smarty_tpl->tpl_vars['poll']->value->password_hash)&&!$_smarty_tpl->tpl_vars['poll']->value->results_publicly_visible) {?>
                            <?php $_smarty_tpl->tpl_vars['password_text'] = new Smarty_variable(__('PollInfo','Password protected'), null, 0);?>
                        <?php } elseif (!empty($_smarty_tpl->tpl_vars['poll']->value->password_hash)&&$_smarty_tpl->tpl_vars['poll']->value->results_publicly_visible) {?>
                            <?php $_smarty_tpl->tpl_vars['password_text'] = new Smarty_variable(__('PollInfo','Votes protected by password'), null, 0);?>
                        <?php } else { ?>
                            <?php $_smarty_tpl->tpl_vars['password_text'] = new Smarty_variable(__('PollInfo','No password'), null, 0);?>
                        <?php }?>
                        <p class=""><span class="glyphicon glyphicon-lock"> </span> <?php echo $_smarty_tpl->tpl_vars['password_text']->value;?>
<button class="btn btn-link btn-sm btn-edit" title="<?php echo __('PollInfo','Edit the poll rules');?>
"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only"><?php echo __('Generic','Edit');?>
</span></button></p>
                        <div class="hidden js-password">
                            <button class="btn btn-link btn-cancel" title="<?php echo __('PollInfo','Cancel the rules edit');?>
"><span class="glyphicon glyphicon-remove"></span><span class="sr-only"><?php echo __('Generic','Cancel');?>
</span></button>
                            <?php if (!empty($_smarty_tpl->tpl_vars['poll']->value->password_hash)) {?>
                                <div class="input-group">
                                    <input type="checkbox" id="removePassword" name="removePassword"/>
                                    <label for="removePassword"><?php echo __('PollInfo','Remove password');?>
</label>
                                    <button type="submit" name="update_poll_info" value="removePassword" class="btn btn-success hidden" title="<?php echo __('PollInfo','Save the new rules');?>
"><span class="glyphicon glyphicon-ok"></span><span class="sr-only"><?php echo __('Generic','Remove password.');?>
</span></button>
                                </div>
                            <?php }?>
                            <div id="password_information">
                                <div class="input-group">
                                    <input type="checkbox" id="resultsPubliclyVisible" name="resultsPubliclyVisible" <?php if ($_smarty_tpl->tpl_vars['poll']->value->results_publicly_visible) {?>checked="checked"<?php }?>/>
                                    <label for="resultsPubliclyVisible"><?php echo __('PollInfo','Results are visible');?>
</label>
                                </div>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="password" name="password"/>
                                    <span class="input-group-btn">
                                        <button type="submit" name="update_poll_info" value="password" class="btn btn-success" title="<?php echo __('PollInfo','Save the new rules');?>
"><span class="glyphicon glyphicon-ok"></span><span class="sr-only"><?php echo __('Generic','Save');?>
</span></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 ">
                    <div id="poll-hidden-form">
                        <?php if ($_smarty_tpl->tpl_vars['poll']->value->hidden) {?>
                            <?php $_smarty_tpl->tpl_vars['hidden_icon'] = new Smarty_variable("glyphicon-eye-close", null, 0);?>
                            <?php $_smarty_tpl->tpl_vars['hidden_text'] = new Smarty_variable(__('PollInfo','Results are hidden'), null, 0);?>
                        <?php } else { ?>
                            <?php $_smarty_tpl->tpl_vars['hidden_icon'] = new Smarty_variable("glyphicon-eye-open", null, 0);?>
                            <?php $_smarty_tpl->tpl_vars['hidden_text'] = new Smarty_variable(__('PollInfo','Results are visible'), null, 0);?>
                        <?php }?>
                        <p class=""><span class="glyphicon <?php echo $_smarty_tpl->tpl_vars['hidden_icon']->value;?>
"> </span> <?php echo $_smarty_tpl->tpl_vars['hidden_text']->value;?>
<button class="btn btn-link btn-sm btn-edit" title="<?php echo __('PollInfo','Edit the poll rules');?>
"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only"><?php echo __('Generic','Edit');?>
</span></button></p>
                        <div class="hidden js-poll-hidden">
                            <div class="input-group">
                                <input type="checkbox" id="hidden" name="hidden" <?php if ($_smarty_tpl->tpl_vars['poll']->value->hidden) {?>checked="checked"<?php }?>/>
                                <label for="hidden"><?php echo __('PollInfo','Results are hidden');?>
</label>
                                <span class="input-group-btn">
                                    <button type="submit" name="update_poll_info" value="hidden" class="btn btn-success" title="<?php echo __('PollInfo','Save the new rules');?>
"><span class="glyphicon glyphicon-ok"></span><span class="sr-only"><?php echo __('Generic','Save');?>
</span></button>
                                    <button class="btn btn-link btn-cancel" title="<?php echo __('PollInfo','Cancel the rules edit');?>
"><span class="glyphicon glyphicon-remove"></span><span class="sr-only"><?php echo __('Generic','Cancel');?>
</span></button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" >
                    <div id="poll-rules-form">
                        <?php if ($_smarty_tpl->tpl_vars['poll']->value->active) {?>
                            <?php if ($_smarty_tpl->tpl_vars['poll']->value->editable) {?>
                                <?php if ($_smarty_tpl->tpl_vars['poll']->value->editable==constant("Framadate\Editable::EDITABLE_BY_ALL")) {?>
                                    <?php $_smarty_tpl->tpl_vars['rule_id'] = new Smarty_variable(2, null, 0);?>
                                    <?php $_smarty_tpl->tpl_vars['rule_txt'] = new Smarty_variable(__('Step 1','All voters can modify any vote'), null, 0);?>
                                <?php } else { ?>
                                    <?php $_smarty_tpl->tpl_vars['rule_id'] = new Smarty_variable(3, null, 0);?>
                                    <?php $_smarty_tpl->tpl_vars['rule_txt'] = new Smarty_variable(__('Step 1','Voters can modify their vote themselves'), null, 0);?>
                                <?php }?>
                                <?php $_smarty_tpl->tpl_vars['rule_icon'] = new Smarty_variable('<span class="glyphicon glyphicon-edit"></span>', null, 0);?>
                            <?php } else { ?>
                                <?php $_smarty_tpl->tpl_vars['rule_id'] = new Smarty_variable(1, null, 0);?>
                                <?php $_smarty_tpl->tpl_vars['rule_icon'] = new Smarty_variable('<span class="glyphicon glyphicon-check"></span>', null, 0);?>
                                <?php $_smarty_tpl->tpl_vars['rule_txt'] = new Smarty_variable(__('Step 1','Votes cannot be modified'), null, 0);?>
                            <?php }?>
                        <?php } else { ?>
                            <?php $_smarty_tpl->tpl_vars['rule_id'] = new Smarty_variable(0, null, 0);?>
                            <?php $_smarty_tpl->tpl_vars['rule_icon'] = new Smarty_variable('<span class="glyphicon glyphicon-lock"></span>', null, 0);?>
                            <?php $_smarty_tpl->tpl_vars['rule_txt'] = new Smarty_variable(__('PollInfo','Votes and comments are locked'), null, 0);?>
                        <?php }?>
                        <p class=""><?php echo $_smarty_tpl->tpl_vars['rule_icon']->value;?>
 <?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['rule_txt']->value);?>
 <button class="btn btn-link btn-sm btn-edit" title="<?php echo __('PollInfo','Edit the poll rules');?>
"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only"><?php echo __('Generic','Edit');?>
</span></button></p>
                        <div class="hidden js-poll-rules">
                            <label class="sr-only" for="rules"><?php echo __('PollInfo','Poll rules');?>
</label>
                            <div class="input-group">
                                <select class="form-control" id="rules" name="rules">
                                    <option value="0"<?php if ($_smarty_tpl->tpl_vars['rule_id']->value==0) {?> selected="selected"<?php }?>><?php echo __('PollInfo','Votes and comments are locked');?>
</option>
                                    <option value="1"<?php if ($_smarty_tpl->tpl_vars['rule_id']->value==1) {?> selected="selected"<?php }?>><?php echo __('Step 1','Votes cannot be modified');?>
</option>
                                    <option value="3"<?php if ($_smarty_tpl->tpl_vars['rule_id']->value==3) {?> selected="selected"<?php }?>><?php echo __('Step 1','Voters can modify their vote themselves');?>
</option>
                                    <option value="2"<?php if ($_smarty_tpl->tpl_vars['rule_id']->value==2) {?> selected="selected"<?php }?>><?php echo __('Step 1','All voters can modify any vote');?>
</option>
                                </select>
                                <span class="input-group-btn">
                                    <button type="submit" name="update_poll_info" value="rules" class="btn btn-success" title="<?php echo __('PollInfo','Save the new rules');?>
"><span class="glyphicon glyphicon-ok"></span><span class="sr-only"><?php echo __('Generic','Save');?>
</span></button>
                                    <button class="btn btn-link btn-cancel" title="<?php echo __('PollInfo','Cancel the rules edit');?>
"><span class="glyphicon glyphicon-remove"></span><span class="sr-only"><?php echo __('Generic','Cancel');?>
</span></button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php }?>
    </div>
<?php if ($_smarty_tpl->tpl_vars['admin']->value) {?></form><?php }?>
<?php }} ?>
<?php /* Smarty version Smarty-3.1.21, created on 2017-11-02 17:57:24
         compiled from "/var/www/framadate//tpl/part/poll_hint_admin.tpl" */ ?>
<?php if ($_valid && !is_callable('content_59fb4e7426c5d8_14865441')) {function content_59fb4e7426c5d8_14865441($_smarty_tpl) {?><div id="hint_modal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><?php echo __('Generic','Caption');?>
</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <p><?php echo __('adminstuds','As poll administrator, you can change all the lines of this poll with this button');?>

                        <span class="glyphicon glyphicon-pencil"></span><span
                                class="sr-only"><?php echo __('Generic','Edit');?>
</span>,
                        <?php echo __('adminstuds','remove a column or a line with');?>
 <span
                                class="glyphicon glyphicon-remove text-danger"></span><span
                                class="sr-only"><?php echo __('Generic','Remove');?>
</span>
                        <?php echo __('adminstuds','and add a new column with');?>
 <span
                                class="glyphicon glyphicon-plus text-success"></span><span
                                class="sr-only"><?php echo __('adminstuds','Add a column');?>
</span>.</p>

                    <p><?php echo __('adminstuds','Finally, you can change the informations of this poll like the title, the comments or your email address.');?>
</p>

                    <p aria-hidden="true"><strong><?php echo __('Generic','Legend:');?>
</strong> <span
                                class="glyphicon glyphicon-ok"></span> = <?php echo __('Generic','Yes');?>
, <b>(<span
                                    class="glyphicon glyphicon-ok"></span>)</b> = <?php echo __('Generic','Ifneedbe');?>
, <span
                                class="glyphicon glyphicon-ban-circle"></span> = <?php echo __('Generic','No');?>
</p>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php }} ?>
<?php /* Smarty version Smarty-3.1.21, created on 2017-11-02 17:57:24
         compiled from "/var/www/framadate//tpl/part/poll_hint.tpl" */ ?>
<?php if ($_valid && !is_callable('content_59fb4e7427db81_96786959')) {function content_59fb4e7427db81_96786959($_smarty_tpl) {?><div id="hint_modal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><?php echo __('Generic','Caption');?>
</h4>
            </div>
            <div class="modal-body">
                <?php if ($_smarty_tpl->tpl_vars['active']->value) {?>
                    <div class="alert alert-info">
                        <p><?php echo __('studs','If you want to vote in this poll, you have to give your name, choose the values that fit best for you and validate with the plus button at the end of the line.');?>
</p>

                        <p aria-hidden="true"><b><?php echo __('Generic','Legend:');?>
</b> <span
                                    class="glyphicon glyphicon-ok"></span>
                            = <?php echo __('Generic','Yes');?>
, <b>(<span class="glyphicon glyphicon-ok"></span>)</b>
                            = <?php echo __('Generic','Ifneedbe');?>
, <span class="glyphicon glyphicon-ban-circle"></span>
                            = <?php echo __('Generic','No');?>
</p>
                    </div>
                <?php } else { ?>
                    <div class="alert alert-danger">
                        <p><?php echo __('studs','POLL_LOCKED_WARNING');?>
</p>

                        <p aria-hidden="true"><b><?php echo __('Generic','Legend:');?>
</b> <span
                                    class="glyphicon glyphicon-ok"></span>
                            = <?php echo __('Generic','Yes');?>
, <b>(<span class="glyphicon glyphicon-ok"></span>)</b>
                            = <?php echo __('Generic','Ifneedbe');?>
, <span class="glyphicon glyphicon-ban-circle"></span>
                            = <?php echo __('Generic','No');?>
</p>
                    </div>
                <?php }?>
            </div>
        </div>
    </div>
</div><?php }} ?>
<?php /* Smarty version Smarty-3.1.21, created on 2017-11-02 17:57:24
         compiled from "/var/www/framadate//tpl/part/vote_table_date.tpl" */ ?>
<?php if ($_valid && !is_callable('content_59fb4e7428a156_10100796')) {function content_59fb4e7428a156_10100796($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/var/www/framadate/vendor/smarty/smarty/libs/plugins/modifier.date_format.php';
?><?php if (!is_array($_smarty_tpl->tpl_vars['best_choices']->value)||empty($_smarty_tpl->tpl_vars['best_choices']->value)) {?>
    <?php $_smarty_tpl->tpl_vars['best_choices'] = new Smarty_variable(array(0), null, 0);?>
<?php }?>

<h3>
    <?php echo __('Poll results','Votes of the poll');?>
 <?php if ($_smarty_tpl->tpl_vars['hidden']->value) {?><i>(<?php echo __('PollInfo','Results are hidden');?>
)</i><?php }?>
    <?php if ($_smarty_tpl->tpl_vars['accessGranted']->value) {?>
        <a href="" data-toggle="modal" data-target="#hint_modal"><i class="glyphicon glyphicon-info-sign"></i></a>
    <?php }?>
</h3>


<div id="tableContainer" class="tableContainer">
    <form action="<?php if ($_smarty_tpl->tpl_vars['admin']->value) {
echo smarty_function_poll_url(array('id'=>$_smarty_tpl->tpl_vars['admin_poll_id']->value,'admin'=>true),$_smarty_tpl);
} else {
echo smarty_function_poll_url(array('id'=>$_smarty_tpl->tpl_vars['poll_id']->value),$_smarty_tpl);
}?>" method="POST" id="poll_form">
        <input type="hidden" name="control" value="<?php echo $_smarty_tpl->tpl_vars['slots_hash']->value;?>
"/>
        <table class="results">
            <caption class="sr-only"><?php echo __('Poll results','Votes of the poll');?>
 <?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['poll']->value->title);?>
</caption>
            <thead>
            <?php if ($_smarty_tpl->tpl_vars['admin']->value&&!$_smarty_tpl->tpl_vars['expired']->value) {?>
                <tr class="hidden-print">
                    <th role="presentation"></th>
                    <?php $_smarty_tpl->tpl_vars['headersDCount'] = new Smarty_variable(0, null, 0);?>
                    <?php  $_smarty_tpl->tpl_vars['slot'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['slot']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['slots']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['slot']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['slot']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['slot']->key => $_smarty_tpl->tpl_vars['slot']->value) {
$_smarty_tpl->tpl_vars['slot']->_loop = true;
 $_smarty_tpl->tpl_vars['slot']->iteration++;
 $_smarty_tpl->tpl_vars['slot']->last = $_smarty_tpl->tpl_vars['slot']->iteration === $_smarty_tpl->tpl_vars['slot']->total;
?>
                        <?php  $_smarty_tpl->tpl_vars['moment'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['moment']->_loop = false;
 $_smarty_tpl->tpl_vars['id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['slot']->value->moments; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['moment']->key => $_smarty_tpl->tpl_vars['moment']->value) {
$_smarty_tpl->tpl_vars['moment']->_loop = true;
 $_smarty_tpl->tpl_vars['id']->value = $_smarty_tpl->tpl_vars['moment']->key;
?>
                            <td headers="M<?php echo $_smarty_tpl->tpl_vars['slot']->key;?>
 D<?php echo $_smarty_tpl->tpl_vars['headersDCount']->value;?>
 H<?php echo $_smarty_tpl->tpl_vars['headersDCount']->value;?>
">
                                <a href="<?php echo smarty_function_poll_url(array('id'=>$_smarty_tpl->tpl_vars['admin_poll_id']->value,'admin'=>true,'action'=>'delete_column','action_value'=>(($_smarty_tpl->tpl_vars['slot']->value->day).('@')).($_smarty_tpl->tpl_vars['moment']->value)),$_smarty_tpl);?>
"
                                   data-remove-confirmation="<?php echo __('adminstuds','Confirm removal of the column.');?>
"
                                   class="btn btn-link btn-sm remove-column"
                                   title="<?php echo __('adminstuds','Remove the column');?>
 <?php echo smarty_modifier_html(smarty_modifier_date_format($_smarty_tpl->tpl_vars['slot']->value->day,$_smarty_tpl->tpl_vars['date_format']->value['txt_short']));?>
 - <?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['moment']->value);?>
">
                                    <i class="glyphicon glyphicon-remove text-danger"></i><span class="sr-only"><?php echo __('Generic','Remove');?>
</span>
                                </a>
                            </td>
                            <?php $_smarty_tpl->tpl_vars['headersDCount'] = new Smarty_variable($_smarty_tpl->tpl_vars['headersDCount']->value+1, null, 0);?>
                        <?php } ?>
                    <?php } ?>
                    <td>
                        <a href="<?php echo smarty_function_poll_url(array('id'=>$_smarty_tpl->tpl_vars['admin_poll_id']->value,'admin'=>true,'action'=>'add_column'),$_smarty_tpl);?>
"
                           class="btn btn-link btn-sm" title="<?php echo __('adminstuds','Add a column');?>
">
                            <i class="glyphicon glyphicon-plus text-success"></i><span class="sr-only"><?php echo __('Poll results','Add a column');?>
</span>
                        </a>
                    </td>
                </tr>
            <?php }?>
            <tr>
                <th role="presentation"></th>
                <?php $_smarty_tpl->tpl_vars['count_same'] = new Smarty_variable(0, null, 0);?>
                <?php $_smarty_tpl->tpl_vars['previous'] = new Smarty_variable(0, null, 0);?>
                <?php  $_smarty_tpl->tpl_vars['slot'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['slot']->_loop = false;
 $_smarty_tpl->tpl_vars['id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['slots']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['slot']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['slot']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['slot']->key => $_smarty_tpl->tpl_vars['slot']->value) {
$_smarty_tpl->tpl_vars['slot']->_loop = true;
 $_smarty_tpl->tpl_vars['id']->value = $_smarty_tpl->tpl_vars['slot']->key;
 $_smarty_tpl->tpl_vars['slot']->iteration++;
 $_smarty_tpl->tpl_vars['slot']->last = $_smarty_tpl->tpl_vars['slot']->iteration === $_smarty_tpl->tpl_vars['slot']->total;
?>
                    <?php $_smarty_tpl->tpl_vars['display'] = new Smarty_variable(smarty_modifier_html(smarty_modifier_date_format($_smarty_tpl->tpl_vars['slot']->value->day,$_smarty_tpl->tpl_vars['date_format']->value['txt_month_year'])), null, 0);?>
                    <?php if ($_smarty_tpl->tpl_vars['previous']->value!==0&&$_smarty_tpl->tpl_vars['previous']->value!=$_smarty_tpl->tpl_vars['display']->value) {?>
                        <th colspan="<?php echo $_smarty_tpl->tpl_vars['count_same']->value;?>
" class="bg-primary month" id="M<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['previous']->value;?>
</th>
                        <?php $_smarty_tpl->tpl_vars['count_same'] = new Smarty_variable(0, null, 0);?>
                    <?php }?>

                    <?php $_smarty_tpl->tpl_vars['count_same'] = new Smarty_variable($_smarty_tpl->tpl_vars['count_same']->value+count($_smarty_tpl->tpl_vars['slot']->value->moments), null, 0);?>

                    <?php if ($_smarty_tpl->tpl_vars['slot']->last) {?>
                        <th colspan="<?php echo $_smarty_tpl->tpl_vars['count_same']->value;?>
" class="bg-primary month" id="M<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['display']->value;?>
</th>
                    <?php }?>

                    <?php $_smarty_tpl->tpl_vars['previous'] = new Smarty_variable($_smarty_tpl->tpl_vars['display']->value, null, 0);?>

                    <?php $_smarty_tpl->tpl_vars['foo'] = new Smarty_Variable;$_smarty_tpl->tpl_vars['foo']->step = 1;$_smarty_tpl->tpl_vars['foo']->total = (int) ceil(($_smarty_tpl->tpl_vars['foo']->step > 0 ? (count($_smarty_tpl->tpl_vars['slot']->value->moments))-1+1 - (0) : 0-((count($_smarty_tpl->tpl_vars['slot']->value->moments))-1)+1)/abs($_smarty_tpl->tpl_vars['foo']->step));
if ($_smarty_tpl->tpl_vars['foo']->total > 0) {
for ($_smarty_tpl->tpl_vars['foo']->value = 0, $_smarty_tpl->tpl_vars['foo']->iteration = 1;$_smarty_tpl->tpl_vars['foo']->iteration <= $_smarty_tpl->tpl_vars['foo']->total;$_smarty_tpl->tpl_vars['foo']->value += $_smarty_tpl->tpl_vars['foo']->step, $_smarty_tpl->tpl_vars['foo']->iteration++) {
$_smarty_tpl->tpl_vars['foo']->first = $_smarty_tpl->tpl_vars['foo']->iteration == 1;$_smarty_tpl->tpl_vars['foo']->last = $_smarty_tpl->tpl_vars['foo']->iteration == $_smarty_tpl->tpl_vars['foo']->total;?>
                        <?php $_smarty_tpl->createLocalArrayVariable('headersM', null, 0);
$_smarty_tpl->tpl_vars['headersM']->value[] = $_smarty_tpl->tpl_vars['id']->value;?>
                    <?php }} ?>
                <?php } ?>
                <th></th>
            </tr>
            <tr>
                <th role="presentation"></th>
                <?php  $_smarty_tpl->tpl_vars['slot'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['slot']->_loop = false;
 $_smarty_tpl->tpl_vars['id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['slots']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['slot']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['slot']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['slot']->key => $_smarty_tpl->tpl_vars['slot']->value) {
$_smarty_tpl->tpl_vars['slot']->_loop = true;
 $_smarty_tpl->tpl_vars['id']->value = $_smarty_tpl->tpl_vars['slot']->key;
 $_smarty_tpl->tpl_vars['slot']->iteration++;
 $_smarty_tpl->tpl_vars['slot']->last = $_smarty_tpl->tpl_vars['slot']->iteration === $_smarty_tpl->tpl_vars['slot']->total;
?>
                    <th colspan="<?php echo count($_smarty_tpl->tpl_vars['slot']->value->moments);?>
" class="bg-primary day" id="D<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
"><?php echo smarty_modifier_html(smarty_modifier_date_format($_smarty_tpl->tpl_vars['slot']->value->day,$_smarty_tpl->tpl_vars['date_format']->value['txt_day']));?>
</th>
                    <?php $_smarty_tpl->tpl_vars['foo'] = new Smarty_Variable;$_smarty_tpl->tpl_vars['foo']->step = 1;$_smarty_tpl->tpl_vars['foo']->total = (int) ceil(($_smarty_tpl->tpl_vars['foo']->step > 0 ? (count($_smarty_tpl->tpl_vars['slot']->value->moments))-1+1 - (0) : 0-((count($_smarty_tpl->tpl_vars['slot']->value->moments))-1)+1)/abs($_smarty_tpl->tpl_vars['foo']->step));
if ($_smarty_tpl->tpl_vars['foo']->total > 0) {
for ($_smarty_tpl->tpl_vars['foo']->value = 0, $_smarty_tpl->tpl_vars['foo']->iteration = 1;$_smarty_tpl->tpl_vars['foo']->iteration <= $_smarty_tpl->tpl_vars['foo']->total;$_smarty_tpl->tpl_vars['foo']->value += $_smarty_tpl->tpl_vars['foo']->step, $_smarty_tpl->tpl_vars['foo']->iteration++) {
$_smarty_tpl->tpl_vars['foo']->first = $_smarty_tpl->tpl_vars['foo']->iteration == 1;$_smarty_tpl->tpl_vars['foo']->last = $_smarty_tpl->tpl_vars['foo']->iteration == $_smarty_tpl->tpl_vars['foo']->total;?>
                        <?php $_smarty_tpl->createLocalArrayVariable('headersD', null, 0);
$_smarty_tpl->tpl_vars['headersD']->value[] = $_smarty_tpl->tpl_vars['id']->value;?>
                    <?php }} ?>
                <?php } ?>
                <th></th>
            </tr>
            <tr>
                <th role="presentation"></th>
                <?php $_smarty_tpl->tpl_vars['headersDCount'] = new Smarty_variable(0, null, 0);?>
                <?php $_smarty_tpl->tpl_vars['slots_raw'] = new Smarty_variable(array(), null, 0);?>
                <?php  $_smarty_tpl->tpl_vars['slot'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['slot']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['slots']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['slot']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['slot']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['slot']->key => $_smarty_tpl->tpl_vars['slot']->value) {
$_smarty_tpl->tpl_vars['slot']->_loop = true;
 $_smarty_tpl->tpl_vars['slot']->iteration++;
 $_smarty_tpl->tpl_vars['slot']->last = $_smarty_tpl->tpl_vars['slot']->iteration === $_smarty_tpl->tpl_vars['slot']->total;
?>
                    <?php  $_smarty_tpl->tpl_vars['moment'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['moment']->_loop = false;
 $_smarty_tpl->tpl_vars['id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['slot']->value->moments; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['moment']->key => $_smarty_tpl->tpl_vars['moment']->value) {
$_smarty_tpl->tpl_vars['moment']->_loop = true;
 $_smarty_tpl->tpl_vars['id']->value = $_smarty_tpl->tpl_vars['moment']->key;
?>
                        <th colspan="1" class="bg-info" id="H<?php echo $_smarty_tpl->tpl_vars['headersDCount']->value;?>
"><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['moment']->value);?>
</th>
                        <?php $_smarty_tpl->createLocalArrayVariable('headersH', null, 0);
$_smarty_tpl->tpl_vars['headersH']->value[] = $_smarty_tpl->tpl_vars['headersDCount']->value;?>
                        <?php $_smarty_tpl->tpl_vars['headersDCount'] = new Smarty_variable($_smarty_tpl->tpl_vars['headersDCount']->value+1, null, 0);?>
                        <?php $_smarty_tpl->createLocalArrayVariable('slots_raw', null, 0);
$_smarty_tpl->tpl_vars['slots_raw']->value[] = ((smarty_modifier_date_format($_smarty_tpl->tpl_vars['slot']->value->day,$_smarty_tpl->tpl_vars['date_format']->value['txt_full'])).(' - ')).($_smarty_tpl->tpl_vars['moment']->value);?>
                    <?php } ?>
                <?php } ?>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php  $_smarty_tpl->tpl_vars['vote'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['vote']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['votes']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['vote']->key => $_smarty_tpl->tpl_vars['vote']->value) {
$_smarty_tpl->tpl_vars['vote']->_loop = true;
?>
                

                <?php if ($_smarty_tpl->tpl_vars['editingVoteId']->value===$_smarty_tpl->tpl_vars['vote']->value->uniqId&&!$_smarty_tpl->tpl_vars['expired']->value) {?>
                <tr class="hidden-print">
                    <td class="bg-info" style="padding:5px">
                        <div class="input-group input-group-sm" id="edit">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input type="hidden" name="edited_vote" value="<?php echo $_smarty_tpl->tpl_vars['vote']->value->uniqId;?>
"/>
                            <input type="text" id="name" name="name" value="<?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['vote']->value->name);?>
" class="form-control" title="<?php echo __('Generic','Your name');?>
" placeholder="<?php echo __('Generic','Your name');?>
" />
                        </div>
                    </td>

                    <?php $_smarty_tpl->tpl_vars['k'] = new Smarty_variable(0, null, 0);?>
                    <?php  $_smarty_tpl->tpl_vars['slot'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['slot']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['slots']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['slot']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['slot']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['slot']->key => $_smarty_tpl->tpl_vars['slot']->value) {
$_smarty_tpl->tpl_vars['slot']->_loop = true;
 $_smarty_tpl->tpl_vars['slot']->iteration++;
 $_smarty_tpl->tpl_vars['slot']->last = $_smarty_tpl->tpl_vars['slot']->iteration === $_smarty_tpl->tpl_vars['slot']->total;
?>
                      <?php  $_smarty_tpl->tpl_vars['moment'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['moment']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['slot']->value->moments; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['moment']->key => $_smarty_tpl->tpl_vars['moment']->value) {
$_smarty_tpl->tpl_vars['moment']->_loop = true;
?>
                        <?php $_smarty_tpl->tpl_vars['choice'] = new Smarty_variable($_smarty_tpl->tpl_vars['vote']->value->choices[$_smarty_tpl->tpl_vars['k']->value], null, 0);?>


                        <td class="bg-info" headers="M<?php echo $_smarty_tpl->tpl_vars['headersM']->value[$_smarty_tpl->tpl_vars['k']->value];?>
 D<?php echo $_smarty_tpl->tpl_vars['headersD']->value[$_smarty_tpl->tpl_vars['k']->value];?>
 H<?php echo $_smarty_tpl->tpl_vars['headersH']->value[$_smarty_tpl->tpl_vars['k']->value];?>
">
                            <ul class="list-unstyled choice">
                                <li class="yes">
                                    <input type="radio" id="y-choice-<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" name="choices[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
]" value="2" <?php if ($_smarty_tpl->tpl_vars['choice']->value=='2') {?>checked <?php }?>/>
                                    <label class="btn btn-default btn-xs" for="y-choice-<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" title="<?php echo smarty_modifier_html(__('Poll results','Vote yes for'));?>
 <?php echo $_smarty_tpl->tpl_vars['slots_raw']->value[$_smarty_tpl->tpl_vars['k']->value];?>
">
                                        <i class="glyphicon glyphicon-ok"></i><span class="sr-only"><?php echo __('Generic','Yes');?>
</span>
                                    </label>
                                </li>
                                <li class="ifneedbe">
                                    <input type="radio" id="i-choice-<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" name="choices[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
]" value="1" <?php if ($_smarty_tpl->tpl_vars['choice']->value=='1') {?>checked <?php }?>/>
                                    <label class="btn btn-default btn-xs" for="i-choice-<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" title="<?php echo smarty_modifier_html(__('Poll results','Vote ifneedbe for'));?>
 <?php echo $_smarty_tpl->tpl_vars['slots_raw']->value[$_smarty_tpl->tpl_vars['k']->value];?>
">
                                        (<i class="glyphicon glyphicon-ok"></i>)<span class="sr-only"><?php echo __('Generic','Ifneedbe');?>
</span>
                                    </label>
                                </li>
                                <li class="no">
                                    <input type="radio" id="n-choice-<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" name="choices[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
]" value="0" <?php if ($_smarty_tpl->tpl_vars['choice']->value=='0') {?>checked <?php }?>/>
                                    <label class="btn btn-default btn-xs" for="n-choice-<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" title="<?php echo smarty_modifier_html(__('Poll results','Vote no for'));?>
 <?php echo $_smarty_tpl->tpl_vars['slots_raw']->value[$_smarty_tpl->tpl_vars['k']->value];?>
">
                                        <i class="glyphicon glyphicon-ban-circle"></i><span class="sr-only"><?php echo __('Generic','No');?>
</span>
                                    </label>
                                </li>
                                <li style="display:none">
                                    <input type="radio" id="n-choice-<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" name="choices[<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
]" value=" " <?php if ($_smarty_tpl->tpl_vars['choice']->value!='2'&&$_smarty_tpl->tpl_vars['choice']->value!='1'&&$_smarty_tpl->tpl_vars['choice']->value!='0') {?>checked <?php }?>/>
                                </li>
                            </ul>
                        </td>

                        <?php $_smarty_tpl->tpl_vars['k'] = new Smarty_variable($_smarty_tpl->tpl_vars['k']->value+1, null, 0);?>
                      <?php } ?>
                    <?php } ?>

                    <td style="padding:5px"><button type="submit" class="btn btn-success btn-xs" name="save" value="<?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['vote']->value->id);?>
" title="<?php echo __('Poll results','Save the choices');?>
 <?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['vote']->value->name);?>
"><?php echo __('Generic','Save');?>
</button></td>

                </tr>
                <?php } elseif (!$_smarty_tpl->tpl_vars['hidden']->value) {?>
                <tr>

                    

                    <th class="bg-info"><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['vote']->value->name);?>
</th>

                    <?php $_smarty_tpl->tpl_vars['k'] = new Smarty_variable(0, null, 0);?>
                    <?php  $_smarty_tpl->tpl_vars['slot'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['slot']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['slots']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['slot']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['slot']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['slot']->key => $_smarty_tpl->tpl_vars['slot']->value) {
$_smarty_tpl->tpl_vars['slot']->_loop = true;
 $_smarty_tpl->tpl_vars['slot']->iteration++;
 $_smarty_tpl->tpl_vars['slot']->last = $_smarty_tpl->tpl_vars['slot']->iteration === $_smarty_tpl->tpl_vars['slot']->total;
?>
                      <?php  $_smarty_tpl->tpl_vars['moment'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['moment']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['slot']->value->moments; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['moment']->key => $_smarty_tpl->tpl_vars['moment']->value) {
$_smarty_tpl->tpl_vars['moment']->_loop = true;
?>
                        <?php $_smarty_tpl->tpl_vars['choice'] = new Smarty_variable($_smarty_tpl->tpl_vars['vote']->value->choices[$_smarty_tpl->tpl_vars['k']->value], null, 0);?>

                        <?php if ($_smarty_tpl->tpl_vars['choice']->value=='2') {?>
                            <td class="bg-success text-success" headers="M<?php echo $_smarty_tpl->tpl_vars['headersM']->value[$_smarty_tpl->tpl_vars['k']->value];?>
 D<?php echo $_smarty_tpl->tpl_vars['headersD']->value[$_smarty_tpl->tpl_vars['k']->value];?>
 H<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
"><i class="glyphicon glyphicon-ok"></i><span class="sr-only"><?php echo __('Generic','Yes');?>
</span></td>
                        <?php } elseif ($_smarty_tpl->tpl_vars['choice']->value=='1') {?>
                            <td class="bg-warning text-warning" headers="M<?php echo $_smarty_tpl->tpl_vars['headersM']->value[$_smarty_tpl->tpl_vars['k']->value];?>
 D<?php echo $_smarty_tpl->tpl_vars['headersD']->value[$_smarty_tpl->tpl_vars['k']->value];?>
 H<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
">(<i class="glyphicon glyphicon-ok"></i>)<span class="sr-only"><?php echo __('Generic','Ifneedbe');?>
</span></td>
                        <?php } elseif ($_smarty_tpl->tpl_vars['choice']->value=='0') {?>
                            <td class="bg-danger text-danger" headers="M<?php echo $_smarty_tpl->tpl_vars['headersM']->value[$_smarty_tpl->tpl_vars['k']->value];?>
 D<?php echo $_smarty_tpl->tpl_vars['headersD']->value[$_smarty_tpl->tpl_vars['k']->value];?>
 H<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
"><i class="glyphicon glyphicon-ban-circle"></i><span class="sr-only"><?php echo __('Generic','No');?>
</span></td>
                        <?php } else { ?>
                            <td class="bg-info" headers="M<?php echo $_smarty_tpl->tpl_vars['headersM']->value[$_smarty_tpl->tpl_vars['k']->value];?>
 D<?php echo $_smarty_tpl->tpl_vars['headersD']->value[$_smarty_tpl->tpl_vars['k']->value];?>
 H<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
"><span class="sr-only"><?php echo __('Generic','Unknown');?>
</span></td>
                        <?php }?>

                        <?php $_smarty_tpl->tpl_vars['k'] = new Smarty_variable($_smarty_tpl->tpl_vars['k']->value+1, null, 0);?>
                      <?php } ?>
                    <?php } ?>

                    <?php if ($_smarty_tpl->tpl_vars['active']->value&&!$_smarty_tpl->tpl_vars['expired']->value&&$_smarty_tpl->tpl_vars['accessGranted']->value&&($_smarty_tpl->tpl_vars['poll']->value->editable==constant('Framadate\Editable::EDITABLE_BY_ALL')||$_smarty_tpl->tpl_vars['admin']->value||($_smarty_tpl->tpl_vars['poll']->value->editable==constant('Framadate\Editable::EDITABLE_BY_OWN')&&$_smarty_tpl->tpl_vars['editedVoteUniqueId']->value==$_smarty_tpl->tpl_vars['vote']->value->uniqId))) {?>
                        <td class="hidden-print">
                            <a href="<?php if ($_smarty_tpl->tpl_vars['admin']->value) {
echo smarty_function_poll_url(array('id'=>$_smarty_tpl->tpl_vars['poll']->value->admin_id,'vote_id'=>$_smarty_tpl->tpl_vars['vote']->value->uniqId,'admin'=>true),$_smarty_tpl);
} else {
echo smarty_function_poll_url(array('id'=>$_smarty_tpl->tpl_vars['poll']->value->id,'vote_id'=>$_smarty_tpl->tpl_vars['vote']->value->uniqId),$_smarty_tpl);
}?>" class="btn btn-default btn-sm" title="<?php echo smarty_modifier_html(__f('Poll results','Edit the line: %s',$_smarty_tpl->tpl_vars['vote']->value->name));?>
">
                                <i class="glyphicon glyphicon-pencil"></i><span class="sr-only"><?php echo __('Generic','Edit');?>
</span>
                            </a>
                            <?php if ($_smarty_tpl->tpl_vars['admin']->value) {?>
                                <a href="<?php echo smarty_function_poll_url(array('id'=>$_smarty_tpl->tpl_vars['admin_poll_id']->value,'admin'=>true,'action'=>'delete_vote','action_value'=>$_smarty_tpl->tpl_vars['vote']->value->id),$_smarty_tpl);?>
"
                                   class="btn btn-default btn-sm"
                                   title="<?php echo __('Poll results','Remove the line:');?>
 <?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['vote']->value->name);?>
">
                                    <i class="glyphicon glyphicon-remove text-danger"></i><span class="sr-only"><?php echo __('Generic','Remove');?>
</span>
                                </a>
                            <?php }?>
                        </td>
                    <?php } else { ?>
                        <td></td>
                    <?php }?>
                </tr>
                <?php }?>
            <?php } ?>

            

            <?php if ($_smarty_tpl->tpl_vars['active']->value&&$_smarty_tpl->tpl_vars['editingVoteId']->value===0&&!$_smarty_tpl->tpl_vars['expired']->value&&$_smarty_tpl->tpl_vars['accessGranted']->value) {?>
                <tr id="vote-form" class="hidden-print">
                    <td class="bg-info" style="padding:5px">
                        <div class="input-group input-group-sm">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input type="text" id="name" name="name" class="form-control" title="<?php echo __('Generic','Your name');?>
" placeholder="<?php echo __('Generic','Your name');?>
" />
                        </div>
                    </td>
			
			<?php $_smarty_tpl->tpl_vars['MAX_VOTE'] = new Smarty_variable(3, null, 0);?>
			
				
			
                    <?php $_smarty_tpl->tpl_vars['i'] = new Smarty_variable(0, null, 0);?>
		

                    <?php  $_smarty_tpl->tpl_vars['slot'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['slot']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['slots']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['slot']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['slot']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['slot']->key => $_smarty_tpl->tpl_vars['slot']->value) {
$_smarty_tpl->tpl_vars['slot']->_loop = true;
 $_smarty_tpl->tpl_vars['slot']->iteration++;
 $_smarty_tpl->tpl_vars['slot']->last = $_smarty_tpl->tpl_vars['slot']->iteration === $_smarty_tpl->tpl_vars['slot']->total;
?> 
                        <?php  $_smarty_tpl->tpl_vars['moment'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['moment']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['slot']->value->moments; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['moment']->key => $_smarty_tpl->tpl_vars['moment']->value) {
$_smarty_tpl->tpl_vars['moment']->_loop = true;
?>

				

                            <td class="bg-info" headers="M<?php echo $_smarty_tpl->tpl_vars['headersM']->value[$_smarty_tpl->tpl_vars['i']->value];?>
 D<?php echo $_smarty_tpl->tpl_vars['headersD']->value[$_smarty_tpl->tpl_vars['i']->value];?>
 H<?php echo $_smarty_tpl->tpl_vars['headersH']->value[$_smarty_tpl->tpl_vars['i']->value];?>
">
                                <ul class="list-unstyled choice">
				<?php if ($_smarty_tpl->tpl_vars['best_choices']->value['y'][$_smarty_tpl->tpl_vars['i']->value]<$_smarty_tpl->tpl_vars['poll']->value->ValueMax) {?>
                                    <li class="yes">
                                        <input type="radio" id="y-choice-<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
" name="choices[<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
]" value="2" />
                                        <label class="btn btn-default btn-xs" for="y-choice-<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
" title="<?php echo smarty_modifier_html(__('Poll results','Vote yes for'));?>
 <?php echo smarty_modifier_html(smarty_modifier_date_format($_smarty_tpl->tpl_vars['slot']->value->day,$_smarty_tpl->tpl_vars['date_format']->value['txt_short']));?>
 - <?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['moment']->value);?>
">
                                            <i class="glyphicon glyphicon-ok"></i><span class="sr-only"><?php echo __('Generic','Yes');?>
</span>
                                        </label>
                                    </li>
                                    <li class="ifneedbe">
                                        <input type="radio" id="i-choice-<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
" name="choices[<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
]" value="1" />
                                        <label class="btn btn-default btn-xs" for="i-choice-<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
" title="<?php echo smarty_modifier_html(__('Poll results','Vote ifneedbe for'));?>
 <?php echo smarty_modifier_html(smarty_modifier_date_format($_smarty_tpl->tpl_vars['slot']->value->day,$_smarty_tpl->tpl_vars['date_format']->value['txt_short']));?>
 - <?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['moment']->value);?>
">
                                            (<i class="glyphicon glyphicon-ok"></i>)<span class="sr-only"><?php echo __('Generic','Ifneedbe');?>
</span>
                                        </label>
                                    </li>
					
					<?php }?>

                                    <li class="no">
                                        <input type="radio" id="n-choice-<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
" name="choices[<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
]" value="0" />
                                        <label class="btn btn-default btn-xs startunchecked" for="n-choice-<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
" title="<?php echo smarty_modifier_html(__('Poll results','Vote no for'));?>
 <?php echo smarty_modifier_html(smarty_modifier_date_format($_smarty_tpl->tpl_vars['slot']->value->day,$_smarty_tpl->tpl_vars['date_format']->value['txt_short']));?>
 - <?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['moment']->value);?>
">
                                            <i class="glyphicon glyphicon-ban-circle"></i><span class="sr-only"><?php echo __('Generic','No');?>
</span>
                                        </label>
                                    </li>
                                    <li style="display:none">
                                      <input type="radio" id="n-choice-<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
" name="choices[<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
]" value=" " checked/>
                                    </li>
                                </ul>
                            </td>

				

                            <?php $_smarty_tpl->tpl_vars['i'] = new Smarty_variable($_smarty_tpl->tpl_vars['i']->value+1, null, 0);?>
                        <?php } ?>
                    <?php } ?>
                    <td><button type="submit" class="btn btn-success btn-md" name="save" title="<?php echo __('Poll results','Save the choices');?>
"><?php echo __('Generic','Save');?>
</button></td>
                </tr>
            <?php }?>

            <?php if (!$_smarty_tpl->tpl_vars['hidden']->value) {?>
                
                <?php $_smarty_tpl->tpl_vars['count_bests'] = new Smarty_variable(0, null, 0);?>
                <?php $_smarty_tpl->tpl_vars['max'] = new Smarty_variable(max($_smarty_tpl->tpl_vars['best_choices']->value['y']), null, 0);?>
                <?php if ($_smarty_tpl->tpl_vars['max']->value>0) {?>
                    <tr id="addition">
                        <td><?php echo __('Poll results','Addition');?>
<br/><?php echo count($_smarty_tpl->tpl_vars['votes']->value);?>
 <?php if ((count($_smarty_tpl->tpl_vars['votes']->value))==1) {
echo __('Poll results','polled user');
} else {
echo __('Poll results','polled users');
}?></td>
                        <?php  $_smarty_tpl->tpl_vars['best_moment'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['best_moment']->_loop = false;
 $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['best_choices']->value['y']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['best_moment']->key => $_smarty_tpl->tpl_vars['best_moment']->value) {
$_smarty_tpl->tpl_vars['best_moment']->_loop = true;
 $_smarty_tpl->tpl_vars['i']->value = $_smarty_tpl->tpl_vars['best_moment']->key;
?>
                            <?php if ($_smarty_tpl->tpl_vars['max']->value==$_smarty_tpl->tpl_vars['best_moment']->value) {?>
                                <?php $_smarty_tpl->tpl_vars['count_bests'] = new Smarty_variable($_smarty_tpl->tpl_vars['count_bests']->value+1, null, 0);?>
                                <td><i class="glyphicon glyphicon-star text-info"></i><span class="yes-count"><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['best_moment']->value);?>
</span><?php if ($_smarty_tpl->tpl_vars['best_choices']->value['inb'][$_smarty_tpl->tpl_vars['i']->value]>0) {?><br/><span class="small text-muted">(+<span class="inb-count"><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['best_choices']->value['inb'][$_smarty_tpl->tpl_vars['i']->value]);?>
</span>)</span><?php }?></td>
                            <?php } elseif ($_smarty_tpl->tpl_vars['best_moment']->value>0) {?>
                                <td><span class="yes-count"><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['best_moment']->value);?>
</span><?php if ($_smarty_tpl->tpl_vars['best_choices']->value['inb'][$_smarty_tpl->tpl_vars['i']->value]>0) {?><br/><span class="small text-muted">(+<span class="inb-count"><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['best_choices']->value['inb'][$_smarty_tpl->tpl_vars['i']->value]);?>
</span>)</span><?php }?></td>
                            <?php } elseif ($_smarty_tpl->tpl_vars['best_choices']->value['inb'][$_smarty_tpl->tpl_vars['i']->value]>0) {?>
                                <td><br/><span class="small text-muted">(+<span class="inb-count"><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['best_choices']->value['inb'][$_smarty_tpl->tpl_vars['i']->value]);?>
</span>)</span></td>
                            <?php } else { ?>
                                <td></td>
                            <?php }?>
                        <?php } ?>
                    </tr>
                <?php }?>
            <?php }?>
            </tbody>
        </table>
    </form>
</div>

<?php if (!$_smarty_tpl->tpl_vars['hidden']->value&&$_smarty_tpl->tpl_vars['max']->value>0) {?>
    <div class="row" aria-hidden="true">
        <div class="col-xs-12">
            <p class="text-center" id="showChart">
                <button class="btn btn-lg btn-default">
                    <span class="fa fa-fw fa-bar-chart"></span> <?php echo __('Poll results','Display the chart of the results');?>

                </button>
            </p>
        </div>
    </div>
    <?php echo '<script'; ?>
 type="text/javascript">
        $(document).ready(function () {
            $('#showChart').on('click', function() {
                $('#showChart')
                        .after("<h3><?php echo __('Poll results','Chart');?>
</h3><canvas id=\"Chart\"></canvas>")
                        .remove();
                               
                var resIfneedbe = [];
                var resYes = [];
            
                $('#addition').find('td').each(function () {
                    var inbCountText = $(this).find('.inb-count').text();
                    if(inbCountText != '' && inbCountText != undefined) {
                        resIfneedbe.push(inbCountText)
                    } else {
                        resIfneedbe.push(0);
                    }
                    var yesCountText = $(this).find('.yes-count').text();
                    if(yesCountText != '' && yesCountText != undefined) {
                        resYes.push(yesCountText)
                    } else {
                        resYes.push(0);
                    }
                });
                var cols = [
                <?php  $_smarty_tpl->tpl_vars['slot'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['slot']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['slots']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['slot']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['slot']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['slot']->key => $_smarty_tpl->tpl_vars['slot']->value) {
$_smarty_tpl->tpl_vars['slot']->_loop = true;
 $_smarty_tpl->tpl_vars['slot']->iteration++;
 $_smarty_tpl->tpl_vars['slot']->last = $_smarty_tpl->tpl_vars['slot']->iteration === $_smarty_tpl->tpl_vars['slot']->total;
?>
                    <?php  $_smarty_tpl->tpl_vars['moment'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['moment']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['slot']->value->moments; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['moment']->key => $_smarty_tpl->tpl_vars['moment']->value) {
$_smarty_tpl->tpl_vars['moment']->_loop = true;
?>
                        $('<div/>').html('<?php echo smarty_modifier_html(smarty_modifier_date_format($_smarty_tpl->tpl_vars['slot']->value->day,$_smarty_tpl->tpl_vars['date_format']->value['txt_short']));?>
 - <?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['moment']->value);?>
').text(), 
                    <?php } ?>
                <?php } ?>
                ];

                resIfneedbe.shift();
                resYes.shift();

                var barChartData = {
                    labels : cols,
                    datasets : [
                    {
                        label: "<?php echo __('Generic','Ifneedbe');?>
",
                        fillColor : "rgba(255,207,79,0.8)",
                        highlightFill: "rgba(255,207,79,1)",
                        barShowStroke : false,
                        data : resIfneedbe
                    },
                    {
                        label: "<?php echo __('Generic','Yes');?>
",
                        fillColor : "rgba(103,120,53,0.8)",
                        highlightFill : "rgba(103,120,53,1)",
                        barShowStroke : false,
                        data : resYes
                    }
                    ]
                };

                var ctx = document.getElementById("Chart").getContext("2d");
                window.myBar = new Chart(ctx).StackedBar(barChartData, {
                    responsive : true
                });
                return false;
            });
        });
    <?php echo '</script'; ?>
>
    
<?php }?>

<?php if (!$_smarty_tpl->tpl_vars['hidden']->value) {?>
    
    <?php $_smarty_tpl->tpl_vars['max'] = new Smarty_variable(max($_smarty_tpl->tpl_vars['best_choices']->value['y']), null, 0);?>
    <?php if ($_smarty_tpl->tpl_vars['max']->value>0) {?>
        <div class="row">
        <?php if ($_smarty_tpl->tpl_vars['count_bests']->value==1) {?>
        <div class="col-sm-12"><h3><?php echo __('Poll results','Best choice');?>
</h3></div>
        <div class="col-sm-6 col-sm-offset-3 alert alert-info">
            <p><i class="glyphicon glyphicon-star text-info"></i> <?php echo __('Poll results','The best choice at this time is:');?>
</p>
            <?php } elseif ($_smarty_tpl->tpl_vars['count_bests']->value>1) {?>
            <div class="col-sm-12"><h3><?php echo __('Poll results','Best choices');?>
</h3></div>
            <div class="col-sm-6 col-sm-offset-3 alert alert-info">
                <p><i class="glyphicon glyphicon-star text-info"></i> <?php echo __('Poll results','The bests choices at this time are:');?>
</p>
                <?php }?>


                <?php $_smarty_tpl->tpl_vars['i'] = new Smarty_variable(0, null, 0);?>
                <ul style="list-style:none">
                    <?php  $_smarty_tpl->tpl_vars['slot'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['slot']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['slots']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['slot']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['slot']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['slot']->key => $_smarty_tpl->tpl_vars['slot']->value) {
$_smarty_tpl->tpl_vars['slot']->_loop = true;
 $_smarty_tpl->tpl_vars['slot']->iteration++;
 $_smarty_tpl->tpl_vars['slot']->last = $_smarty_tpl->tpl_vars['slot']->iteration === $_smarty_tpl->tpl_vars['slot']->total;
?>
                        <?php  $_smarty_tpl->tpl_vars['moment'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['moment']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['slot']->value->moments; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['moment']->key => $_smarty_tpl->tpl_vars['moment']->value) {
$_smarty_tpl->tpl_vars['moment']->_loop = true;
?>
                            <?php if ($_smarty_tpl->tpl_vars['best_choices']->value['y'][$_smarty_tpl->tpl_vars['i']->value]==$_smarty_tpl->tpl_vars['max']->value) {?>
                                <li><strong><?php echo smarty_modifier_html(smarty_modifier_date_format($_smarty_tpl->tpl_vars['slot']->value->day,$_smarty_tpl->tpl_vars['date_format']->value['txt_full']));?>
 - <?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['moment']->value);?>
</strong></li>
                            <?php }?>
                            <?php $_smarty_tpl->tpl_vars['i'] = new Smarty_variable($_smarty_tpl->tpl_vars['i']->value+1, null, 0);?>
                        <?php } ?>
                    <?php } ?>
                </ul>
                <p><?php echo __('Generic','with');?>
 <b><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['max']->value);?>
</b> <?php if ($_smarty_tpl->tpl_vars['max']->value==1) {
echo __('Generic','vote');
} else {
echo __('Generic','votes');
}?>.</p>
            </div>
        </div>
    <?php }?>
<?php }?>
<?php }} ?>
<?php /* Smarty version Smarty-3.1.21, created on 2017-11-02 17:57:24
         compiled from "/var/www/framadate//tpl/part/vote_table_classic.tpl" */ ?>
<?php if ($_valid && !is_callable('content_59fb4e74355aa7_76270587')) {function content_59fb4e74355aa7_76270587($_smarty_tpl) {?><?php if (!is_array($_smarty_tpl->tpl_vars['best_choices']->value)||empty($_smarty_tpl->tpl_vars['best_choices']->value)) {?>
    <?php $_smarty_tpl->tpl_vars['best_choices'] = new Smarty_variable(array(0), null, 0);?>
<?php }?>

<h3>
    <?php echo __('Poll results','Votes of the poll');?>
 <?php if ($_smarty_tpl->tpl_vars['hidden']->value) {?><i>(<?php echo __('PollInfo','Results are hidden');?>
)</i><?php }?>
    <?php if ($_smarty_tpl->tpl_vars['accessGranted']->value) {?>
        <a href="" data-toggle="modal" data-target="#hint_modal"><i class="glyphicon glyphicon-info-sign"></i></a><!-- TODO Add accessibility -->
    <?php }?>
</h3>

<div id="tableContainer" class="tableContainer">
    <form action="<?php if ($_smarty_tpl->tpl_vars['admin']->value) {
echo smarty_function_poll_url(array('id'=>$_smarty_tpl->tpl_vars['admin_poll_id']->value,'admin'=>true),$_smarty_tpl);
} else {
echo smarty_function_poll_url(array('id'=>$_smarty_tpl->tpl_vars['poll_id']->value),$_smarty_tpl);
}?>" method="POST"  id="poll_form">
        <input type="hidden" name="control" value="<?php echo $_smarty_tpl->tpl_vars['slots_hash']->value;?>
"/>
        <table class="results">
            <caption class="sr-only"><?php echo __('Poll results','Votes of the poll');?>
 <?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['poll']->value->title);?>
</caption>
            <thead>
            <?php if ($_smarty_tpl->tpl_vars['admin']->value&&!$_smarty_tpl->tpl_vars['expired']->value) {?>
                <tr class="hidden-print">
                    <th role="presentation"></th>
                    <?php  $_smarty_tpl->tpl_vars['slot'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['slot']->_loop = false;
 $_smarty_tpl->tpl_vars['id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['slots']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['slot']->key => $_smarty_tpl->tpl_vars['slot']->value) {
$_smarty_tpl->tpl_vars['slot']->_loop = true;
 $_smarty_tpl->tpl_vars['id']->value = $_smarty_tpl->tpl_vars['slot']->key;
?>
                        <td headers="C<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
">
                            <a href="<?php echo smarty_function_poll_url(array('id'=>$_smarty_tpl->tpl_vars['admin_poll_id']->value,'admin'=>true,'action'=>'delete_column','action_value'=>$_smarty_tpl->tpl_vars['slot']->value->title),$_smarty_tpl);?>
"
                               data-remove-confirmation="<?php echo __('adminstuds','Confirm removal of the column.');?>
"
                               class="btn btn-link btn-sm remove-column" title="<?php echo __('adminstuds','Remove the column');?>
 <?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['slot']->value->title);?>
">
                                <i class="glyphicon glyphicon-remove text-danger"></i><span class="sr-only"><?php echo __('Generic','Remove');?>
</span>
                            </a>
                            </td>
                    <?php } ?>
                    <td>
                        <a href="<?php echo smarty_function_poll_url(array('id'=>$_smarty_tpl->tpl_vars['admin_poll_id']->value,'admin'=>true,'action'=>'add_column'),$_smarty_tpl);?>
"
                           class="btn btn-link btn-sm" title="<?php echo __('adminstuds','Add a column');?>
">
                            <i class="glyphicon glyphicon-plus text-success"></i><span class="sr-only"><?php echo __('Poll results','Add a column');?>
</span>
                        </a>
                    </td>
                </tr>
            <?php }?>
            <tr>
                <th role="presentation"></th>
                <?php  $_smarty_tpl->tpl_vars['slot'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['slot']->_loop = false;
 $_smarty_tpl->tpl_vars['id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['slots']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['slot']->key => $_smarty_tpl->tpl_vars['slot']->value) {
$_smarty_tpl->tpl_vars['slot']->_loop = true;
 $_smarty_tpl->tpl_vars['id']->value = $_smarty_tpl->tpl_vars['slot']->key;
?>
                    <th class="bg-info" id="C<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" title="<?php echo smarty_modifier_markdown($_smarty_tpl->tpl_vars['slot']->value->title,true);?>
"><?php echo smarty_modifier_markdown($_smarty_tpl->tpl_vars['slot']->value->title);?>
</th>
                <?php } ?>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php  $_smarty_tpl->tpl_vars['vote'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['vote']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['votes']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['vote']->key => $_smarty_tpl->tpl_vars['vote']->value) {
$_smarty_tpl->tpl_vars['vote']->_loop = true;
?>
                

                <?php if ($_smarty_tpl->tpl_vars['editingVoteId']->value===$_smarty_tpl->tpl_vars['vote']->value->uniqId&&!$_smarty_tpl->tpl_vars['expired']->value) {?>

                <tr class="hidden-print">
                    <td class="bg-info" style="padding:5px">
                        <div class="input-group input-group-sm" id="edit">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input type="hidden" name="edited_vote" value="<?php echo $_smarty_tpl->tpl_vars['vote']->value->uniqId;?>
"/>
                            <input type="text" id="name" name="name" value="<?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['vote']->value->name);?>
" class="form-control" title="<?php echo __('Generic','Your name');?>
" placeholder="<?php echo __('Generic','Your name');?>
" />
                        </div>
                    </td>

                    <?php $_smarty_tpl->tpl_vars['id'] = new Smarty_variable(0, null, 0);?>
                    <?php  $_smarty_tpl->tpl_vars['slot'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['slot']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['slots']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['slot']->key => $_smarty_tpl->tpl_vars['slot']->value) {
$_smarty_tpl->tpl_vars['slot']->_loop = true;
?>
                        <?php $_smarty_tpl->tpl_vars['choice'] = new Smarty_variable($_smarty_tpl->tpl_vars['vote']->value->choices[$_smarty_tpl->tpl_vars['id']->value], null, 0);?>

                        <td class="bg-info" headers="C<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
">
                            <ul class="list-unstyled choice">
                                <li class="yes">
                                    <input type="radio" id="y-choice-<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" name="choices[<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
]" value="2" <?php if ($_smarty_tpl->tpl_vars['choice']->value=='2') {?>checked <?php }?>/>
                                    <label class="btn btn-default btn-xs" for="y-choice-<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" title="<?php echo smarty_modifier_html(__('Poll results','Vote yes for'));?>
 <?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['slots']->value[$_smarty_tpl->tpl_vars['id']->value]->title);?>
">
                                        <i class="glyphicon glyphicon-ok"></i><span class="sr-only"><?php echo __('Generic','Yes');?>
</span>
                                    </label>
                                </li>
                                <li class="ifneedbe">
                                    <input type="radio" id="i-choice-<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" name="choices[<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
]" value="1" <?php if ($_smarty_tpl->tpl_vars['choice']->value=='1') {?>checked <?php }?>/>
                                    <label class="btn btn-default btn-xs" for="i-choice-<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" title="<?php echo smarty_modifier_html(__('Poll results','Vote ifneedbe for'));?>
 <?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['slots']->value[$_smarty_tpl->tpl_vars['id']->value]->title);?>
">
                                        (<i class="glyphicon glyphicon-ok"></i>)<span class="sr-only"><?php echo __('Generic','Ifneedbe');?>
</span>
                                    </label>
                                </li>
                                <li class="no">
                                    <input type="radio" id="n-choice-<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" name="choices[<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
]" value="0" <?php if ($_smarty_tpl->tpl_vars['choice']->value=='0') {?>checked <?php }?>/>
                                    <label class="btn btn-default btn-xs" for="n-choice-<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" title="<?php echo smarty_modifier_html(__('Poll results','Vote no for'));?>
 <?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['slots']->value[$_smarty_tpl->tpl_vars['id']->value]->title);?>
">
                                        <i class="glyphicon glyphicon-ban-circle"></i><span class="sr-only"><?php echo __('Generic','No');?>
</span>
                                    </label>
                                </li>
                                <li style="display:none">
                                    <input type="radio" id="n-choice-<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" name="choices[<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
]" value=" " <?php if ($_smarty_tpl->tpl_vars['choice']->value!='2'&&$_smarty_tpl->tpl_vars['choice']->value!='1'&&$_smarty_tpl->tpl_vars['choice']->value!='0') {?>checked <?php }?>/>
                                </li>
                            </ul>
                        </td>

                        <?php $_smarty_tpl->tpl_vars['id'] = new Smarty_variable($_smarty_tpl->tpl_vars['id']->value+1, null, 0);?>
                    <?php } ?>

                    <td style="padding:5px"><button type="submit" class="btn btn-success btn-xs" name="save" value="<?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['vote']->value->id);?>
" title="<?php echo __('Poll results','Save the choices');?>
 <?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['vote']->value->name);?>
"><?php echo __('Generic','Save');?>
</button></td>
                </tr>
                <?php } elseif (!$_smarty_tpl->tpl_vars['hidden']->value) {?> 
                <tr>

                    <th class="bg-info"><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['vote']->value->name);?>
</th>

                    <?php $_smarty_tpl->tpl_vars['id'] = new Smarty_variable(0, null, 0);?>
                    <?php  $_smarty_tpl->tpl_vars['slot'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['slot']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['slots']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['slot']->key => $_smarty_tpl->tpl_vars['slot']->value) {
$_smarty_tpl->tpl_vars['slot']->_loop = true;
?>
                        <?php $_smarty_tpl->tpl_vars['choice'] = new Smarty_variable($_smarty_tpl->tpl_vars['vote']->value->choices[$_smarty_tpl->tpl_vars['id']->value], null, 0);?>

                        <?php if ($_smarty_tpl->tpl_vars['choice']->value=='2') {?>
                            <td class="bg-success text-success" headers="C<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
"><i class="glyphicon glyphicon-ok"></i><span class="sr-only"><?php echo __('Generic','Yes');?>
</span></td>
                        <?php } elseif ($_smarty_tpl->tpl_vars['choice']->value=='1') {?>
                            <td class="bg-warning text-warning" headers="C<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
">(<i class="glyphicon glyphicon-ok"></i>)<span class="sr-only"><?php echo __('Generic','Ifneedbe');?>
</span></td>
                        <?php } elseif ($_smarty_tpl->tpl_vars['choice']->value=='0') {?>
                            <td class="bg-danger text-danger" headers="C<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
"><i class="glyphicon glyphicon-ban-circle"></i><span class="sr-only"><?php echo __('Generic','No');?>
</span></td>
                        <?php } else { ?>
                            <td class="bg-info" headers="C<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
"><span class="sr-only"><?php echo __('Generic','Unknown');?>
</span></td>
                        <?php }?>

                        <?php $_smarty_tpl->tpl_vars['id'] = new Smarty_variable($_smarty_tpl->tpl_vars['id']->value+1, null, 0);?>
                    <?php } ?>

                    <?php if ($_smarty_tpl->tpl_vars['active']->value&&!$_smarty_tpl->tpl_vars['expired']->value&&$_smarty_tpl->tpl_vars['accessGranted']->value&&($_smarty_tpl->tpl_vars['poll']->value->editable==constant('Framadate\Editable::EDITABLE_BY_ALL')||$_smarty_tpl->tpl_vars['admin']->value||($_smarty_tpl->tpl_vars['poll']->value->editable==constant('Framadate\Editable::EDITABLE_BY_OWN')&&$_smarty_tpl->tpl_vars['editedVoteUniqueId']->value==$_smarty_tpl->tpl_vars['vote']->value->uniqId))) {?>

                        <td class="hidden-print">
                            <a href="<?php if ($_smarty_tpl->tpl_vars['admin']->value) {
echo smarty_function_poll_url(array('id'=>$_smarty_tpl->tpl_vars['poll']->value->admin_id,'vote_id'=>$_smarty_tpl->tpl_vars['vote']->value->uniqId,'admin'=>true),$_smarty_tpl);
} else {
echo smarty_function_poll_url(array('id'=>$_smarty_tpl->tpl_vars['poll']->value->id,'vote_id'=>$_smarty_tpl->tpl_vars['vote']->value->uniqId),$_smarty_tpl);
}?>" class="btn btn-default btn-sm" title="<?php echo smarty_modifier_html(__f('Poll results','Edit the line: %s',$_smarty_tpl->tpl_vars['vote']->value->name));?>
">
                                <i class="glyphicon glyphicon-pencil"></i><span class="sr-only"><?php echo __('Generic','Edit');?>
</span>
                            </a>
                            <?php if ($_smarty_tpl->tpl_vars['admin']->value) {?>
                                <a href="<?php echo smarty_function_poll_url(array('id'=>$_smarty_tpl->tpl_vars['admin_poll_id']->value,'admin'=>true,'action'=>'delete_vote','action_value'=>$_smarty_tpl->tpl_vars['vote']->value->id),$_smarty_tpl);?>
"
                                   class="btn btn-default btn-sm"
                                   title="<?php echo __('Poll results','Remove the line:');?>
 <?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['vote']->value->name);?>
">
                                    <i class="glyphicon glyphicon-remove text-danger"></i><span class="sr-only"><?php echo __('Generic','Remove');?>
</span>
                                </a>
                            <?php }?>
                        </td>
                    <?php } else { ?>
                        <td></td>
                    <?php }?>
                </tr>
                <?php }?>
            <?php } ?>

            

            <?php if ($_smarty_tpl->tpl_vars['active']->value&&$_smarty_tpl->tpl_vars['editingVoteId']->value===0&&!$_smarty_tpl->tpl_vars['expired']->value&&$_smarty_tpl->tpl_vars['accessGranted']->value) {?>
                <tr id="vote-form" class="hidden-print">
                    <td class="bg-info" style="padding:5px">
                        <div class="input-group input-group-sm">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input type="text" id="name" name="name" class="form-control" title="<?php echo __('Generic','Your name');?>
" placeholder="<?php echo __('Generic','Your name');?>
" />
                        </div>
                    </td>
                    <?php  $_smarty_tpl->tpl_vars['slot'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['slot']->_loop = false;
 $_smarty_tpl->tpl_vars['id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['slots']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['slot']->key => $_smarty_tpl->tpl_vars['slot']->value) {
$_smarty_tpl->tpl_vars['slot']->_loop = true;
 $_smarty_tpl->tpl_vars['id']->value = $_smarty_tpl->tpl_vars['slot']->key;
?>
                        <td class="bg-info" headers="C<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
">
                            <ul class="list-unstyled choice">
                                <li class="yes">
                                    <input type="radio" id="y-choice-<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" name="choices[<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
]" value="2" />
                                    <label class="btn btn-default btn-xs" for="y-choice-<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" title="<?php echo smarty_modifier_html(__('Poll results','Vote yes for'));?>
 <?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['slot']->value->title);?>
">
                                        <i class="glyphicon glyphicon-ok"></i><span class="sr-only"><?php echo __('Generic','Yes');?>
</span>
                                    </label>
                                </li>
                                <li class="ifneedbe">
                                    <input type="radio" id="i-choice-<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" name="choices[<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
]" value="1" />
                                    <label class="btn btn-default btn-xs" for="i-choice-<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" title="<?php echo smarty_modifier_html(__('Poll results','Vote ifneedbe for'));?>
 <?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['slot']->value->title);?>
">
                                        (<i class="glyphicon glyphicon-ok"></i>)<span class="sr-only"><?php echo __('Generic','Ifneedbe');?>
</span>
                                    </label>
                                </li>
                                <li class="no">
                                    <input type="radio" id="n-choice-<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" name="choices[<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
]" value="0" />
                                    <label class="btn btn-default btn-xs startunchecked" for="n-choice-<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" title="<?php echo smarty_modifier_html(__('Poll results','Vote no for'));?>
 <?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['slot']->value->title);?>
">
                                        <i class="glyphicon glyphicon-ban-circle"></i><span class="sr-only"><?php echo __('Generic','No');?>
</span>
                                    </label>
                                </li>
                                <li style="display:none">
                                  <input type="radio" id="n-choice-<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" name="choices[<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
]" value=" " checked/>
                                </li>
                            </ul>
                        </td>
                    <?php } ?>
                    <td><button type="submit" class="btn btn-success btn-md" name="save" title="<?php echo __('Poll results','Save the choices');?>
"><?php echo __('Generic','Save');?>
</button></td>
                </tr>
            <?php }?>

            <?php if (!$_smarty_tpl->tpl_vars['hidden']->value) {?>
                
                <?php $_smarty_tpl->tpl_vars['count_bests'] = new Smarty_variable(0, null, 0);?>
                <?php $_smarty_tpl->tpl_vars['max'] = new Smarty_variable(max($_smarty_tpl->tpl_vars['best_choices']->value['y']), null, 0);?>
                <?php if ($_smarty_tpl->tpl_vars['max']->value>0) {?>
                    <tr id="addition">
                        <td><?php echo __('Poll results','Addition');?>
<br/><?php echo count($_smarty_tpl->tpl_vars['votes']->value);?>
 <?php if ((count($_smarty_tpl->tpl_vars['votes']->value))==1) {
echo __('Poll results','polled user');
} else {
echo __('Poll results','polled users');
}?></td>
                        <?php  $_smarty_tpl->tpl_vars['best_choice'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['best_choice']->_loop = false;
 $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['best_choices']->value['y']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['best_choice']->key => $_smarty_tpl->tpl_vars['best_choice']->value) {
$_smarty_tpl->tpl_vars['best_choice']->_loop = true;
 $_smarty_tpl->tpl_vars['i']->value = $_smarty_tpl->tpl_vars['best_choice']->key;
?>
                            <?php if ($_smarty_tpl->tpl_vars['max']->value==$_smarty_tpl->tpl_vars['best_choice']->value) {?>
                                <?php $_smarty_tpl->tpl_vars['count_bests'] = new Smarty_variable($_smarty_tpl->tpl_vars['count_bests']->value+1, null, 0);?>
                                <td><i class="glyphicon glyphicon-star text-info"></i><span class="yes-count"><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['best_choice']->value);?>
</span><?php if ($_smarty_tpl->tpl_vars['best_choices']->value['inb'][$_smarty_tpl->tpl_vars['i']->value]>0) {?><br/><span class="small text-muted">(+<span class="inb-count"><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['best_choices']->value['inb'][$_smarty_tpl->tpl_vars['i']->value]);?>
</span>)</span><?php }?></td>
                            <?php } elseif ($_smarty_tpl->tpl_vars['best_choice']->value>0) {?>
                                <td><span class="yes-count"><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['best_choice']->value);?>
</span><?php if ($_smarty_tpl->tpl_vars['best_choices']->value['inb'][$_smarty_tpl->tpl_vars['i']->value]>0) {?><br/><span class="small text-muted">(+<span class="inb-count"><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['best_choices']->value['inb'][$_smarty_tpl->tpl_vars['i']->value]);?>
</span>)</span><?php }?></td>
                            <?php } elseif ($_smarty_tpl->tpl_vars['best_choices']->value['inb'][$_smarty_tpl->tpl_vars['i']->value]>0) {?>
                                <td><br/><span class="small text-muted">(+<span class="inb-count"><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['best_choices']->value['inb'][$_smarty_tpl->tpl_vars['i']->value]);?>
</span>)</span></td>
                            <?php } else { ?>
                                <td></td>
                            <?php }?>
                        <?php } ?>
                    </tr>
                <?php }?>
            <?php }?>
            </tbody>
        </table>
    </form>
</div>

<?php if (!$_smarty_tpl->tpl_vars['hidden']->value&&$_smarty_tpl->tpl_vars['max']->value>0) {?>
    <div class="row" aria-hidden="true">
        <div class="col-xs-12">
            <p class="text-center" id="showChart">
                <button class="btn btn-lg btn-default">
                    <span class="fa fa-fw fa-bar-chart"></span> <?php echo __('Poll results','Display the chart of the results');?>

                </button>
            </p>
        </div>
    </div>
    <?php echo '<script'; ?>
 type="text/javascript">
        $(document).ready(function () {
            $('#showChart').on('click', function() {
                $('#showChart')
                        .after("<h3><?php echo __('Poll results','Chart');?>
</h3><canvas id=\"Chart\"></canvas>")
                        .remove();
                
                var resIfneedbe = [];
                var resYes = [];
            
                $('#addition').find('td').each(function () {
                    var inbCountText = $(this).find('.inb-count').text();
                    if(inbCountText != '' && inbCountText != undefined) {
                        resIfneedbe.push($(this).find('.inb-count').html())
                    } else {
                        resIfneedbe.push(0);
                    }

                    var yesCountText = $(this).find('.yes-count').text();
                    if(yesCountText != '' && yesCountText != undefined) {
                        resYes.push($(this).find('.yes-count').html())
                    } else {
                        resYes.push(0);
                    }
                });
                var cols = [
                <?php  $_smarty_tpl->tpl_vars['slot'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['slot']->_loop = false;
 $_smarty_tpl->tpl_vars['id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['slots']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['slot']->key => $_smarty_tpl->tpl_vars['slot']->value) {
$_smarty_tpl->tpl_vars['slot']->_loop = true;
 $_smarty_tpl->tpl_vars['id']->value = $_smarty_tpl->tpl_vars['slot']->key;
?>
                    $('<div/>').html('<?php echo smarty_modifier_markdown($_smarty_tpl->tpl_vars['slot']->value->title,true);?>
').text(), 
                <?php } ?>
                ];

                resIfneedbe.shift();
                resYes.shift();

                var barChartData = {
                    labels : cols,
                    datasets : [
                    {
                        label: "<?php echo __('Generic','Ifneedbe');?>
",
                        fillColor : "rgba(255,207,79,0.8)",
                        highlightFill: "rgba(255,207,79,1)",
                        barShowStroke : false,
                        data : resIfneedbe
                    },
                    {
                        label: "<?php echo __('Generic','Yes');?>
",
                        fillColor : "rgba(103,120,53,0.8)",
                        highlightFill : "rgba(103,120,53,1)",
                        barShowStroke : false,
                        data : resYes
                    }
                    ]
                };

                var ctx = document.getElementById("Chart").getContext("2d");
                window.myBar = new Chart(ctx).StackedBar(barChartData, {
                    responsive : true
                });
                return false;
            });
        });
    <?php echo '</script'; ?>
>
    
<?php }?>



<?php if (!$_smarty_tpl->tpl_vars['hidden']->value) {?>
    
    <?php $_smarty_tpl->tpl_vars['max'] = new Smarty_variable(max($_smarty_tpl->tpl_vars['best_choices']->value['y']), null, 0);?>
    <?php if ($_smarty_tpl->tpl_vars['max']->value>0) {?>
        <div class="row">
        <?php if ($_smarty_tpl->tpl_vars['count_bests']->value==1) {?>
        <div class="col-sm-12"><h3><?php echo __('Poll results','Best choice');?>
</h3></div>
        <div class="col-sm-6 col-sm-offset-3 alert alert-info">
            <p><i class="glyphicon glyphicon-star text-info"></i> <?php echo __('Poll results','The best choice at this time is:');?>
</p>
            <?php } elseif ($_smarty_tpl->tpl_vars['count_bests']->value>1) {?>
            <div class="col-sm-12"><h3><?php echo __('Poll results','Best choices');?>
</h3></div>
            <div class="col-sm-6 col-sm-offset-3 alert alert-info">
                <p><i class="glyphicon glyphicon-star text-info"></i> <?php echo __('Poll results','The bests choices at this time are:');?>
</p>
                <?php }?>


                <?php $_smarty_tpl->tpl_vars['i'] = new Smarty_variable(0, null, 0);?>
                <ul style="list-style:none">
                    <?php  $_smarty_tpl->tpl_vars['slot'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['slot']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['slots']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['slot']->key => $_smarty_tpl->tpl_vars['slot']->value) {
$_smarty_tpl->tpl_vars['slot']->_loop = true;
?>
                        <?php if ($_smarty_tpl->tpl_vars['best_choices']->value['y'][$_smarty_tpl->tpl_vars['i']->value]==$_smarty_tpl->tpl_vars['max']->value) {?>
                            <li><strong><?php echo smarty_modifier_markdown($_smarty_tpl->tpl_vars['slot']->value->title,true);?>
</strong></li>
                        <?php }?>
                        <?php $_smarty_tpl->tpl_vars['i'] = new Smarty_variable($_smarty_tpl->tpl_vars['i']->value+1, null, 0);?>
                    <?php } ?>
                </ul>
                <p><?php echo __('Generic','with');?>
 <b><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['max']->value);?>
</b> <?php if ($_smarty_tpl->tpl_vars['max']->value==1) {
echo __('Generic','vote');
} else {
echo __('Generic','votes');
}?>.</p>
            </div>
        </div>
    <?php }?>
<?php }?>
<?php }} ?>
<?php /* Smarty version Smarty-3.1.21, created on 2017-11-02 17:57:24
         compiled from "/var/www/framadate//tpl/part/comments.tpl" */ ?>
<?php if ($_valid && !is_callable('content_59fb4e743df1c7_88532632')) {function content_59fb4e743df1c7_88532632($_smarty_tpl) {?><hr role="presentation" id="comments" class="hidden-print"/>


<?php /*  Call merged included template "part/comments_list.tpl" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('part/comments_list.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0, '70746031159fb4e741a26e5-28143015');
content_59fb4e743e0237_23202340($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); 
/*  End of included template "part/comments_list.tpl" */?>


<?php if ($_smarty_tpl->tpl_vars['active']->value&&!$_smarty_tpl->tpl_vars['expired']->value&&$_smarty_tpl->tpl_vars['accessGranted']->value) {?>
    <form action="<?php echo smarty_modifier_resource('action/add_comment.php');?>
" method="POST" id="comment_form">

        <input type="hidden" name="poll" value="<?php echo $_smarty_tpl->tpl_vars['poll_id']->value;?>
"/>
        <?php if (!empty($_smarty_tpl->tpl_vars['admin_poll_id']->value)) {?>
            <input type="hidden" name="poll_admin" value="<?php echo $_smarty_tpl->tpl_vars['admin_poll_id']->value;?>
"/>
        <?php }?>
        <div class="hidden-print jumbotron">
            <div class="col-md-6 col-md-offset-3">
                <fieldset id="add-comment"><legend><?php echo __('Comments','Add a comment to the poll');?>
</legend>
                    <div class="form-group">
                        <label for="comment_name" class="control-label"><?php echo __('Generic','Your name');?>
</label>
                        <input type="text" name="name" id="comment_name" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label for="comment" class="control-label"><?php echo __('Comments','Your comment');?>
</label>
                        <textarea name="comment" id="comment" class="form-control" rows="2" cols="40"></textarea>
                    </div>
                    <div class="pull-right">
                        <input type="submit" id="add_comment" name="add_comment" value="<?php echo __('Comments','Send the comment');?>
" class="btn btn-success">
                    </div>
                </fieldset>
            </div>
            <div class="clearfix"></div>
        </div>
    </form>
<?php }?><?php }} ?>
<?php /* Smarty version Smarty-3.1.21, created on 2017-11-02 17:57:24
         compiled from "/var/www/framadate//tpl/part/comments_list.tpl" */ ?>
<?php if ($_valid && !is_callable('content_59fb4e743e0237_23202340')) {function content_59fb4e743e0237_23202340($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/var/www/framadate/vendor/smarty/smarty/libs/plugins/modifier.date_format.php';
?><div id="comments_list">
    <form action="<?php if ($_smarty_tpl->tpl_vars['admin']->value) {
echo smarty_function_poll_url(array('id'=>$_smarty_tpl->tpl_vars['admin_poll_id']->value,'admin'=>true),$_smarty_tpl);
} else {
echo smarty_function_poll_url(array('id'=>$_smarty_tpl->tpl_vars['poll_id']->value),$_smarty_tpl);
}?>" method="POST">
    <?php if (count($_smarty_tpl->tpl_vars['comments']->value)>0) {?>
        <h3><?php echo __('Comments','Comments of polled people');?>
</h3>
        <?php  $_smarty_tpl->tpl_vars['comment'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['comment']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['comments']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['comment']->key => $_smarty_tpl->tpl_vars['comment']->value) {
$_smarty_tpl->tpl_vars['comment']->_loop = true;
?>
            <div class="comment">
                <?php if ($_smarty_tpl->tpl_vars['admin']->value&&!$_smarty_tpl->tpl_vars['expired']->value) {?>
                    <button type="submit" name="delete_comment" value="<?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['comment']->value->id);?>
" class="btn btn-link" title="<?php echo __('Comments','Remove the comment');?>
"><span class="glyphicon glyphicon-remove text-danger"></span><span class="sr-only"><?php echo __('Generic','Remove');?>
</span></button>
                <?php }?>
                <span class="comment_date"><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['comment']->value->date,$_smarty_tpl->tpl_vars['date_format']->value['txt_datetime_short']);?>
</span>
                <b><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['comment']->value->name);?>
</b>&nbsp;
                <span><?php echo nl2br(htmlspecialchars($_smarty_tpl->tpl_vars['comment']->value->comment, ENT_QUOTES, 'ISO-8859-1', true));?>
</span>
            </div>
        <?php } ?>
    <?php }?>
    </form>
    <div id="comments_alerts"></div>
</div><?php }} ?>
