<?php /* Smarty version Smarty-3.1.21, created on 2017-09-27 18:17:50
         compiled from "/var/www/framadate//tpl/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:141115761059cbcf2eaedd75-83440767%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fe7bb26fe74ac654e0d58e28c5c44320fab04a5f' => 
    array (
      0 => '/var/www/framadate//tpl/index.tpl',
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
  'nocache_hash' => '141115761059cbcf2eaedd75-83440767',
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
  'unifunc' => 'content_59cbcf2eb83c77_44460046',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59cbcf2eb83c77_44460046')) {function content_59cbcf2eb83c77_44460046($_smarty_tpl) {?><!DOCTYPE html>
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
        <div class="col-xs-12 col-md-6 text-center">
            <p class="home-choice">
                <a href="<?php echo $_smarty_tpl->tpl_vars['SERVER_URL']->value;?>
create_poll.php?type=date" class="opacity" role="button">
                    <img class="img-responsive center-block" src="<?php echo smarty_modifier_resource('images/date.png');?>
" alt=""/>
                    <br/>
                    <span class="btn btn-primary btn-lg">
                        <span class="glyphicon glyphicon-calendar"></span>
                        <?php echo __('Homepage','Schedule an event');?>

                    </span>
                </a>
            </p>
        </div>
        <div class="col-xs-12 col-md-6 text-center">
            <p class="home-choice">
                <a href="<?php echo $_smarty_tpl->tpl_vars['SERVER_URL']->value;?>
create_poll.php?type=autre" class="opacity" role="button">
                    <img alt="" class="img-responsive center-block" src="<?php echo smarty_modifier_resource('images/classic.png');?>
"/>
                    <br/>
                    <span class="btn btn-info btn-lg">
                        <span class="glyphicon glyphicon-stats"></span>
                        <?php echo __('Homepage','Make a classic poll');?>

                    </span>
                </a>
            </p>
        </div>
        <div class="col-xs-12 col-md-6 col-md-offset-3 text-center">
            <p class="home-choice">
                <a href="<?php echo $_smarty_tpl->tpl_vars['SERVER_URL']->value;?>
find_polls.php" class="opacity" role="button">
                    <span class="btn btn-warning btn-lg">
                        <span class="glyphicon glyphicon-search"></span>
                        <?php echo __('Homepage','Where are my polls');?>

                    </span>
                </a>
            </p>
        </div>
    </div>
    <hr role="presentation"/>
    <div class="row">

        <?php if ($_smarty_tpl->tpl_vars['show_what_is_that']->value) {?>
            <div class="col-md-<?php echo $_smarty_tpl->tpl_vars['col_size']->value;?>
">
                <h3><?php echo __('1st section','What is that?');?>
</h3>

                <p class="text-center" role="presentation">
                    <span class="glyphicon glyphicon-question-sign" style="font-size:50px"></span>
                </p>

                <p><?php echo __('1st section','Framadate is an online service for planning an appointment or make a decision quickly and easily. No registration is required.');?>
</p>

                <p><?php echo __('1st section','Here is how it works:');?>
</p>
                <ol>
                    <li><?php echo __('1st section','Make a poll');?>
</li>
                    <li><?php echo __('1st section','Define dates or subjects to choose');?>
</li>
                    <li><?php echo __('1st section','Send the poll link to your friends or colleagues');?>
</li>
                    <li><?php echo __('1st section','Discuss and make a decision');?>
</li>
                </ol>

                <?php if ($_smarty_tpl->tpl_vars['demo_poll']->value!=null) {?>
                <p>
                    <?php echo __('1st section','Do you want to');?>

                    <a href="<?php echo smarty_function_poll_url(array('id'=>'aqg259dth55iuhwm'),$_smarty_tpl);?>
"><?php echo __('1st section','view an example?');?>
</a>
                </p>
                <?php }?>
            </div>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['show_the_software']->value) {?>
            <div class="col-md-<?php echo $_smarty_tpl->tpl_vars['col_size']->value;?>
">
                <h3><?php echo __('2nd section','The software');?>
</h3>

                <p class="text-center" role="presentation">
                    <span class="glyphicon glyphicon-cloud" style="font-size:50px"></span>
                </p>

                <p><?php echo __('2nd section','Framadate was initially based on ');?>

                    <a href="https://sourcesup.cru.fr/projects/studs/">Studs</a>
                    <?php echo __('2nd section','a software developed by the University of Strasbourg. Today, it is devevoped by the association Framasoft.');?>

                </p>

                <p><?php echo __('2nd section','This software needs javascript and cookies enabled. It is compatible with the following web browsers:');?>
</p>
                <ul>
                    <li>Microsoft Internet Explorer 9+</li>
                    <li>Google Chrome 19+</li>
                    <li>Firefox 12+</li>
                    <li>Safari 5+</li>
                    <li>Opera 11+</li>
                </ul>
                <p>
                    <?php echo __('2nd section','It is governed by the');?>

                    <a href="http://www.cecill.info"><?php echo __('2nd section','CeCILL-B license');?>
</a>.
                </p>
            </div>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['show_cultivate_your_garden']->value) {?>
            <div class="col-md-<?php echo $_smarty_tpl->tpl_vars['col_size']->value;?>
">
                <h3><?php echo __('3rd section','Cultivate your garden');?>
</h3>

                <p class="text-center" role="presentation">
                    <span class="glyphicon glyphicon-tree-deciduous" style="font-size:50px"></span>
                </p>

                <p>
                    <?php echo __('3rd section','To participate in the software development, suggest improvements or simply download it, please visit ');?>

                    <a href="https://framagit.org/framasoft/framadate"><?php echo __('3rd section','the development site');?>
</a>.
                </p>
                <br/>

                <p><?php echo __('3rd section','If you want to install the software for your own use and thus increase your independence, we help you on:');?>
</p>

                <p class="text-center">
                    <a href="http://framacloud.org/cultiver-son-jardin/installation-de-framadate/"
                       class="btn btn-success">
                        <span class="glyphicon glyphicon-tree-deciduous"></span>
                        framacloud.org
                    </a>
                </p>
            </div>
        <?php }?>
    </div>


</main>
</div> <!-- .container -->
</body>
</html>
<?php }} ?>
