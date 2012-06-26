<?php
class UserAction extends BaseAction 
{
    public function __construct()
    {
        parent::__construct();
        checkLogin();
    }

	public function index()
	{
        $page = $_GET['index'] ? $_GET['index'] : 1;
        $perpage = 20;
        $limit = ($page-1)*$perpage . ',' . $perpage;
            
        $userModel = M('user');
        $sql = "select A.*,B.groupname from bian_user A LEFT JOIN bian_group B ON A.groupid=B.group_id";
        $list = $userModel->query($sql);
        $this->list = $list;
        $this->display();
	}

    public function add()
    {
        $groupModel = M('group');
        $userModel = M('user');
        $groupList = $groupModel->findAll();
        if (isset($_POST['submit'])) {
            $data = $_POST['data'];
            $data['username'] = trim($data['username']);
            $user = $userModel->where(array('username'=>$data['username']))->find();
            if (isset($user['uid'])) {
                js_alert('用户名已经存在', '/user');exit;
            }
            $data['password'] = md5($_POST['password']);
            $data['created'] = time();
            $userModel->add($data);
            js_alert('添加成功', '/user');
        }

        $this->group = $groupList;
        $this->display();
    }

    public function edit()
    {
        $uid = $_GET['edit'];
        $groupModel = M('group');
        $userModel = M('user');
        $groupList = $groupModel->findAll();
        $userinfo = $userModel->where(array('uid'=>$uid))->find();
        if (isset($_POST['submit'])) {
            $data = $_POST['data'];
            if ($_POST['password']) {
                $data['password'] = md5($_POST['password']);
            }
            $userModel->where(array('uid'=>$uid))->save($data);
            js_alert('修改成功', '/user');
        }
        $this->userinfo = $userinfo;
        $this->group = $groupList;
        $this->display();
    }

    public function delete()
    {
        $uid = $_GET['delete'];
        if (!empty($uid)) {
            $userModel = M('user');
            $userModel->where(array('uid'=>$uid))->delete();
            js_alert('删除成功', '/user');
        }
    }
}
?>
