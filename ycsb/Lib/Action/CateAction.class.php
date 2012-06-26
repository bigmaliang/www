<?php
class CateAction extends Action
{
    function __construct() {
        parent::__construct();
        checkLogin();
    }

    public function index()
    {
        $type = $_GET['index'] ? $_GET['index'] : 'news';
        $cateModel = new CateModel();
        $list = $cateModel->getBackList(array('type'=>$type, 'cup'=>0));
        $this->list = $list;
        $this->type = $type;
        $this->display();
    }

    public function add()
    {
        $cateModel = new CateModel();
        $type = $_GET['add'] ? $_GET['add'] : 'news';
        $option = $cateModel->option('', $type);
        if (isset($_POST['submit'])) {
            $data = $_POST['data'];
            $data['type'] = $type;
            $result = $cateModel->insert($data);
            if ($result) js_alert('添加成功', '/cate/add/'.$type);
            else js_alert ('添加失败，请重试', '/cate/add/'.$type);
        }
        $this->type = $type;
        $this->option = $option;
        $this->display();
    }

    public function edit()
    {
        $cate_id = $_GET['edit'];
        $cateModel = new CateModel();
        if (isset($_POST['submit'])) {
            $data = $_POST['data'];
            $result = $cateModel->set(array('cate_id'=>$cate_id), $data);
            js_alert('修改成功', '/cate/index/'.$data['type']);
        }
        $info = $cateModel->getOne($cate_id);
        $this->option = $cateModel->option($cate_id);
        $this->info = $info;
        $this->type = $type;
        $this->display();
    }

    public function delete()
    {
        $cate_id = $_GET['delete'];
        if ($cate_id) {
            $cateModel = new CateModel();
            $cateModel->where(array('cate_id'=>$cate_id))->delete();
            js_alert('删除成功', '/cate/index/');
        }
        
    }
}



?>
