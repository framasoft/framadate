<?php /* Smarty version Smarty-3.1.21, created on 2017-11-02 17:34:56
         compiled from "/var/www/framadate//tpl/create_poll.tpl" */ ?>
<?php /*%%SmartyHeaderCode:204551682559fb4930a13822-84581280%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '919d1bbaaa8f118144b25cd38f24611665c8a7ef' => 
    array (
      0 => '/var/www/framadate//tpl/create_poll.tpl',
      1 => 1509298107,
      2 => 'file',
    ),
    'cece18edb8bb8323539cb82888af012e22be1acf' => 
    array (
      0 => '/var/www/framadate//tpl/page.tpl',
      1 => 1506522320,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '204551682559fb4930a13822-84581280',
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
  'unifunc' => 'content_59fb4930b208a5_50271461',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59fb4930b208a5_50271461')) {function content_59fb4930b208a5_50271461($_smarty_tpl) {?><!DOCTYPE html>
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
 src="<?php echo smarty_modifier_resource("js/app/create_poll.js");?>
" type="text/javascript"><?php echo '</script'; ?>
>
    <link rel="stylesheet" href="<?php echo smarty_modifier_resource("css/app/create_poll.css");?>
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



    <div class="row" style="display:none" id="form-block">
        <div class="col-md-8 col-md-offset-2">
            <form name="formulaire" id="formulaire" action="" method="POST" class="form-horizontal" role="form">

                <div class="alert alert-info">
                    <p>
                        <?php echo __('Step 1','You are in the poll creation section.');?>
<br/>
                        <?php echo __('Step 1','Required fields cannot be left blank.');?>

                    </p>
                </div>

                <div class="form-group <?php echo $_smarty_tpl->tpl_vars['errors']->value['name']['class'];?>
">
                    <label for="yourname" class="col-sm-4 control-label"><?php echo __('Generic','Your name');?>
 *</label>

                    <div class="col-sm-8">
                        <?php if ($_smarty_tpl->tpl_vars['useRemoteUser']->value) {?>
                            <input type="hidden" name="name" value="<?php echo $_smarty_tpl->tpl_vars['form']->value->admin_name;?>
" /><?php echo $_smarty_tpl->tpl_vars['form']->value->admin_name;?>

                        <?php } else { ?>
                            <input id="yourname" type="text" name="name" class="form-control" <?php echo $_smarty_tpl->tpl_vars['errors']->value['name']['aria'];?>
 value="<?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['poll_name']->value);?>
" />
                        <?php }?>
                    </div>
                </div>
                <?php if (!empty($_smarty_tpl->tpl_vars['errors']->value['name']['msg'])) {?>
                    <div class="alert alert-danger">
                        <p id="poll_title_error">
                            <?php echo $_smarty_tpl->tpl_vars['errors']->value['name']['msg'];?>

                        </p>
                    </div>
                <?php }?>

                <?php if ($_smarty_tpl->tpl_vars['use_smtp']->value) {?>
                    <div class="form-group <?php echo $_smarty_tpl->tpl_vars['errors']->value['email']['class'];?>
">
                        <label for="email" class="col-sm-4 control-label">
                            <?php echo __('Generic','Your email address');?>
 *<br/>
                            <span class="small"><?php echo __('Generic','(in the format name@mail.com)');?>
</span>
                        </label>

                        <div class="col-sm-8">
                            <?php if ($_smarty_tpl->tpl_vars['useRemoteUser']->value) {?>
                                <input type="hidden" name="mail" value="<?php echo $_smarty_tpl->tpl_vars['form']->value->admin_mail;?>
"><?php echo $_smarty_tpl->tpl_vars['form']->value->admin_mail;?>

                            <?php } else { ?>
                                <input id="email" type="text" name="mail" class="form-control" <?php echo $_smarty_tpl->tpl_vars['errors']->value['email']['aria'];?>
 value="<?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['poll_mail']->value);?>
" />
                            <?php }?>
                        </div>
                    </div>
                    <?php if (!empty($_smarty_tpl->tpl_vars['errors']->value['email']['msg'])) {?>
                        <div class="alert alert-danger">
                            <p id="poll_title_error">
                                <?php echo $_smarty_tpl->tpl_vars['errors']->value['email']['msg'];?>

                            </p>
                        </div>
                    <?php }?>

                <?php }?>

                <div class="form-group <?php echo $_smarty_tpl->tpl_vars['errors']->value['title']['class'];?>
">
                    <label for="poll_title" class="col-sm-4 control-label"><?php echo __('Step 1','Poll title');?>
 *</label>

                    <div class="col-sm-8">
                        <input id="poll_title" type="text" name="title" class="form-control" <?php echo $_smarty_tpl->tpl_vars['errors']->value['title']['aria'];?>

                               value="<?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['poll_title']->value);?>
"/>
                    </div>
                </div>
                <?php if (!empty($_smarty_tpl->tpl_vars['errors']->value['title']['msg'])) {?>
                    <div class="alert alert-danger">
                        <p id="poll_title_error">
                            <?php echo $_smarty_tpl->tpl_vars['errors']->value['title']['msg'];?>

                        </p>
                    </div>
                <?php }?>

                <div class="form-group <?php echo $_smarty_tpl->tpl_vars['errors']->value['description']['class'];?>
">
                    <label for="poll_comments" class="col-sm-4 control-label"><?php echo __('Generic','Description');?>
</label>

                    <div class="col-sm-8">
                        <textarea id="poll_comments" name="description"
                                  class="form-control" <?php echo $_smarty_tpl->tpl_vars['errors']->value['description']['aria'];?>

                                  rows="5"><?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['poll_description']->value);?>
</textarea>
                    </div>
                </div>
                <?php if (!empty($_smarty_tpl->tpl_vars['errors']->value['description']['msg'])) {?>
                    <div class="alert alert-danger">
                        <p id="poll_title_error">
                            <?php echo $_smarty_tpl->tpl_vars['errors']->value['description']['msg'];?>

                        </p>
                    </div>
                <?php }?>

                
                <div class="col-sm-offset-3 col-sm-1 hidden-xs">
                    <p class="lead">
                        <i class="glyphicon glyphicon-cog" aria-hidden="true"></i>
                    </p>
                </div>
                <div class="col-sm-8 col-xs-12">
                    <span class="lead visible-xs-inline">
                        <i class="glyphicon glyphicon-cog" aria-hidden="true"></i>
                    </span>
                    <a class="optionnal-parameters collapsed lead" role="button" data-toggle="collapse" href="#optionnal" aria-expanded="false" aria-controls="optionnal">
                        <?php echo __('Step 1',"Optional parameters");?>

                        <i class="caret" aria-hidden="true"></i>
                        <i class="caret caret-up" aria-hidden="true"></i>
                    </a>

                </div>
                <div class="clearfix"></div>


                <div class="collapse" id="optionnal">

		    
		    
			
			               
	
		    <div class="form-group">
                        <label for="use_valueMax" class="col-sm-4 control-label">
			<?php echo __('Step 1','Value Max');?>
<br/>
                        </label>
                        <div class="col-sm-8">
                            <div class="checkbox">
                                <label>
                                    <input id="use_ValueMax" name="use_ValueMax" type="checkbox" >         
						<?php echo __('Step 1',"Limit the ammount of voters per option");?>
  
			        </label>
                            </div>
                        </div>
		     </div>

		    <div class="form-group">
		      <div id="ValueMax"<?php if (!$_smarty_tpl->tpl_vars['use_ValueMax']->value) {?> class="hidden"<?php }?>>
			
                            <div class="col-sm-offset-4 col-sm-8">
				    <label >   
                                        <input id="ValueMax" type="number" name="ValueMax">
      
                                        <?php echo __('Step 1',"ValueMax instructions");?>

                                    </label>
			
			    </div>
			</div>
		   </div>
		
	

                    

                    <div class="form-group <?php echo $_smarty_tpl->tpl_vars['errors']->value['customized_url']['class'];?>
">
                        <label for="poll_id" class="col-sm-4 control-label">
                            <?php echo __('Step 1','Poll id');?>
<br/>
                        </label>

                        <div class="col-sm-8">
                            <div class="checkbox">
                                <label>
                                    <input id="use_customized_url" name="use_customized_url" type="checkbox" <?php if ($_smarty_tpl->tpl_vars['use_customized_url']->value) {?>checked<?php }?>/>
                                    <?php echo __('Step 1','Customize the URL');?>

                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="customized_url_options" <?php if (!$_smarty_tpl->tpl_vars['use_customized_url']->value) {?>class="hidden"<?php }?>>
                        <div class="form-group <?php echo $_smarty_tpl->tpl_vars['errors']->value['customized_url']['class'];?>
">
                            <label for="customized_url" class="col-sm-4 control-label">
                                <span id="pollUrlDesc" class="small"><?php echo __('Step 1','Poll id rules');?>
</span>
                            </label>

                            <div class="col-sm-8">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <?php echo $_smarty_tpl->tpl_vars['SERVER_URL']->value;?>

                                    </span>
                                    <input id="customized_url" type="text" name="customized_url" class="form-control" <?php echo $_smarty_tpl->tpl_vars['errors']->value['customized_url']['aria'];?>

                                           value="<?php echo smarty_modifier_html($_smarty_tpl->tpl_vars['customized_url']->value);?>
" aria-describedBy="pollUrlDesc" maxlength="64"
                                           pattern="[A-Za-z0-9-]+"/>
                                </div>
                                <span class="help-block text-warning"><?php echo __('Step 1','Poll id warning');?>
</span>
                            </div>
                        </div>
                        <?php if (!empty($_smarty_tpl->tpl_vars['errors']->value['customized_url']['msg'])) {?>
                            <div class="alert alert-danger">
                                <p id="poll_customized_url_error">
                                    <?php echo $_smarty_tpl->tpl_vars['errors']->value['customized_url']['msg'];?>

                                </p>
                            </div>
                        <?php }?>
                    </div>

                    

                    <div class="form-group">
                        <label for="poll_id" class="col-sm-4 control-label">
                            <?php echo __('Step 1','Poll password');?>

                        </label>

                        <div class="col-sm-8">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="use_password" <?php if ($_smarty_tpl->tpl_vars['poll_use_password']->value) {?>checked<?php }?>
                                           id="use_password">
                                    <?php echo __('Step 1',"Use a password to restrict access");?>

                                </label>
                            </div>
                        </div>

                        <div id="password_options"<?php if (!$_smarty_tpl->tpl_vars['poll_use_password']->value) {?> class="hidden"<?php }?>>
                            <div class="col-sm-offset-4 col-sm-8">
                                <div class="input-group">
                                    <input id="poll_password" type="password" name="password" class="form-control" <?php echo $_smarty_tpl->tpl_vars['errors']->value['password']['aria'];?>
/>
                                    <label for="poll_password" class="input-group-addon"><?php echo __('Step 1','Password choice');?>
</label>
                                </div>
                            </div>
                            <?php if (!empty($_smarty_tpl->tpl_vars['errors']->value['password']['msg'])) {?>
                                <div class="alert alert-danger">
                                    <p id="poll_password_error">
                                        <?php echo $_smarty_tpl->tpl_vars['errors']->value['password']['msg'];?>

                                    </p>
                                </div>
                            <?php }?>
                            <div class="col-sm-offset-4 col-sm-8">
                                <div class="input-group">
                                    <input id="poll_password_repeat" type="password" name="password_repeat" class="form-control" <?php echo $_smarty_tpl->tpl_vars['errors']->value['password_repeat']['aria'];?>
/>
                                    <label for="poll_password_repeat" class="input-group-addon"><?php echo __('Step 1','Password confirmation');?>
</label>
                                </div>
                            </div>
                            <?php if (!empty($_smarty_tpl->tpl_vars['errors']->value['password_repeat']['msg'])) {?>
                                <div class="alert alert-danger">
                                    <p id="poll_password_repeat_error">
                                        <?php echo $_smarty_tpl->tpl_vars['errors']->value['password_repeat']['msg'];?>

                                    </p>
                                </div>
                            <?php }?>
                            <div class="col-sm-offset-4 col-sm-8">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="results_publicly_visible"
                                               <?php if ($_smarty_tpl->tpl_vars['poll_results_publicly_visible']->value) {?>checked<?php }?> id="results_publicly_visible"/>
                                        <?php echo __('Step 1',"The results are publicly visible");?>

                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="poll_id" class="col-sm-4 control-label">
                            <?php echo __('Step 1','Permissions');?>

                        </label>
                        <div class="col-sm-8">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="editable" id="editableByAll" <?php if ($_smarty_tpl->tpl_vars['poll_editable']->value==constant("Framadate\Editable::EDITABLE_BY_ALL")) {?>checked<?php }?> value="<?php echo constant("Framadate\Editable::EDITABLE_BY_ALL");?>
">
                                    <?php echo __('Step 1','All voters can modify any vote');?>

                                </label>
                                <label>
                                    <input type="radio" name="editable" <?php if ($_smarty_tpl->tpl_vars['poll_editable']->value==constant("Framadate\Editable::EDITABLE_BY_OWN")) {?>checked<?php }?> value="<?php echo constant("Framadate\Editable::EDITABLE_BY_OWN");?>
">
                                    <?php echo __('Step 1','Voters can modify their vote themselves');?>

                                </label>
                                <label>
                                    <input type="radio" name="editable" <?php if (empty($_smarty_tpl->tpl_vars['poll_editable']->value)||$_smarty_tpl->tpl_vars['poll_editable']->value==constant("Framadate\Editable::NOT_EDITABLE")) {?>checked<?php }?> value="<?php echo constant("Framadate\Editable::NOT_EDITABLE");?>
">
                                    <?php echo __('Step 1','Votes cannot be modified');?>

                                </label>
                            </div>
                        </div>
                    </div>


                    <?php if ($_smarty_tpl->tpl_vars['use_smtp']->value) {?>
                        <div class="form-group">
                            <div class="col-sm-offset-4 col-sm-8">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="receiveNewVotes" <?php if ($_smarty_tpl->tpl_vars['poll_receiveNewVotes']->value) {?>checked<?php }?>
                                        id="receiveNewVotes">
                                        <?php echo __('Step 1','To receive an email for each new vote');?>

                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-4 col-sm-8">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="receiveNewComments" <?php if ($_smarty_tpl->tpl_vars['poll_receiveNewComments']->value) {?>checked<?php }?>
                                        id="receiveNewComments">
                                        <?php echo __('Step 1','To receive an email for each new comment');?>

                                    </label>
                                </div>
                            </div>
                        </div>
                    <?php }?>

                    <div class="form-group">
                        <div class="col-sm-offset-4 col-sm-8">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="hidden" <?php if ($_smarty_tpl->tpl_vars['poll_hidden']->value) {?>checked<?php }?>
                                    id="hidden">
                                    <?php echo __('Step 1',"Only the poll maker can see the poll's results");?>

                                </label>
                            </div>
                            <div id="hiddenWithBadEditionModeError" class="alert alert-danger hidden">
                                <p>
                                    <?php echo __('Error',"You can't create a poll with hidden results with the following edition option:");?>
"<?php echo __('Step 1','All voters can modify any vote');?>
"
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="text-right">
                    <input type="hidden" name="type" value="$poll_type"/>
                    <button name="<?php echo $_smarty_tpl->tpl_vars['goToStep2']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['poll_type']->value;?>
" type="submit"
                            class="btn btn-success"><?php echo __('Step 1','Go to step 2');?>
</button>
                </p>

                <?php echo '<script'; ?>
 type="text/javascript">document.formulaire.title.focus();<?php echo '</script'; ?>
>

            </form>
        </div>
    </div>
    <noscript>
        <div class="alert alert-danger">
            <?php echo __('Step 1','Javascript is disabled on your browser. Its activation is required to create a poll.');?>

        </div>
    </noscript>
    <div id="cookie-warning" class="alert alert-danger" style="display:none">
        <?php echo __('Step 1','Cookies are disabled on your browser. Theirs activation is required to create a poll.');?>

    </div>


</main>
</div> <!-- .container -->
</body>
</html>
<?php }} ?>
