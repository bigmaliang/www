<?php
session_start();
header("Content-Type:text/html; charset=utf-8");
if (isset($_POST['__ssid__'])) 
			session_id($_POST['__ssid__']);
			
require 'Lib/Class/Image.class.php';

$uid = $_SESSION['admin_id'];
$room_id = '17192';
$type_res = Image::getImageType($_FILES['Filedata']['tmp_name']);
if ($type_res === false)
{
	//exit('no|图片类型错误, 请您稍候再试.|3');			
}

//生成三张缩略图:70*43 630*390
$file_path_pfx = getImagePath($uid, $room_id);
$file_path = ROOT . 'upload/cover/' . $file_path_pfx;
//40*40
Image::imageResize($_FILES['Filedata']['tmp_name'], $file_path . '_60.jpg', 60, 60);
Image::imageResize($_FILES['Filedata']['tmp_name'], $file_path . '_630.jpg', 630, 410);
//保留原图
copy($_FILES['Filedata']['tmp_name'], $file_path. '.jpg');

$_SESSION['room_photos'][] = $file_path_pfx;
exit(json_encode($_SESSION['room_photos']));
exit('ok|' . $file_path_pfx . '|' . substr($_FILES['Filedata']['name'], 0, -4) . '|' . $file_path_pfx);


/**
 * 根据用户编号与房间编号信息生成图片存储路径
 *
 * @param Integer	$uid
 * @param Integer $room_id
 * @return String
 */
function getImagePath($uid, $room_id)
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

function dump($str)
{
	echo "<pre>";
	print_r($str);
	echo "</pre>";
}
?>