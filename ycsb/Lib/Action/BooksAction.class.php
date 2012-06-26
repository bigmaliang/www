<?php
class BooksAction extends BaseAction 
{
    public function __construct()
    {
        parent::__construct();
        //招聘
        $model = M('news');
    	$z = $model->where(array('cate_id'=>40))->order('news_id desc')->limit('11')->findAll();
    	$r = $model->order('click desc')->limit('11')->findAll();
    	
    	$this->z = $z;
    	$this->r = $r;
    }
    
    public function index()
    {
    	$this->webtitle = '在线预订 ';
    	$msgModel = M("message");
    	$houseModel = M('house');
    	$house_id = $_GET['Index'];
    	$houseInfo = $houseModel->where(array('id'=>$house_id))->find();
    	if (!$houseInfo) {
    		redirect('/');
    	}
    	
    	if (isset($_POST['submit'])) {
    		$data = $_POST['data'];
    		$data['name'] = trim($data['name']);
    		$data['house_id'] = $house_id;
    		$data['mobile'] = trim($data['mobile']);
    		if (empty($data['name'])) js_alert('姓名不能为空', '/books');
    		$mobilePattern = '/(1)\d{10}/';
    		$result= preg_match($mobilePattern, $data['mobile']);
    		if (!$result) js_alert('手机格式错误');
    		
    		$data['addtime'] = date('Y-m-d H:i:s', time());
    		$msgModel->add($data);
    		js_alert('谢谢您的预订，我们会尽快跟您联系的！', '/books');
    	}
    	
    	$this->display();
    }
}
?>