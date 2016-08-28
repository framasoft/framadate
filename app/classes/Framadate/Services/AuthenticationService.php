<?php

namespace Framadate\Services;

use Framadate\FramaDB;
use Framadate\Utils;
use Framadate\Security\PasswordHasher;
use Smarty;

/**
 * This class helps to authenticate into admin pages.
 */
class AuthenticationService {

	private $msg_error;

	/**
	 * @var FramaDB
	 */
	private $connect;

	/**
	 * @var Record with required password
	 */
	private $RecVariable;

	public function __construct(FramaDB $connect) {
		$this->msg_error = null;
		$this->connect = $connect;

		try
		{
			$SQL = "select * from `" . Utils::table('variable') . "` where name = 'admin_pwd'";
			$this->RecVariable = $this->connect->query($SQL)->fetch(\PDO::FETCH_OBJ);
		}
		catch (\PDO\PDOException $e)
		{
			$this->msg_error = "PDOException : " . $e->getMessage() . "\n\n" . __('Admin', 'PleaseUpgrade');
		}		
		catch (\Exception $e)
		{
			$this->msg_error = "Exception : " . $e->getMessage() . "\n\n" . __('Admin', 'PleaseUpgrade');
		}
	}

	/**
	 * @param $smarty		 : SMARTY engine for asking password
	 * @param $PageTitle : Title of the calling page
	 * @return bool : Is user authorized to access to admin pages ?
	 */
	public function IsAuthorized(Smarty &$smarty, $PageTitle)
	{
		if (!$this->IsPasswordRequired())
		  return true;

		$password = self::GetCurrentPassword();
		if ($this->IsPasswordOK($password))
			return true;

		$msg_error = '';

		if (!is_null($password) && $password != '')
		{
		  $msg_error = __('Password', 'Wrong password');

			unset($_SESSION['admin_password']);
			unset($_POST['admin_password']);
		}

		//Ask for password :

		$smarty->assign('title', $PageTitle);
		
		$smarty->assign('password', $password);
		$smarty->assign('msg_error', $msg_error);

		$smarty->display('admin/login.tpl');

		return false;
	}
	
	/**
	 * @return bool
	 */
	public function IsPasswordRequired()
	{
		if (!is_object($this->RecVariable))  //No admin passsword defined => Migration not up to date => As before, password is not required.
			return false;

		return (!is_null($this->RecVariable->value) && !empty($this->RecVariable->value));
	}

	/**
	 * @param $PasswordToCheck
	 * @return bool
	 */
	public function IsPasswordOK($PasswordToCheck)
	{
		return (!is_null($PasswordToCheck) && PasswordHasher::verify($PasswordToCheck, $this->RecVariable->value));
	}

	/**
	 * Updates password in database
	 * 
	 * @param $Password
	 * @return bool (OK / KO)
	 */
	public function SavePassword($password)
	{
		$this->msg_error = null;

		if (!is_object($this->RecVariable))  //No admin passsword record defined => Migration not up to date => can't update admin password.
		{
			$this->msg_error = __('Admin', 'PleaseUpgrade');
			return false;
		}

		$SQLPwdHash = 'NULL';
		if (!is_null($password) && !empty($password))
			$SQLPwdHash = "'" . PasswordHasher::hash($password) . "'";

		$SQL = "update `" . Utils::table('variable') . "` set value = " . $SQLPwdHash . " where name = 'admin_pwd'";
		$this->connect->GetPDO()->exec($SQL);
		
		$_SESSION['admin_password'] = $password;
		
		return true;
	}

	/**
	 * Gets password from global data (session / post / etc.)
	 * 
	 * @return string
	 */
	public static function GetCurrentPassword()
	{
		$password = @$_SESSION['admin_password'];
		if (is_null($password) || empty($password))
		{
			$password = @$_POST['admin_password'];
			$_SESSION['admin_password'] = $password;
		}

		return $password;
	}

	/**
	 * Gets error message
	 * 
	 * @param AsHTML bool return message à html texte (\n replaced by <br/>)
	 * @return string
	 */
	public function GetMsgError()
	{
		return $this->msg_error;
	}
}
