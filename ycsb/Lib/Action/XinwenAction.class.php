<?php
class XinwenAction extends BaseAction 
{
    public function __construct()
    {
        parent::__construct();
        checkLogin();
    }
    
    public function index()
    {
    	Vendor('pagelist');
        $newsModel = M('news');
		
		if (isset($_POST['submit'])) {
			$cate_id = $_POST['cate_id'];
			redirect('/xinwen/index/cate_id/'.$cate_id.'/news_id/');
		}

		$cate_id = $_GET['cate_id'];
		
		if ($cate_id) {
			$cates = $this->getCate($cate_id);
			
			$where = " where cate_id in($cates)";
		}
		
        $page = $_GET['news_id'] ? $_GET['news_id'] : 1;
        $perpage = 15;
		$limit = ($page-1)*$perpage . ',' . $perpage;
		$cateModel = new CateModel();
		$list = $newsModel->query("select * from bian_news{$where} order by news_id desc limit $limit");
        foreach ($list as $key=>$value) {
        	$cate = $cateModel->where(array('cate_id'=>$value['cate_id']))->find();
        	$list[$key]['catename'] = $cate['cate_name'];
        }
        
        $count = $newsModel->query("select count(*) as count from bian_news{$where}");

        $count = $count[0]['count'];
		
		$cateModel = new CateModel();
        $option = $cateModel->option();
		
        
        //分页
    	$pagelist = new PageList($count,$perpage,$page,'/xinwen/index/cate_id/'.$cate_id.'/news_id');
    	$this->pagelist = $pagelist->flickr();
        
		$this->option = $option;
        $this->list = $list;
        $this->display();
    }
    private function getCate($cate_id)
    {
    	$cateModel = new CateModel();
    	
    	$list = $cateModel->where(array('cup'=>$cate_id))->findAll();
    	$array = array($cate_id);
    	foreach ((array)$list as $key=>$value) {
    		$array[] = $value['cate_id'];
    	}
    	
    	return implode(',', $array);
    }
    public function add()
    {
    	$cateModel = new CateModel();
        $option = $cateModel->option('', 'news');
    	$newsModel = M('news');
        if (isset($_POST['submit'])) {
        	$data = $_POST['data'];
        	if ($_FILES['imgurl']['name'][0]) {
        		//上传图片
	        	$savePath = 'upload/'.date('Y-m-d', time());
	        	$up = new UploadClass('imgurl', $savePath, 'jpg|jpeg|gif|bmp|png|JPG|JPEG|BMP|GIF|PNG', 10000000);
	        	$image = $up->up();
				
	        	$data['imgurl'] = $image[0]['saveto'];
        	}
        	$data['addtime'] = date('Y-m-d H:i:s', time());
        	$newsModel->add($data);
        	js_alert('添加成功', '/xinwen');
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
	        	$up = new UploadClass('imgurl', $savePath, 'jpg|jpeg|gif|bmp|png|JPG|JPEG|BMP|GIF|PNG', 10000000);
	        	$image = $up->up();
	        	$data['imgurl'] = $image[0]['saveto'];
        	}
    		$newsModel->where(array('news_id'=>$id))->save($data);
    		js_alert('修改成功', '/xinwen');
    	}
    	$cateModel = new CateModel();
    	$info = $newsModel->where(array('news_id'=>$id))->find();
		$info['content'] = stripslashes($info['content']);
    	$this->info = $info;
    	$option = $cateModel->newsoption($info['cate_id']);
        $this->option = $option;
        $this->display();
    }

    public function delete()
    {
        $id = $_GET['delete'];
        $newsModel = M('news');
        $newsModel->where(array('news_id'=>$id))->delete();
        js_alert('删除成功', '/xinwen');
    }
    
    public function image()
    {
    	$hud = M('hud');
    	if (isset($_POST['submit'])) {
    		$data = array(
    			'title'=>$_POST['title'],
    			'url'=>$_POST['url']
    		);
    		if (!$_FILES['imgurl']['name'][0]) {
        		js_alert('图片不能为空');
        	} else {
        		//上传图片
	        	$savePath = 'upload/'.date('Y-m-d', time());
	        	$up = new UploadClass('imgurl', $savePath, 'jpg|jpeg|gif|bmp|png', 50000000);
	        	$image = $up->up();
	        	$data['imgurl'] = $image[0]['saveto'];
        	}
        	$hud->add($data);
        	js_alert('添加成功', '/xinwen/image');
    	}
    	$list = $hud->findAll();
    	$this->list = $list;
    	$this->display();
    }
    
    function del()
    {
    	$id = $_GET['del'];
    	if (!empty($id)) {
    		$hud = M('hud');
    		$hud->where(array('id'=>$id))->delete();
    		js_alert('删除成功', '/xinwen/image');
    	}
    }
}
