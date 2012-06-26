<?php
class PaperAction extends BaseAction 
{
    public function __construct()
    {
        parent::__construct();
    }

	public function index()
    {
		$id = $_GET['Index'];
		
		$paperModel = M('paper');
		
		$paper = $paperModel->where(array('id' => $id, 'statu'=>0))->find();
		
		$papers = $paperModel->where(array('pid'=>$id, 'statu'=>0))->order('id desc')->findAll();
		
		$this->LayoutTitle = $paper['title'];
		
		$this->paper = $paper;
		$this->papers = $papers;
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
                    js_alert ('用户密码或者密码错误！', '/index/login');
                } else {
                    $login->keepUser();
                    redirect('/');
                }                
            }
        }
        $this->display();
    }

    public function logout()
    {
        $login = new LoginClass();
        $login->quitUser();
        redirect('/index/login');
    }
}
?>
