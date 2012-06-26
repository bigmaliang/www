<?php
/**
 * 本类为自定义action类
 * 主要为了放置所有的模块之前需要运行的代码
 * 注意，之后所有的模块都应该继承该类而非Action基类
 * evila
 */

class BaseAction extends Action
{
    var $logininfo;
    public function __construct()
    {
        parent::__construct();
        //登陆信息  
        $login = new LoginClass();
        $this->logininfo = array(
            'uid' => $login->getUserID(),
            'username' => $login->getUserName(),
            'groupid' => $login->getGroupId(),
            'truename' => $login->getTrueName(),
        );
		
		if (strpos($GLOBALS['REQUEST_URI']) && !self::loginUrl()) {
			//redirect('/admin/login');
		}

		
		$model = M('nav');
		$paperModel = M('paper');
		
		$navs = $model->findAll();
   		$id = $_GET['Index'] ? $_GET['Index'] : 1;
   		$paper = $paperModel->where(array('id'=>$id))->find();
    	
    	foreach ($navs as $key => $nav) {
			$paperinfo = $paperModel->where(array('id'=>$nav['pid']))->find();
			$navs[$key]['title'] = $paperinfo['title'];
			$navs[$key]['name'] = $paperinfo['title'];

			if ($navs[$key]['pid'] == 1)
				$navs[$key]['href'] = '/index.php';
			else
				$navs[$key]['href'] = '/paper/Index/' . $navs[$key]['pid'];

			if ($nav['pid'] == $id || $nav['pid'] == $paper['pid']) {
				$navs[$key]['class'] = 'selected';
			}
    	}
    	
    	$this->LayoutTabs = $navs;
		/*
        if (!$this->logininfo['uid']) {
            if (!self::loginUrl()) {
                redirect('/admin/login');
            }
        }
		*/
    }

    static function loginUrl()
    {
        $array = array(
            '/admin/login',   
            '/admin/quit'
        );
        $REQUEST_URI = $_SERVER['REQUEST_URI'];
        if (in_array($REQUEST_URI, $array)) {
            return true;
        }
    }

    public function userinfo()
    {
        $uid = $this->logininfo['uid'];
        $userModel = M('user');
        $data = $userModel->where(array('uid'=>$uid))->find();
        return $data;
    }
}

?>
