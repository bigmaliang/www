<?php

class AboutusAction extends BaseAction 
{
    function __construct()
    {
        parent::__construct();
        checklogin();
    }

    function index()
    {
        $type = $_GET['index'];
        $array = array(
            1=>'关于我们',    
            2=>'荣誉资质' 
        );
        $aboutModel = M('about');
        $info = $aboutModel->where(array('type'=>$type, 'tab'=>1))->find();
		$info['content'] = stripslashes($info['content']);
        if (isset($_POST['submit'])) {
            $content = $_POST['content'];
            $content = stripslashes($content);
            if ($info) {
                $data = array('content'=>$content);
                $result = $aboutModel->where(array('type'=>$type, 'tab'=>1))->save($data);
            } else {
                $data = array(
                    'type'=>$type,    
                    'content'=>$content
                );
                $result = $aboutModel->add($data);
            }
            js_alert ('添加成功', '/aboutus/index/'.$type);
        }


        $this->array = $array;
        $this->type = $type;
        $this->info = $info;

        $this->display();
    }
    
    function siteset()
    {
    	$model = M('siteset');
    	if (isset($_POST['submit'])) {
    		$data = $_POST['data'];
    		foreach ($data as $key=>$value) {
    			$info = $model->where(array('name'=>$key))->find();
    			if ($info) {
    				$model->where(array('name'=>$key))->save(array('value'=>$value));
    			} else {
    				$model->add(array('name'=>$key, 'value'=>$value));
    			}
    		}
    		js_alert('修改成功', '/aboutus/siteset');
    	}
    	$info = $model->findAll();
    	$array = array();
    	foreach ($info as $key=>$value) {
    		$array[$value['name']][] = $value['value'];
    	}
    	$this->site = $array;
    	$this->display();
    }
}
