<?php
class FangjianAction extends BaseAction 
{
    public function __construct()
    {
        parent::__construct();
        checkLogin();
    }
    
    public function index()
    {
    	Vendor('pagelist');
        $houseModel = M('house');
		
		if (isset($_POST['submit'])) {
			$cate_id = $_POST['cate_id'];
			$cate_id = $cate_id ? $cate_id : 0;
			redirect('/fangjian/index/cate_id/'.$cate_id.'/page/1');
		}

		$cate_id = $_GET['cate_id'] ? $_GET['cate_id'] : 0;
		if ($cate_id) {
			$cates = $this->getCate($cate_id);
			$where = " where cate_id in($cates)";
		}
		
        $page = $_GET['page'] ? $_GET['page'] : 1;
        $perpage = 20;
		$limit = ($page-1)*$perpage . ',' . $perpage;
		$cateModel = new CateModel();
		$list = $houseModel->query("select * from bian_house{$where} order by id desc limit $limit");
		
        foreach ($list as $key=>$value) {
        	$cate = $cateModel->where(array('cate_id'=>$value['cate_id']))->find();
        	$list[$key]['catename'] = $cate['cate_name'];
        }
        
        $count = $houseModel->query("select count(*) as count from bian_house{$where}");
        $count = $count[0]['count'];
		
		$cateModel = new CateModel();
        $option = $cateModel->option('', 'product');
        
        //分页
    	$pagelist = new PageList($count,$perpage,$page,'/fangjian/index/cate_id/'.$cate_id.'/page');
    	$this->pagelist = $pagelist->flickr();
        
		$this->option = $option;
        $this->list = $list;
        $this->display();
    }
    
    private function getCate($cate_id)
    {
    	$cateModel = new CateModel();
    	$list = $cateModel->where(array('cup'=>$cate_id, 'type'=>'product'))->findAll();
    	$array = array($cate_id);
    	foreach ((array)$list as $key=>$value) {
    		$array[] = $value['cate_id'];
    	}
    	return implode(',', $array);
    }
    
    public function add()
    {
    	$userinfo = $this->userinfo();
		$uid = $userinfo['uid'];
		$cateModel = new CateModel();
        $option = $cateModel->option('', 'product');
        //	$_SESSION['room_step'] = '';
       	$houseModel = M('house');
        $imageModel = M('image');
        if (isset($_POST['submit'])) {
        	$data = $_POST['data'];
        	$data['addtime'] = date('Y-m-d H:i:s', time());
        	
        	
        	$insert_id = $houseModel->add($data);
        	foreach ((array)$_SESSION['room_photos'] as $key=>$value) {
        		$imgData = array(
        			'house_id'=>$insert_id,
        			'imgurl'=>$value,
        			'name'=>' '
        		);
        		$result = $imageModel->add($imgData);
        	}
        	$houseModel->where(array('id'=>$insert_id))->save(array('imgurl'=>$value));
        	$_SESSION['room_photos'] = '';
        	js_alert('添加成功', '/fangjian');
        }
        
        $this->option = $option;
    	$this->session_id = session_id();
    	$this->display();
    }
    
    function edit()
    {
    	$houseModel = M('house');
        $imageModel = M('image');
        $id = $_GET['edit'];
        $info = $houseModel->where(array('id'=>$id))->find();
        $imglist = $imageModel->where(array('house_id'=>$id))->findAll();
        if (isset($_POST['submit'])) {
        	$data = $_POST['data'];
        	
        	foreach ((array)$_SESSION['room_photos'] as $key=>$value) {
        		if (!empty($value)) {
	        		$imgData = array(
	        			'house_id'=>$id,
	        			'imgurl'=>$value,
	        			'name'=>' '
	        		);
	        		$result = $imageModel->add($imgData);
        		}
        	}
        	foreach ((array)$_POST['name'] as $key=>$value) {
        		$order = $_POST['orders'][$key];
        		$imageModel->where(array('id'=>$key))->save(array('name'=>$value, 'orders'=>$order));
        	}
        	$result = $houseModel->where(array('id'=>$id))->save($data);
        	$_SESSION['room_photos'] = '';
        	js_alert('修改成功', '/fangjian');
        }
		$info['content'] = stripslashes($info['content']);
        
        $cateModel = new CateModel();
        $option = $cateModel->option($info['cate_id'], 'product');
        $this->option = $option;
        $this->imglist = $imglist;
        $this->info = $info;
    	$this->display();
    }
    
    function delhouse()
    {
    	$id = $_GET['delhouse'];
    	
    	if (!empty($id)) {
    		$houseModel = M('house');
       	 	$imageModel = M('image');
       	 	$houseModel->where(array('id'=>$id))->delete();
       	 	$imageModel->where(array('house_id'=>$id))->delete();
    		js_alert('删除成功', '/fangjian');
    	}
    }
    
    function delimg()
    {
    	$id = $_POST['id'];
    	if (!empty($id)) {
    		$imageModel = M('image');
    		$imageModel->where(array('id'=>$id))->delete();
    		echo '1';
    	}
    }
    
    function setimg()
    {
    	$id = $_POST['id'];
    	$imgurl = $_POST['imgurl'];
    	if ($id && $imgurl) {
    		$houseModel = M('house');
    		$result = $houseModel->where(array('id'=>$id))->save(array('imgurl'=>$imgurl));
    		echo '1';
    	}
    }

}
