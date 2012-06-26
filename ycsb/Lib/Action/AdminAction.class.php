<?php

class AdminAction extends BaseAction 
{
    public function __construct()
    {
        parent::__construct();
        checkLogin();
    }

	public function index()
    {
        $permiss = new PermissClass($this->logininfo['groupid']);
        $leftMenu = $permiss->leftmenu();
        $this->userinfo = $this->logininfo;
        $this->leftMenu = $leftMenu;
        $this->display();
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
        if (isset($_POST['submit'])) {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);
            $login = new LoginClass();
            if ($username && $password) {
                $result = $login->checkUser($username, $password);
                if ($result == '-1') {
                    js_alert ('用户密码或者密码错误！', '/admin/login');
                } else {
                    $login->keepUser();
                    redirect('/admin');
                }                
            }
        }
        $this->display();
    }

    public function logout()
    {
        $login = new LoginClass();
        $login->quitUser();
        redirect('/admin/login');
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
