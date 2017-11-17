<?php /* Smarty version Smarty-3.1.21, created on 2017-09-27 18:18:03
         compiled from "/var/www/framadate//tpl/admin/polls.tpl" */ ?>
<?php /*%%SmartyHeaderCode:69416690259cbcf3b052ba2-66161406%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7439deeebb3969a96b1b8f35fe2695ca7d9338df' => 
    array (
      0 => '/var/www/framadate//tpl/admin/polls.tpl',
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
  'nocache_hash' => '69416690259cbcf3b052ba2-66161406',
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
  'unifunc' => 'content_59cbcf3b13e891_86432882',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59cbcf3b13e891_86432882')) {function content_59cbcf3b13e891_86432882($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/var/www/framadate/vendor/smarty/smarty/libs/plugins/modifier.date_format.php';
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
 src="<?php echo smarty_modifier_resource("js/app/admin/polls.js");?>
" type="text/javascript"><?php echo '</script'; ?>
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
        <div class="col-xs-12">
            <a href="<?php echo smarty_modifier_resource('admin');?>
"><?php echo __('Admin','Back to administration');?>
</a>
        </div>
    </div>
    
    <div class="panel panel-default" id="poll_search">
        <div class="panel-heading"><?php echo __('Generic','Search');?>
</div>
        <div class="panel-body" style="display: none;">
            <form action="" method="GET">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="poll" class="control-label"><?php echo __('Admin','Poll ID');?>
</label>
                            <input type="text" name="poll" id="poll" class="form-control"
                                   value="<?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['search']->value['poll']);?>
"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="title" class="control-label"><?php echo __('Admin','Title');?>
</label>
                            <input type="text" name="title" id="title" class="form-control"
                                   value="<?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['search']->value['title']);?>
"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name" class="control-label"><?php echo __('Admin','Author');?>
</label>
                            <input type="text" name="name" id="name" class="form-control"
                                   value="<?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['search']->value['name']);?>
"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="mail" class="control-label"><?php echo __('Admin','Email');?>
</label>
                            <input type="text" name="mail" id="mail" class="form-control"
                                   value="<?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['search']->value['mail']);?>
"/>
                        </div>
                    </div>
                </div>
                <input type="submit" value="<?php echo __('Generic','Search');?>
" class="btn btn-default"/>
            </form>
        </div>
    </div>

    <form action="" method="POST">
        <input type="hidden" name="csrf" value="<?php echo $_smarty_tpl->tpl_vars['crsf']->value;?>
"/>
        <?php if ($_smarty_tpl->tpl_vars['poll_to_delete']->value) {?>
            <div class="alert alert-warning text-center">
                <h3><?php echo __('adminstuds','Confirm removal of the poll');?>
 "<?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['poll_to_delete']->value->id);?>
"</h3>

                <p>
                    <button class="btn btn-default" type="submit" value="1"
                            name="annullesuppression"><?php echo __('adminstuds','Keep the poll');?>
</button>
                    <button type="submit" name="delete_confirm" value="<?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['poll_to_delete']->value->id);?>
"
                            class="btn btn-danger"><?php echo __('adminstuds','Delete the poll');?>
</button>
                </p>
            </div>
        <?php }?>
        <input type="hidden" name="csrf" value="<?php echo $_smarty_tpl->tpl_vars['crsf']->value;?>
"/>

        <div class="panel panel-default">
            <div class="panel-heading">
                <?php if ($_smarty_tpl->tpl_vars['count']->value==$_smarty_tpl->tpl_vars['total']->value) {
echo $_smarty_tpl->tpl_vars['count']->value;
} else {
echo $_smarty_tpl->tpl_vars['count']->value;?>
 / <?php echo $_smarty_tpl->tpl_vars['total']->value;
}?> <?php echo __('Admin','polls in the database at this time');?>

            </div>

            <table class="table table-bordered table-polls">
                <tr align="center">
                    <th scope="col"></th>
                    <th scope="col"><?php echo __('Admin','Title');?>
</th>
                    <th scope="col"><?php echo __('Admin','Author');?>
</th>
                    <th scope="col"><?php echo __('Admin','Email');?>
</th>
                    <th scope="col"><?php echo __('Admin','Expiration date');?>
</th>
                    <th scope="col"><?php echo __('Admin','Votes');?>
</th>
                    <th scope="col"><?php echo __('Admin','Poll ID');?>
</th>
                    <th scope="col" colspan="3"><?php echo __('Admin','Actions');?>
</th>
                </tr>
                <?php  $_smarty_tpl->tpl_vars['poll'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['poll']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['polls']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['poll']->key => $_smarty_tpl->tpl_vars['poll']->value) {
$_smarty_tpl->tpl_vars['poll']->_loop = true;
?>
                    <tr align="center">
                        <td class="cell-format">
                            <?php if ($_smarty_tpl->tpl_vars['poll']->value->format==='D') {?>
                                <span class="glyphicon glyphicon-calendar" aria-hidden="true"
                                      title="<?php echo __('Generic','Date');?>
"></span>
                                <span class="sr-only"><?php echo __('Generic','Date');?>
</span>
                            <?php } else { ?>
                                <span class="glyphicon glyphicon-list-alt" aria-hidden="true"
                                      title="<?php echo __('Generic','Classic');?>
"></span>
                                <span class="sr-only"><?php echo __('Generic','Classic');?>
</span>
                            <?php }?>
                        </td>
                        <td><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['poll']->value->title);?>
</td>
                        <td><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['poll']->value->admin_name);?>
</td>
                        <td><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['poll']->value->admin_mail);?>
</td>

                        <?php if (strtotime($_smarty_tpl->tpl_vars['poll']->value->end_date)>time()) {?>
                            <td><?php echo date('d/m/y',strtotime($_smarty_tpl->tpl_vars['poll']->value->end_date));?>
</td>
                        <?php } else { ?>
                            <td><span class="text-danger"><?php echo smarty_modifier_date_format(strtotime($_smarty_tpl->tpl_vars['poll']->value->end_date),'d/m/Y');?>
</span></td>
                        <?php }?>
                        <td><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['poll']->value->votes);?>
</td>
                        <td><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['poll']->value->id);?>
</td>
                        <td><a href="<?php echo smarty_function_poll_url(array('id'=>$_smarty_tpl->tpl_vars['poll']->value->id),$_smarty_tpl);?>
" class="btn btn-link"
                               title="<?php echo __('Admin','See the poll');?>
"><span
                                        class="glyphicon glyphicon-eye-open"></span><span
                                        class="sr-only"><?php echo __('Admin','See the poll');?>
</span></a></td>
                        <td><a href="<?php echo smarty_function_poll_url(array('id'=>$_smarty_tpl->tpl_vars['poll']->value->admin_id,'admin'=>true),$_smarty_tpl);?>
" class="btn btn-link"
                               title="<?php echo __('Admin','Change the poll');?>
"><span
                                        class="glyphicon glyphicon-pencil"></span><span
                                        class="sr-only"><?php echo __('Admin','Change the poll');?>
</span></a></td>
                        <td>
                            <button type="submit" name="delete_poll" value="<?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['poll']->value->id);?>
" class="btn btn-link"
                                    title="<?php echo __('Admin','Deleted the poll');?>
"><span
                                        class="glyphicon glyphicon-trash text-danger"></span><span
                                        class="sr-only"><?php echo __('Admin','Deleted the poll');?>
</span>
                        </td>
                    </tr>
                <?php } ?>
            </table>

            <div class="panel-heading">
                <?php echo __('Admin','Pages:');?>

                <?php $_smarty_tpl->tpl_vars['p'] = new Smarty_Variable;$_smarty_tpl->tpl_vars['p']->step = 1;$_smarty_tpl->tpl_vars['p']->total = (int) ceil(($_smarty_tpl->tpl_vars['p']->step > 0 ? $_smarty_tpl->tpl_vars['pages']->value+1 - (1) : 1-($_smarty_tpl->tpl_vars['pages']->value)+1)/abs($_smarty_tpl->tpl_vars['p']->step));
if ($_smarty_tpl->tpl_vars['p']->total > 0) {
for ($_smarty_tpl->tpl_vars['p']->value = 1, $_smarty_tpl->tpl_vars['p']->iteration = 1;$_smarty_tpl->tpl_vars['p']->iteration <= $_smarty_tpl->tpl_vars['p']->total;$_smarty_tpl->tpl_vars['p']->value += $_smarty_tpl->tpl_vars['p']->step, $_smarty_tpl->tpl_vars['p']->iteration++) {
$_smarty_tpl->tpl_vars['p']->first = $_smarty_tpl->tpl_vars['p']->iteration == 1;$_smarty_tpl->tpl_vars['p']->last = $_smarty_tpl->tpl_vars['p']->iteration == $_smarty_tpl->tpl_vars['p']->total;?>
                    <?php if ($_smarty_tpl->tpl_vars['p']->value===$_smarty_tpl->tpl_vars['page']->value) {?>
                        <a href="<?php echo $_smarty_tpl->tpl_vars['SERVER_URL']->value;?>
admin/polls.php?page=<?php echo $_smarty_tpl->tpl_vars['p']->value;?>
&<?php echo $_smarty_tpl->tpl_vars['search_query']->value;?>
" class="btn btn-danger"
                           disabled="disabled"><?php echo $_smarty_tpl->tpl_vars['p']->value;?>
</a>
                    <?php } else { ?>
                        <a href="<?php echo $_smarty_tpl->tpl_vars['SERVER_URL']->value;?>
admin/polls.php?page=<?php echo $_smarty_tpl->tpl_vars['p']->value;?>
&<?php echo $_smarty_tpl->tpl_vars['search_query']->value;?>
" class="btn btn-info"><?php echo $_smarty_tpl->tpl_vars['p']->value;?>
</a>
                    <?php }?>
                <?php }} ?>
            </div>
        </div>
    </form>



</main>
</div> <!-- .container -->
</body>
</html>
<?php }} ?>
