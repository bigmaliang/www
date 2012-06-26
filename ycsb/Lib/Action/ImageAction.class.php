<?php
if (isset($_POST['__ssid__'])) 
	session_id($_POST['__ssid__']);
			
class ImageAction extends BaseAction 
{
    public function __construct()
    {
        parent::__construct();
    }	
    
    public function index()
    {
		//$userinfo = $this->userinfo();
		//$uid = $userinfo['uid'];
		
		$uid = '123123';
		$room_id = '123123';
		
		//处理图片 
		require ROOT.'Lib/Class/Image.class.php';
		$type_res = Image::getImageType($_FILES['Filedata']['tmp_name']);
		if ($type_res === false)
		{
			exit('no|图片类型错误, 请您稍候再试.|3');			
		}
		
		//生成三张缩略图:70*43 630*390
		$file_path_pfx = $this->getImagePath($uid, $room_id);
		$file_path = ROOT . 'upload/cover/' . $file_path_pfx;
		//40*40
		Image::imageResize($_FILES['Filedata']['tmp_name'], $file_path . '_60.jpg', 60, 60);
		Image::imageResize($_FILES['Filedata']['tmp_name'], $file_path . '_126.jpg', 126, 95);
		Image::imageResize($_FILES['Filedata']['tmp_name'], $file_path . '_630.jpg', 630, 410);
		//保留原图
		copy($_FILES['Filedata']['tmp_name'], $file_path. '.jpg');
		
		
		foreach (array('630') as $key=>$value) {
			$filename = $file_path . '_'.$value.'.jpg';
			$image = new Image($filename);
			$image->waterMark(ROOT . 'Public/images/water.png', 9);
			$f = $image->save(2, '', '');
		}
		
		$_SESSION['room_photos'][] = $file_path_pfx;
		
		exit('ok|' . $file_path_pfx . '|' . substr($_FILES['Filedata']['name'], 0, -4) . '|' . $file_path_pfx);
    	
    }
    
	/**
	 * 根据用户编号与房间编号信息生成图片存储路径
	 *
	 * @param Integer	$uid
	 * @param Integer $room_id
	 * @return String
	 */
	private function getImagePath($uid, $room_id)
	{
		$solt = 'jIO23-=&k34sdf;23';
		
		$md5_str = md5($uid . $solt);
		
		$sub_path = substr($md5_str, 3, 2) . '/' . substr($md5_str, 10, 2) . '/';
		//检查目录是否存在
		if (!file_exists(ROOT . 'upload/cover/' . $sub_path))
		{
			$res = mkdir(ROOT . 'upload/cover/' . $sub_path, 0700, true);
			if ($res == false)
				exit('no|上传失败，请稍候再试.|4' . $this->app->cfg['path']['cover'] . $sub_path);
		}
			
		$file_name = md5($room_id . rand(86400, time()) . $uid);
		
		return $sub_path . $file_name;
	}    
}

?>