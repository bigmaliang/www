<?php
class MessageAction extends BaseAction 
{
    function __construct()
    {
        parent::__construct();
        checkLogin();
    }
	
    public function index()
    {
    	$messageModel = M('message');
    	$houseModel = M('house');
    	
    	$list = $messageModel->order('id desc')->where()->findall();
    	foreach ($list as $key=>$value) {
    		$house_info = $houseModel->where(array('id'=>$value['house_id']))->find();
    		$list[$key]['title'] = $house_info['title'];
    	}
    	
    	
    	$this->list = $list;
    	$this->display();
    }
    
    function reply()
    {
    	$id = $_GET['reply'];
    	if (!empty($id)) {
    		$content = $_POST['content'];
    		if ($content) {
    			$replyModel = M('reply');
    			$data = array(
    				'message_id'=>$id,
    				'content'=>$content,
    				'addtime'=>date('Y-m-d H:i:s', time()),
    			);
    			$replyModel->add($data);
    			js_alert('回复留言成功', '/message');
    		}
    	}
    	$this->display();
    }
    
    public function delete()
    {
    	$id = $_GET['delete'];
    	if (!empty($id)) {
    		$messageModel = M('message');
    		$messageModel->where(array('id'=>$id))->delete();
    		js_alert('删除成功', '/message');
    	}
    }
}
