<?php
class AboutAction extends BaseAction 
{
	var $aboutModel;
    function __construct()
    {
        parent::__construct();
        $this->aboutModel = M('about');
        
    }
	
    public function index()
    {
    	$this->intro();
    }
    public function intro()
    {
        $info = $this->aboutModel->where(array('type'=>1))->find();
		$info['content'] = stripslashes($info['content']);
        $this->webtitle = '关于我们 ';
        $this->info = $info;
        $this->flag = 1;
        $this->display();
    }

    public function contactus()
    {
        $info = $this->aboutModel->where(array('type'=>4))->find();
        $this->webtitle = '联系我们 ';
        $this->info = $info;
        $this->flag = 4;
        $this->display();
    }
    
    public function honor()
    {
    	$this->flag = 2;
    	$this->display();
    }
    
    public function policy()
    {
    	$newsModel = M('news');
    	$this->info = $newsModel->where(array('news_id'=>1))->find();
    	$this->flag = 3;
    	$this->display();
    }
}
