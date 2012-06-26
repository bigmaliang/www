<?php
/**
 * 用户登陆类 （包括管理员跟一切角色）
 * 
 * @author  rewind 
 * @date 2009-2-24
 */

class LoginClass
{
	var $account;
	var $username = '';
	var $password = '';
	var $userID   = '';
	var $groupID  = '';
	var $trueName  = '';
	var $type = '';
	var $keepUserId = 'admin_id';
	var $keepUsername = 'admin_user';
	var $keepGroupID  = 'admin_groupid';
	var $keepTrueName  = 'admin_truename';
	var $keepType  = 'admin_type';
	
	function __construct()
	{
        global $db;
        $this->account = M('member');
		if (isset($_SESSION[$this->keepUserId]))
		{
			$this->userID = $_SESSION[$this->keepUserId];
			$this->username = $_SESSION[$this->keepUsername];
			$this->groupID = $_SESSION[$this->keepGroupID];
			$this->trueName = $_SESSION[$this->keepTrueName];
			$this->type = $_SESSION[$this->keepType];
		}
	}

	function admin_login()
	{
		$this->__construct();
	}

	/**
	 * 验证用户登陆
	 *@return true or false
	 */
	function checkUser($username, $password)
	{
		$this->username = ereg_replace("[^0-9a-zA-Z_@\!\.-]","",$username);
		$this->password = ereg_replace("[^0-9a-zA-Z_@\!\.-]","",$password);
        //$this->password = md5($this->password);
        $result = $this->account->where(array('mname'=>$this->username, 'msn'=>$this->password))->limit(1)->find();
        
		if (!$result)
		{
			return -1;
		}
		else
        {
			$loginip = GetIP();
			$this->userID    = $result['mname'];
			//$this->groupID   = $result['groupid'];
			$this->groupID   = 1;
			$this->username  = $result['mname'];
            $this->trueName  = $result['mname'];
            /*
            $updateData = array(
                'lastlogintime' => time(),
                'loginnum' => $result['loginnum'] + 1,
                'lastloginip' => $loginip
            );
            $this->account->where(array('uid'=>$result['uid']))->save($updateData);
            */
		}
	}

	/**
	 * 保存用户的状态 0表示登陆失败
	 *@return int
	 */
	function keepUser()
	{
		if ($this->userID)
		{
			session_register($this->keepUserId);
			$_SESSION[$this->keepUserId] = $this->userID;

			session_register($this->keepUsername);
			$_SESSION[$this->keepUsername] = $this->username;

			session_register($this->keepGroupID);
			$_SESSION[$this->keepGroupID] = $this->groupID;	
					
			session_register($this->keepTrueName);
			$_SESSION[$this->keepTrueName] = $this->trueName;
		}
	}

	/**
	 * 获取用户的ID值
	 *@return int
	 */
	function getUserID()
	{
		if($this->userID!="") return $this->userID;
		else return 0;
	}

	/**
	 * 获取用户的用户名
	 *@return int
	 */
	function getUserName()
	{
		if($this->username!="") return $this->username;
		else return 0;
	}

	/**
	 * 获取用户的用户组ID值
	 *@return int
	 */
	function getGroupId()
	{
		if($this->groupID!="") return $this->groupID;
		else return 0;
	}
	
	/**
	 * 获取用户的姓名
	 *@return string
	 */
	function getTrueName()
	{
		if($this->trueName!="") return $this->trueName;
		else return 0;
	}
	
	/**
	 * 注销用户
	 *@return int
	 */
	function quitUser()
	{
		@session_unregister($this->keepUserId);
		@session_unregister($this->keepUsername);
		@session_unregister($this->keepGroupID);
		session_destroy();

	}
}	
?>
