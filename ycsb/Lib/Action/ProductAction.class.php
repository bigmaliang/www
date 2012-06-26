<?php
class ProductAction extends BaseAction 
{
    public function __construct()
    {
        parent::__construct();
        checkLogin();
    }
    
    public function index()
    {
        $newsModel = M('news');
        $this->list = $newsModel->findAll();
        $this->display();
    }

    public function add()
    {
    	$cateModel = new CateModel();
        $option = $cateModel->option('', $this->language);
    	$newsModel = M('news');
        if (isset($_POST['submit'])) {
        	$data = $_POST['data'];
        	//上传图片
        	$savePath = 'upload/'.date('Y-m-d', time());
        	$up = new UploadClass('imgurl', $savePath, 'jpg|jpeg|gif|bmp|png', 10000000);
        	$image = $up->up();
        	$data['imgurl'] = $image[0]['saveto'];
        	$data['addtime'] = date('Y-m-d H:i:s', time());
        	$result = $newsModel->add($data);
        	js_alert('添加成功', '/product');
        }
        $this->option = $option;
    	$this->display();
    }

    public function edit()
    {
    	$id = $_GET['edit'];
    	$newsModel = M('news');
    	if (isset($_POST['submit'])) {
    		$data = $_POST['data'];
    		if ($_FILES['imgurl']['name'][0]) {
    			//上传图片
	        	$savePath = 'upload/'.date('Y-m-d', time());
	        	$up = new UploadClass('imgurl', $savePath, 'jpg|jpeg|gif|bmp|png', 1000000);
	        	$image = $up->up();
	        	$data['imgurl'] = $image[0]['saveto'];
    		}
    		$newsModel->where(array('news_id'=>$id))->save($data);
    		js_alert('修改成功', '/product');
    	}
    	$cateModel = new CateModel();
    	$info = $newsModel->where(array('news_id'=>$id))->find();
    	$this->info = $info;
    	$option = $cateModel->newsoption($info['cate_id'], $this->language);
        $this->option = $option;
        $this->display();
    }

    public function delete()
    {
        $id = $_GET['delete'];
        $newsModel = M('news');
        $newsModel->where(array('news_id'=>$id))->delete();
        js_alert('删除成功', '/product');
    }
}
