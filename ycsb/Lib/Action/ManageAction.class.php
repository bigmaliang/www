<?php

class ManageAction extends BaseAction 
{
    public function __construct()
    {
        parent::__construct();
        //checkLogin();
    }

	private function checklogin()
	{
		if (!$_SESSION['admin_id']) {
			$this->errcode = 25;
			$this->errmsg = '请登录后操作';
	
			$this->display();
			exit;
		} else {
			$this->username = $_SESSION['admin_user'];
			$this->LayoutActions = $GLOBALS['adminActions'];
		}
	}

	public function index()
    {
		$this->checklogin();

        $this->display();
    }
    
    public function paper()
    {
		$this->checklogin();

		$paperModel = M('paper');
		
		$papers = $paperModel->where(array('statu'=>0))->order('id desc')->findAll();
		
		$this->papers = $papers;

        $this->display();
	}
	
	public function papernew()
	{
		$this->checklogin();
		
		$model = M('nav');
		$paperModel = M('paper');
		
		$navs = $model->findAll();
    	
    	foreach ($navs as $key => $nav) {
			$paperinfo = $paperModel->where(array('id'=>$nav['pid']))->find();
			$navs[$key]['title'] = $paperinfo['title'];
    	}
    	
    	$this->navs = $navs;

        $this->display();
	}

	public function papernewsave()
	{
		$this->checklogin();

		$data = $_POST;

		$paperModel = M('paper');
		
		$paperModel->add($data);
		
		echo json_encode(array('success'=> 1));
		exit;
	}

	public function paperedit()
	{
		$this->checklogin();

		$id = $_GET['paperedit'];
		
		$paperModel = M('paper');
		
		$paper = $paperModel->where(array('id'=>$id))->find();
		
		$this->paper = $paper;

        $this->display();
	}

	public function papermod()
	{
		$this->checklogin();
		
		$paperModel = M('paper');
		
		$updateData = array();
		foreach ($_POST as $key=>$val) {
			if ($key != 'id')
				$updateData[$key] = $val;
		}
		
		//$updateData = array('statu'=>$_POST['statu']);
		
		$paperModel->where(array('id'=>$_POST['id']))->save($updateData);
		
		echo json_encode(array('success'=> 1));
		exit;
	}

    public function guide()
    {
        $groupModel = M('group');
        $this->groupinfo = $groupModel->where(array('group_id'=>$this->logininfo['groupid']))->find();
        $userinfo = $this->userinfo();
        $this->userinfo = $userinfo;
        $this->display();
    }

	public function login()
    {
		$mname = $_GET['mname'] ? trim($_GET['mname']) : ' ';
		$msn = $_GET['msn'] ? trim($_GET['msn']) : ' ';
		
		$login = new LoginClass();
		$result = $login->checkUser($mname, $msn);
		if ($result == '-1') {
			echo json_encode(array('errcode'=>30, 'errmsg'=>'密码错误'));
		} else {
			$login->keepUser();
			echo json_encode(array('success'=>'1'));
		}
		exit;
    }

    public function logout()
    {
        $login = new LoginClass();
        $login->quitUser();
		echo json_encode(array('success'=>'1'));
		exit;
    }
    
    public function upload()
    {
    	$model = M('img');
    	$id = $_GET['upload'];
    	if (!empty($id)) {
    		$info = $model->where(array('id'=>$id))->find();
    		$this->info = $info;
    		if (isset($_POST['submit'])) {
	    		$data = $_POST['data'];
	    		if ($_FILES['imgurl']['name'][0]) {
		        	$savePath = 'upload/'.date('Y-m-d', time());
		        	$up = new UploadClass('imgurl', $savePath, 'jpg|jpeg|gif|bmp|png', 10000000);
		        	$image = $up->up();
		        	$data['imgurl'] = $image[0]['saveto'];
    			}
	        	$model->where(array('id'=>$id))->save($data);
	        	js_alert('修改成功', '/admin/upload');
	    	}
    	} else {
	    	if (isset($_POST['submit'])) {
	    		$data = $_POST['data'];
	    		//上传图片
	        	$savePath = 'upload/'.date('Y-m-d', time());
	        	$up = new UploadClass('imgurl', $savePath, 'jpg|jpeg|gif|bmp|png', 10000000);
	        	$image = $up->up();
	        	$data['imgurl'] = $image[0]['saveto'];
	        	$model->add($data);
	        	js_alert('添加成功', '/admin/upload');
	    	}    		
    	}
    	$this->list = $model->findAll();
    	$this->display();
    }
    
    function control()
    {
    	$model = M('control');
    	$id = $_GET['control'];
    	if (!empty($id)) {
    		$info = $model->where(array('id'=>$id))->find();
    		$this->info = $info;
    		if (isset($_POST['submit'])) {
	    		$data = $_POST['data'];
	    		if ($_FILES['imgurl']['name'][0]) {
		        	$savePath = 'upload/'.date('Y-m-d', time());
		        	$up = new UploadClass('imgurl', $savePath, 'jpg|jpeg|gif|bmp|png', 10000000);
		        	$image = $up->up();
		        	$data['imgurl'] = $image[0]['saveto'];
    			}
	        	$model->where(array('id'=>$id))->save($data);
	        	js_alert('修改成功', '/admin/control');
	    	}
    	} else {
	    	if (isset($_POST['submit'])) {
	    		$data = $_POST['data'];
	    		//上传图片
	        	$savePath = 'upload/'.date('Y-m-d', time());
	        	$up = new UploadClass('imgurl', $savePath, 'jpg|jpeg|gif|bmp|png', 10000000);
	        	$image = $up->up();
	        	$data['imgurl'] = $image[0]['saveto'];
	        	$model->add($data);
	        	js_alert('添加成功', '/admin/control');
	    	}    		
    	}
    	$this->list = $model->findAll();
    	$this->display();
    }
}
?>
