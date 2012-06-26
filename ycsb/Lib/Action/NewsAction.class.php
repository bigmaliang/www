<?php
class NewsAction extends BaseAction 
{
    public function __construct()
    {
        parent::__construct();
    }
  	
    public function index()
    {
    	$model = M('news');
    	$cateModel = new CateModel();
    	$catelist = $cateModel->getList(array('type'=>'news'));
    	$cate_id = $_GET['Index'];
    	$where = array();
    	if (is_numeric($cate_id) && $cate_id) {
    		$where = array('cate_id'=>$cate_id);
    		$cate = $cateModel->getOne($cate_id);
    		$this->cate = $cate;
    	}
    	$list = $model->where($where)->order('news_id desc')->findAll();
    	foreach ($list as $key=>$value) {
    		$cateInfo = $cateModel->getOne($value['cate_id']);
    		$list[$key]['catename'] = $cateInfo['cate_name'];
    	}
    	if ($cate) 
    		$this->webtitle = $cate['cate_name'].' ';
    	else 
    		$this->webtitle = '公寓动态 ';
    	$this->list = $list;
    	$this->catelist = $catelist;
    	$this->display();
    }
    
    public function info()
    {
    	$news_id = $_GET['info'];
    	if (!$news_id) redirect('/');
    	
    	$model = M('news');
    	$cateModel = new CateModel();
    	$newsInfo = $model->where(array('news_id'=>$news_id))->find();
		$newsInfo['content'] = stripslashes($newsInfo['content']);
    	$model->where(array('news_id'=>$news_id))->save(array('click'=>$newsInfo['click']+1));
    	
    	if (!$newsInfo) redirect('/');
    	

    	$cate_id = $newsInfo['cate_id'];
    	//分类
    	$catelist = $cateModel->getList(array('type'=>'news'));
    	//最新
    	$list = $model->where($where)->order('news_id desc')->limit(6)->findAll();
    	
    	$this->list = $list;
    	$this->catelist = $catelist;
    	$this->cate = $cateModel->getOne($cate_id);
		$this->news_id = $news_id;
    	$this->webtitle = $newsInfo['title']. ' ';
    	$this->info = $newsInfo;
    	$this->display();
    }    

	public function upload()
	{
		
		//filedata
		$savePath = 'upload/'.date('Y-m-d', time()).'/';
		$this->dirCreate($savePath);
		$saveName = $this->setSaveName();
		$filename = $savePath . $saveName;
		
		
		
		if (move_uploaded_file($_FILES['filedata']['tmp_name'], $filename) || @copy($_FILES['filedata']['tmp_name'], $filename)) {
			exit(stripslashes(json_encode(array('err'=>'', 'msg'=>'/'.$filename))));
		}

		//


	}

	function setSaveName()
	{
		$string = array("A","B","C","D","E","F","G","H","I","J","L","M","N","P","Q","R","S","T",
					"U","V","W","X","Y","Z","1","2","3","4","5","6","7","8","9","a","b","c",
					"d","e","f","g","h","i","j","k","l","m","n","p","q","r","s","t","u","v",
					"w","x","y","z");
		
		$rand=array_rand($string,16);
		for($i=0;$i<=15;$i++)
			$code .= $string[$rand[$i]];
		return $code.'.'.'jpg';	
	}
	
	function dirCreate($path, $mod = 0777)
	{
		$path = $this->dir_path($path);
		$pathArr = explode('/', $path);
		$pathNum = count($pathArr) -1;
		for ($i=0; $i<$pathNum; $i++)
		{
			$createPath .= $pathArr[$i].'/';
			if (!@is_dir($createPath)) {
				@mkdir($createPath, $mod);
				@chmod($createPath, $mod);
			}
		}
		return is_dir($path);
	}
	function dir_path($path)
	{
		$path = str_replace('\\','/', $path);
		if (substr($path, -1) != '/') $path = $path.'/';
		return $path;
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
    
    private function getNav($cate_id)
    {
    	$cateModel = new CateModel();
    	$cate = $cateModel->where(array('cate_id'=>$cate_id))->find();
    	$cateInfo = $cateModel->where(array('cup'=>$cate['cup']))->find();
    	$nav = '';
    	if ($cate['cup']) {
    		$nav .= '&gt; <a href="/news/cate/'.$cateInfo['cate_id'].'" title="">'.$cateInfo['cate_name'].'</a>';
    	}
    	$nav .= '&gt; <a href="'.$cate['cate_id'].'" title="">'.$cate['cate_name'].'</a>';
    	return $nav;
    }
    
}
?>