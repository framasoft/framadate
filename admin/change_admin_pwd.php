<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of Framadate/OpenSondage: Framasoft (https://github.com/framasoft)
 *
 * =============================
 *
 * Ce logiciel est régi par la licence CeCILL-B. Si une copie de cette licence
 * ne se trouve pas avec ce fichier vous pouvez l'obtenir sur
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 *
 * Auteurs de STUdS (projet initial) : Guilhem BORGHESI (borghesi@unistra.fr) et Raphaël DROZ
 * Auteurs de Framadate/OpenSondage : Framasoft (https://github.com/framasoft)
 */

include_once __DIR__ . '/../app/inc/init.php';
//include_once __DIR__ . '/../bandeaux.php';

use Framadate\Utils;

$msg_info = null;
$msg_error = null;

//Check and save new password

if (isset($_POST['old_pwd']) && isset($_POST['new_pwd']) && isset($_POST['new_pwd_confirm']))
{
	$login = new Framadate\Services\AuthenticationService($connect);
	
	if ($login->IsPasswordRequired() && !$login->IsPasswordOK($_POST['old_pwd']))
	{
		$msg_error = __('Password', 'OldPwdIncorrect');
	}
	else if ($_POST['new_pwd_confirm'] != $_POST['new_pwd'])
	{
		$msg_error = __('Error', 'Passwords do not match');
	}
	else //OK : password changing and go back to admin page
	{
		if ($login->SavePassword($_POST['new_pwd']))
		{
			header('Location: ' . Utils::get_server_name() . 'admin/');
			exit;
		}
		
		$msg_error = $login->GetMsgError();
	}
}

// Show template to change the password

$smarty->assign('msg_info', $msg_info);
$smarty->assign('msg_error', $msg_error);

$smarty->assign('title', __('Admin', 'ChangePwd'));
$smarty->display('admin/change_admin_pwd.tpl');
