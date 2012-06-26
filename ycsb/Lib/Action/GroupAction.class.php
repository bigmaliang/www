<?php
class GroupAction extends BaseAction 
{
    public function __construct()
    {
        parent::__construct();
        checkLogin();
    }

	public function index()
	{
        $groupModel = M('group');
        $list = $groupModel->select();
        $this->list = $list;
        $this->display();
	}

    public function add()
    {
        if (isset($_POST['submit'])) {
			$groupname = $_POST['groupname'];
			$targets = $_POST['targets'];
			if (!$groupname) js_alert('用户组名称不能为空！', '/group/add');
            if (!$targets) js_alert('请选择权限模板！', '/group/add');
            $data = array('groupname'=>$groupname, 'mright'=>serialize($targets));
            $group = M('group');
            $result = $group->add($data);
            //js_redirect('/group');
        }

        $menuArray = require 'Conf/menu.php';
        $this->listmenu = $menuArray;

        $this->display();
    }

    public function edit()
    {
        $groupId = $_GET['edit'];
        $groupModel = M('group');
        if (isset($_POST['submit'])) {
			$groupname = $_POST['groupname'];
			$targets = $_POST['targets'];
			if (!$groupname) js_alert('用户组名称不能为空！', '/group');
			if (!$targets) js_alert('请选择权限模板！', '/group');
            $data = array('groupname'=>$groupname, 'mright'=>serialize($targets));
            $groupModel->where(array('group_id'=>$groupId))->save($data);
            js_alert('修改成功', '/group');
        }

        $menuArray = require 'Conf/menu.php';
        $info = $groupModel->where(array('group_id'=>$groupId))->find();
        $mright = unserialize(stripslashes($info['mright']));
        $this->info = $info;
        $this->listmenu = $menuArray;
        $this->mright = $mright;
        $this->display();
    }

    public function delete()
    {
        $groupId = $_GET['delete'];
        $groupModel = M('group');
        if ($groupId) {
            $result = $groupModel->where(array('group_id'=>$groupId))->limit(1)->delete();
            dump($result);
        }
    }
}
?>
