<?php
/**
 * 文件上传类
 * @author rewind @date 2009-04-24
 */

class UploadClass
{
	var $files = array();
	var $savePath;
	var $savename;
	var $allowSize;//如果为0，不做限制
	var $allowExt;
	var $imageExt = array('gif', 'jpg', 'jpeg', 'png', 'bmp');
	var $uploads = 0;
	var $errors;
	var $fileext;
	var $uploadedfiles;
	
	function UploadClass($uploadname, $savepath, $allowExt = 'jpg|jpeg|gif|bmp|png|doc|docx|xls|ppt|pdf|txt|rar|zip', $allowSize = 0)
	{
		if (!$_FILES[$uploadname]['name']) return false;
		$savepath = str_replace('\\', '/', $savepath);
		$this->savePath = $this->set_savepath($savepath);
		$this->allowExt = $allowExt;
		$this->allowSize = $allowSize;
		if (is_array($_FILES[$uploadname]['name']))
		{
			foreach ($_FILES[$uploadname]['name'] as $key=>$value)
			{
				if (!$value)
				{
					unset($_FILES[$uploadname]['name'][$key]);
				}
			}
		}
		
		$this->uploads = count($_FILES[$uploadname]['name']);
		if ($this->uploads == 0)
		{
			$this->uploads = 1;
			$filesarray[] = array('tmp_name' => $_FILES[$uploadname]['tmp_name'], 'name' => $_FILES[$uploadname]['name'], 'type' => $_FILES[$uploadname]['type'], 'size' => $_FILES[$uploadname]['size'], 'error' => $_FILES[$uploadname]['error']);
		}
		else 
		{
			foreach ($_FILES[$uploadname]['name'] as $key=>$value)
			{
				$filesarray[$key] = array('tmp_name'=>$_FILES[$uploadname]['tmp_name'][$key], 'name'=>$_FILES[$uploadname]['name'][$key], 'type'=>$_FILES[$uploadname]['type'][$key], 'size'=>$_FILES[$uploadname]['size'][$key], 'error'=>$_FILES[$uploadname]['error'][$key]);
			}
			
		}
		$this->files = $filesarray;
		return $this->files; 
	}
	
	function up()
	{
		if (empty($this->files)) return false;
		foreach ($this->files as $key=>$value)
		{
			$fileext = $this->fileext($value['name']);
			$this->fileext = $fileext;
			if(!preg_match("/^(".$this->allowExt.")$/", $fileext)) //后缀
			{
				$this->errors = 9;
				return 1;
			}
			if ($this->allowSize && $value['size'] > $this->allowSize) //大小
			{
				$this->errors = 1;
				return 2;
			}
			/*if (!$this->isuploadedfile($value['tmp_name'])) //是否为http方式
			{
				$this->errors = 11;
				return 3;
			}*/
			if (!$this->dirCreate($this->savePath)) //目录权限
			{
				$this->errors = 7;
				return 4;
			}
			$this->savename = $this->setSaveName();
			$savefile = $this->savePath.$this->savename;
			$datePath = substr($this->savePath, strrpos($this->savePath,'upload'));
			$datePath = substr($datePath, -1) == '/' ? $datePath : $datePath.'/';
			$saveto = $datePath.$this->savename;
			if (move_uploaded_file($value['tmp_name'], $savefile) || @copy($value['tmp_name'], $savefile))
			{
				@chmod($savefile, 0644);
				@unlink($value['tmp_name']);
				$this->uploadedfiles[] = array('saveto'=>$saveto, 'filename'=>$value['name'], 'filetype'=>$value['type'], 'size'=>$value['size'], 'imagenum'=>$this->uploads);
			}
		}
		return $this->uploadedfiles;
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
		return $code.'.'.$this->fileext;	
	}
	
	function set_savepath($savepath)
	{
		$savepath = str_replace('\\', '/', $savepath);
		$savepath = substr($savepath, '-1') == '/' ? $savepath : $savepath.'/';
		$this->savePath = $savepath;
		return $this->savePath;
	}
	
	function fileext($file)
	{
		return substr($file, strrpos($file, '.')+1);
	}
	
	function isuploadedfile($file)
	{
		return is_uploaded_file($file) || is_uploaded_file(str_replace('\\\\', '\\', $file));
	}
	
	function error()
	{
		$upload_error = array(
			0=>'文件上传成功！',
			1=>'文件超过指定的大小',
			3=>'文件只有部分上传',
			4=>'文件没有上传',
			5=>'找不到临时文件',
			6=>'文件写入临时文件失败',
			7=>'附件目录创建失败',
			8=>'附件目录没有写入的权限',
			9=>'不允许上传该类型的文件',
			10=>'目录非法',
			11 => '非法上传文件',
		);
		return $upload_error[$this->errors];
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
}

?>