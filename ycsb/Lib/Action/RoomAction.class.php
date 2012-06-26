<?php
class RoomAction extends BaseAction 
{
	var $aboutModel;
    function __construct()
    {
        parent::__construct();
    }
    
    function index()
    {
    	$cateModel = new CateModel();
    	//分类
    	$catelist = $cateModel->getList(array('type'=>'product'));
    	$cate_id = $_GET['Index'];
    	
    	if (is_numeric($cate_id) && $cate_id) {
    		$where = array('cate_id'=>$cate_id);
    		$cate = $cateModel->getOne($cate_id);
    		$this->cate = $cate;
    	}
    	$houseModel = M('house');
    	
    	if ($cate)
			$this->webtitle = $cate['cate_name'].' ';    		
    	else 
    		$this->webtitle = '公寓客房 ';
    	
    	$list = $houseModel->where($where)->order('id desc')->findAll();
    	
    	$this->list = $list;
    	$this->catelist = $catelist;
    	$this->display();
    }
    
    function info()
    {
    	$houseModel = M('house');
    	$houseImageModel = M('image');
    	$cateModel = new CateModel();
    	//分类
    	$catelist = $cateModel->getList(array('type'=>'product'));
    	$id = $_GET['info'];
    	//房间信息
    	$house_info = $houseModel->where(array('id'=>$id))->find();
    	$house_info['content'] = stripslashes($house_info['content']);
    	
    	if (!$house_info) redirect('/room');
		//图片信息
		$house_image = $houseImageModel->where(array('house_id'=>$id))->order('name asc')->findAll();
    	
		$this->house = $house_info;
		$this->house_image = $house_image;
    	$this->catelist = $catelist;
    	$this->webtitle = $house_info['title']. ' ';
    	$this->display();
    }
    
    
}
?>