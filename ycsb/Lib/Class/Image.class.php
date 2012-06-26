<?php

/**
 *  Image 类
 *
 * @package lib
 * @subpackage util
 * @author 张立冰 <roast@php.net>
 */

class Image
{
	/**
	 * @var string $fileName 文件名
	 * @access private
	 */
	private $fileName = '';
	
	/**
	 * @var gd resource $imageResource 原图像
	 * @access private
	 */
	private $imageResource = NULL;
	
	/**
	 * @var int $imageWidth 原图像宽
	 * @access private
	 */
	private $imageWidth = NULL;
	
	/**
	 * @var int $imageHeight 原图像高
	 * @access private
	 */
	private $imageHeight = NULL;
	
	/**
	 * @var int $imageType 原图像类型
	 * @access private
	 */
	private $imageType = NULL;
	
	/**
	 * @var int $newResource 新图像
	 * @access private
	 */
	private $newResource = NULL;
	
	/**
	 * @var int $newResType 新图像类型
	 * @access private
	 */
	private $newResType = NULL;

	
	/**
	 * 构造函数
	 * @param string $fileName 文件名
	 */
	public function __construct($fileName = NULL)
	{
		$this->fileName = $fileName;
		if ($this->fileName)
		{
			$this->getSrcImageInfo();
		}
	}

	
	/**
	 * 取源图像信息
	 * @access private
	 * @return void
	 */
	private function getSrcImageInfo()
	{
		$info = $this->getImageInfo();
		$this->imageWidth = $info[0];
		$this->imageHeight = $info[1];
		$this->imageType = $info[2];
	}

	
	/**
	 * 取图像信息
	 * @param string $fileName 文件名
	 * @access private
	 * @return array
	 */
	private function getImageInfo($fileName = NULL)
	{
		if ($fileName == NULL)
		{
			$fileName = $this->fileName;
		}
		$info = getimagesize($fileName);
		return $info;
	}

	
	/**
	 * 创建源图像GD 资源
	 * @access private
	 * @return void
	 */
	private function createSrcImage()
	{
		$this->imageResource = $this->createImageFromFile();
	}

	
	/**
	 * 跟据文件创建图像GD 资源
	 * @param string $fileName 文件名
	 * @return gd resource
	 */
	public function createImageFromFile($fileName = NULL)
	{
		if (! $fileName)
		{
			$fileName = $this->fileName;
			$imgType = $this->imageType;
		}
		if (! is_readable($fileName) || ! file_exists($fileName))
		{
			throw new Exception('Unable to open file "' . $fileName . '"');
		}
		
		if (! $imgType)
		{
			$imageInfo = $this->getImageInfo($fileName);
			$imgType = $imageInfo[2];
		}
		
		switch ($imgType)
		{
			case IMAGETYPE_GIF:
				$tempResource = imagecreatefromgif($fileName);
				break;
			case IMAGETYPE_JPEG:
				$tempResource = imagecreatefromjpeg($fileName);
				break;
			case IMAGETYPE_PNG:
				$tempResource = imagecreatefrompng($fileName);
				break;
			case IMAGETYPE_WBMP:
				$tempResource = imagecreatefromwbmp($fileName);
				break;
			case IMAGETYPE_XBM:
				$tempResource = imagecreatefromxbm($fileName);
				break;
			default:
				throw new Exception('Unsupport image type');
		}
		return $tempResource;
	}

	
	/**
	 * 改变图像大小
	 * @param int $width 宽
	 * @param int $height 高
	 * @param string $flag 按什么方式改变 0=长宽转换成参数指定的 1=按比例缩放，长宽约束在参数指定内，2=以宽为约束缩放，3=以高为约束缩放
	 * @return string
	 */
	public function resizeImage($width, $height, $flag = 1)
	{
		$widthRatio = $width / $this->imageWidth;
		$heightRatio = $height / $this->imageHeight;
		switch ($flag)
		{
			case 1:
				if ($this->imageHeight < $height && $this->imageWidth < $width)
				{
					$endWidth = $this->imageWidth;
					$endHeight = $this->imageHeight;
				
		//return;
				}
				elseif (($this->imageHeight * $widthRatio) > $height)
				{
					$endWidth = ceil($this->imageWidth * $heightRatio);
					$endHeight = $height;
				}
				else
				{
					$endWidth = $width;
					$endHeight = ceil($this->imageHeight * $widthRatio);
				}
				break;
			case 2:
				$endWidth = $width;
				$endHeight = ceil($this->imageHeight * $widthRatio);
				break;
			case 3:
				$endWidth = ceil($this->imageWidth * $heightRatio);
				$endHeight = $height;
				break;
			case 4:
				$endWidth2 = $width;
				$endHeight2 = $height;
				if ($this->imageHeight < $height && $this->imageWidth < $width)
				{
					$endWidth = $this->imageWidth;
					$endHeight = $this->imageHeight;
				
		//return;
				}
				elseif (($this->imageHeight * $widthRatio) < $height)
				{
					$endWidth = ceil($this->imageWidth * $heightRatio);
					$endHeight = $height;
				}
				else
				{
					$endWidth = $width;
					$endHeight = ceil($this->imageHeight * $widthRatio);
				}
				break;
			default:
				$endWidth = $width;
				$endHeight = $height;
				break;
		}
		if ($this->imageResource == NULL)
		{
			$this->createSrcImage();
		}
		if ($flag == 4)
		{
			$this->newResource = imagecreatetruecolor($endWidth2, $endHeight2);
		}
		else
		{
			$this->newResource = imagecreatetruecolor($endWidth, $endHeight);
		}
		$this->newResType = $this->imageType;
		imagecopyresampled($this->newResource, $this->imageResource, 0, 0, 0, 0, $endWidth, $endHeight, $this->imageWidth, $this->imageHeight);
	
	}

	
	/**
	 * 给图像加水印
	 * @param string $waterContent 水印内容可以是图像文件名，也可以是文字
	 * @param int $pos 位置0-9可以是数组
	 * @param int $textFont 字体大字，当水印内容是文字时有效
	 * @param string $textColor 文字颜色，当水印内容是文字时有效
	 * @return string
	 */
	public function waterMark($waterContent, $pos = 0, $textFont = 5, $textColor = "#ffffff")
	{
		$isWaterImage = file_exists($waterContent);
		if ($isWaterImage)
		{
			$waterImgRes = $this->createImageFromFile($waterContent);
			$waterImgInfo = $this->getImageInfo($waterContent);
			$waterWidth = $waterImgInfo[0];
			$waterHeight = $waterImgInfo[1];
		}
		else
		{
			$waterText = $waterContent;
			$waterWidth = 100;
			$waterHeight = 12;
		}
		if ($this->imageResource == NULL)
		{
			$this->createSrcImage();
		}
		switch ($pos)
		{
			case 1: //1为顶端居左 
				$posX = 0;
				$posY = 0;
				break;
			case 2: //2为顶端居中 
				$posX = ($this->imageWidth - $waterWidth) / 2;
				$posY = 0;
				break;
			case 3: //3为顶端居右 
				$posX = $this->imageWidth - $waterWidth;
				$posY = 0;
				break;
			case 4: //4为中部居左 
				$posX = 0;
				$posY = ($this->imageHeight - $waterHeight) / 2;
				break;
			case 5: //5为中部居中 
				$posX = ($this->imageWidth - $waterWidth) / 2;
				$posY = ($this->imageHeight - $waterHeight) / 2;
				break;
			case 6: //6为中部居右 
				$posX = $this->imageWidth - $waterWidth;
				$posY = ($this->imageHeight - $waterHeight) / 2;
				break;
			case 7: //7为底端居左 
				$posX = 0;
				$posY = $this->imageHeight - $waterHeight;
				break;
			case 8: //8为底端居中 
				$posX = ($this->imageWidth - $waterWidth) / 2;
				$posY = $this->imageHeight - $waterHeight;
				break;
			case 9: //9为底端居右 
				$posX = $this->imageWidth - $waterWidth - 20;
				$posY = $this->imageHeight - $waterHeight - 10;
				break;
			default: //随机 
				$posX = rand(0, ($this->imageWidth - $waterWidth));
				$posY = rand(0, ($this->imageHeight - $waterHeight));
				break;
		}
		imagealphablending($this->imageResource, true);
		if ($isWaterImage)
		{
			imagecopy($this->imageResource, $waterImgRes, $posX, $posY, 0, 0, $waterWidth, $waterHeight);
		}
		else
		{
			$R = hexdec(substr($textColor, 1, 2));
			$G = hexdec(substr($textColor, 3, 2));
			$B = hexdec(substr($textColor, 5));
			
			$textColor = imagecolorallocate($this->imageResource, $R, $G, $B);
			//imagestring ($this->imageResource, $textFont, $posX, $posY, $waterText, $textColor);     
			imagefttext($this->imageResource, $textFont, 0, $posX, $posY, $textColor, 'simhei.ttf', $waterText);
		}
		$this->newResource = $this->imageResource;
		$this->newResType = $this->imageType;
	}

	
	/**
	 * 生成验证码图片
	 * @param int $width 宽
	 * @param string $height 高
	 * @param int $length 长度
	 * @param int $validType 0=数字,1=字母,2=数字加字母
	 * @param string $textColor 文字颜色
	 * @param string $backgroundColor 背景颜色
	 * @return void
	 */
	public function imageValidate($width, $height, $length = 4, $validType = 1, $textColor = '#000000', $backgroundColor = '#ffffff')
	{
		if ($validType == 1)
		{
			$validString = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$validLength = 52;
		}
		elseif ($validType == 2)
		{
			$validString = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$validLength = 62;
		}
		elseif ($validType == 3)
		{
			$validString = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$validLength = 26;
		}
		else
		{
			$validString = '0123456789';
			$validLength = 10;
		}
		
		srand((int) time());
		$valid = '';
		for ($i = 0; $i < $length; $i ++)
		{
			$valid .= $validString{rand(0, $validLength - 1)};
		}
		$this->newResource = imagecreate($width, $height);
		$bgR = hexdec(substr($backgroundColor, 1, 2));
		$bgG = hexdec(substr($backgroundColor, 3, 2));
		$bgB = hexdec(substr($backgroundColor, 5, 2));
		$backgroundColor = imagecolorallocate($this->newResource, $bgR, $bgG, $bgB);
		$tR = hexdec(substr($textColor, 1, 2));
		$tG = hexdec(substr($textColor, 3, 2));
		$tB = hexdec(substr($textColor, 5, 2));
		$textColor = imagecolorallocate($this->newResource, $tR, $tG, $tB);
		for ($i = 0; $i < strlen($valid); $i ++)
		{
			imagestring($this->newResource, 5, $i * $width / $length + 3, 2, $valid[$i], $textColor);
		}
		$this->newResType = IMAGETYPE_JPEG;
		return $valid;
	
	}
	

	/**
	 * 图片圆角处理
	 *
	 * @param Integer $size 圆角大小 单位：px
	 * @return gd resource 新图像资源
	 */
	public function roundedCorner($size = 6)
	{
		if ($this->imageResource == NULL)
		{
			$this->createSrcImage();
		}
		
		$this->newResource = $this->imageResource;
		$this->newResType = $this->imageType;
		
		$im_corner = imagecreatetruecolor($size, $size);
		
		$white = imagecolorallocate($im_corner, 0xFF, 0xFF, 0xFF); //白色背景
		$black = ImageColorAllocate($im_corner, 0, 0, 0);
		
		imagefill($im_corner, 0, 0, $white);
		
		//画一段圆弧黑色填充
		imagefilledarc($im_corner, $size, $size, $size * 2, $size * 2, - 180, - 90, $black, IMG_ARC_PIE);
		
		$image_width = imagesx($this->newResource);
		$image_height = imagesy($this->newResource);
		
		//上->左 圆角
		imagecolortransparent($im_corner, $black);
		$dest_x = 0;
		$dest_y = 0;
		imagecopymerge($this->newResource, $im_corner, $dest_x, $dest_y, 0, 0, $size, $size, 100);
		
		//下->左 圆角
		$degrees = 90;
		$rotated = imagerotate($im_corner, $degrees, 0);
		imagecolortransparent($rotated, $black);
		$dest_x = 0;
		$dest_y = $image_height - $size;
		imagecopymerge($this->newResource, $rotated, $dest_x, $dest_y, 0, 0, $size, $size, 100);
		
		//下->右 圆角
		$degrees = 180;
		$rotated = imagerotate($im_corner, $degrees, 0);
		imagecolortransparent($rotated, $black);
		$dest_x = $image_width - $size;
		$dest_y = $image_height - $size;
		imagecopymerge($this->newResource, $rotated, $dest_x, $dest_y, 0, 0, $size, $size, 100);
		
		//上->右 圆角
		$degrees = 270;
		$rotated = imagerotate($im_corner, $degrees, 0);
		imagecolortransparent($rotated, $black);
		$dest_x = $image_width - $size;
		$dest_y = 0;
		imagecopymerge($this->newResource, $rotated, $dest_x, $dest_y, 0, 0, $size, $size, 100);
		
		return $this->newResource;
	}

	
	/**
	 * 显示输出图像
	 * @return void
	 */
	public function display($fileName = '', $quality = 100)
	{
		
		$imgType = $this->newResType;
		$imageSrc = $this->newResource;
		switch ($imgType)
		{
			case IMAGETYPE_GIF:
				if ($fileName == '')
				{
					header('Content-type: image/gif');
				}
				imagegif($imageSrc, $fileName, $quality);
				break;
			case IMAGETYPE_JPEG:
				if ($fileName == '')
				{
					header('Content-type: image/jpeg');
				}
				imagejpeg($imageSrc, $fileName, $quality);
				break;
			case IMAGETYPE_PNG:
				if ($fileName == '')
				{
					header('Content-type: image/png');
					imagepng($imageSrc);
				}
				else
				{
					imagepng($imageSrc, $fileName);
				}
				break;
			case IMAGETYPE_WBMP:
				if ($fileName == '')
				{
					header('Content-type: image/wbmp');
				}
				imagewbmp($imageSrc, $fileName, $quality);
				break;
			case IMAGETYPE_XBM:
				if ($fileName == '')
				{
					header('Content-type: image/xbm');
				}
				imagexbm($imageSrc, $fileName, $quality);
				break;
			default:
				throw new Exception('Unsupport image type');
		}
		imagedestroy($imageSrc);
	}

	
	/**
	 * 保存图像
	 * @param int $fileNameType 文件名类型 0使用原文件名，1使用指定的文件名，2在原文件名加上后缀，3产生随机文件名
	 * @param string $folder 文件夹路径 为空为与原文件相同
	 * @param string $param 参数$fileNameType为1时为文件名2时为后缀
	 * @return void
	 */
	public function save($fileNameType = 0, $folder = NULL, $param = '_miniature')
	{
		//import('util.FileSystem');
		require_once ROOT . 'Lib/Class/FileSystem.class.php';
		
		if ($folder === NULL)
		{
			$folder = dirname($this->fileName) . DIRECTORY_SEPARATOR;
		}
		$fileExtName = FileSystem::fileExt($this->fileName, true);
		$fileBesicName = FileSystem::getBasicName($this->fileName, false);
		
		switch ($fileNameType)
		{
			case 1:
				$newFileName = $param;
				break;
			case 2:
				$newFileName = $fileBesicName . $param . $fileExtName;
				break;
			case 3:
				$tmp = date('YmdHis');
				$fileBesicName = $tmp;
				$i = 0;
				while (file_exists($folder . $fileBesicName . $fileExtName))
				{
					$fileBesicName = $tmp . $i;
					$i ++;
				}
				$newFileName = $fileBesicName . $fileExtName;
				break;
			default:
				$newFileName = $this->fileName;
				break;
		}
		
		$this->display($folder . $newFileName);
		return $newFileName;
	}

	
	/**
	 * 判断图片类型,并返回
	 * 用于相册,支持jpg,png,bmp,gif
	 *
	 * @param string $path 图片路径
	 * @return boolean|string 失败返回false,成功返回图片扩展名
	 */
	public static function getImageType($path)
	{
		$type = exif_imagetype($path);
		switch ($type)
		{
			case IMAGETYPE_JPEG:
				return 'jpg';
				break;
			case IMAGETYPE_GIF:
				return 'gif';
				break;
			case IMAGETYPE_PNG:
				return 'png';
				break;
			case IMAGETYPE_BMP:
				return 'bmp';
				break;			
			default:
				return false;
		}
	}
	
	/**
	 * 创建缩略图,相册专用
	 *
	 * @param string $filepath			图片路径
	 * @param string $sava_path			缩略图生成后,存放的完整路径
	 * @param integer $new_width		图片新宽度,默认150
	 * @param integer $new_height		图片新高度,默认100
	 * @param string $flag 按什么方式改变 0=长宽转换成参数指定的 1=按比例缩放，长宽约束在参数指定内，2=以宽为约束缩放，3=以高为约束缩放
	 * @param integer $quality			JPEG图片生成质量,默认80
	 * @return boolean 					成功返回true,失败返回false
	 */
	public static function imageResize($filepath, $sava_path, $new_width = 150, $new_height = 100, $flag = 1, $quality = 80)
	{
		$image_type = self::getImageType($filepath);
		switch ($image_type)
		{
			case 'jpg':
				$image = imagecreatefromjpeg($filepath);
				break;
			case 'gif':
				$image = imagecreatefromgif($filepath);
				break;
			case 'png':
				$image = imagecreatefrompng($filepath);
				break;			
			case 'bmp':
				$image = self::imagecreatefrombmp($filepath);
				break;
			default:
				return false;				
		}
		
		if ($image == false)
			return false;
		
		list($width, $height) = getimagesize($filepath);
			
		$width_ratio = $new_width/$width;
		$height_ratio = $new_height/$height;
		switch ($flag) 
		{
			case 1:
				if ($height < $new_height && $width < $new_width) 
				{
					$new_width = $width;
					$new_height = $height;
				} 
				elseif (($height * $width_ratio) > $new_height ) 
					$new_width = ceil($width * $height_ratio);
				else 
					$new_height = ceil($height * $width_ratio);
				break;
			case 2:
				$new_height = ceil($height * $width_ratio);
				break;
			case 3:
				$new_width = ceil($width * $height_ratio);
				break;
			case 4:
				if ($height < $new_height && $width < $new_width) 
				{
					$new_width = $width;
					$new_height = $height;
				}
				elseif (($height * $width_ratio)<$new_height) 
					$new_width = ceil($width * $height_ratio);
				else 
					$new_height = ceil($height * $width_ratio);
				break;
			default:
				$new_width = $width;
				$new_height = $height;
				break;
		}
		
		$image_color = imagecreatetruecolor($new_width, $new_height);
		$trans_colour = imagecolorallocate($image_color, 255, 255, 255);
		imagefill($image_color, 0, 0, $trans_colour);
		if (!imagecopyresampled($image_color, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height))
			return false;
							
		switch ($image_type)
		{
			case 'jpg':
			case 'bmp':
				if (imagejpeg($image_color, $sava_path, $quality))
				{
					imagedestroy($image_color);
					return true;
				}
				break;
			case 'gif':
				imagecolortransparent($image_color, imagecolorallocate($image_color, 0, 0, 0)); 
				if (imagegif($image_color, $sava_path))
				{
					imagedestroy($image_color);
					return true;
				}
				break;
			case 'png':
				imagecolortransparent($image_color, imagecolorallocate($image_color, 0, 0, 0)); 
				if (imagepng($image_color, $sava_path))
				{
					imagedestroy($image_color);
					return true;
				}
				break;
			
			default:
				return false;				
		}
		
		imagedestroy($image_color);
		return false;
	}
	
	
	/**
	 * 转换BMP为GD格式
	 *
	 * @param string $src	输入文件
	 * @param string $dest	输出文件	
	 * @return boolean 		成功返回true,失败返回false
	 */
	private function ConvertBMP2GD($src, $dest) 
	{
		if (!($src_f = fopen($src, "rb"))) 
			return false;
			
		if (!($dest_f = fopen($dest, "wb"))) 
			return false;

		$header = unpack("vtype/Vsize/v2reserved/Voffset", fread($src_f,14));
		$info = unpack("Vsize/Vwidth/Vheight/vplanes/vbits/Vcompression/Vimagesize/Vxres/Vyres/Vncolor/Vimportant",
		fread($src_f, 40));
		
		extract($info);
		extract($header);
		
		if ($type != 0x4D42) 
			return false;
		
		$palette_size = $offset - 54;
		$ncolor = $palette_size / 4;
		$gd_header = "";
		
		$gd_header .= ($palette_size == 0) ? "\xFF\xFE" : "\xFF\xFF";
		$gd_header .= pack("n2", $width, $height);
		$gd_header .= ($palette_size == 0) ? "\x01" : "\x00";
		if ($palette_size) 
			$gd_header .= pack("n", $ncolor);
			
		$gd_header .= "\xFF\xFF\xFF\xFF";		
		fwrite($dest_f, $gd_header);
		
		if ($palette_size) 
		{
			$palette = fread($src_f, $palette_size);
			$gd_palette = "";
			$j = 0;
			while($j < $palette_size)
			{
				$b = $palette{$j++};
				$g = $palette{$j++};
				$r = $palette{$j++};
				$a = $palette{$j++};
				$gd_palette .= "$r$g$b$a";
			}
			
			$gd_palette .= str_repeat("\x00\x00\x00\x00", 256 - $ncolor);
			fwrite($dest_f, $gd_palette);
		}
		
		$scan_line_size = (($bits * $width) + 7) >> 3;
		$scan_line_align = ($scan_line_size & 0x03) ? 4 - ($scan_line_size & 0x03) : 0;
		
		for($i = 0, $l = $height - 1; $i < $height; $i++, $l--) 
		{
			fseek($src_f, $offset + (($scan_line_size + $scan_line_align) *	$l));
			$scan_line = fread($src_f, $scan_line_size);
			if ($bits == 24) 
			{
				$gd_scan_line = "";
				$j = 0;
				while($j < $scan_line_size) 
				{
					$b = $scan_line{$j++};
					$g = $scan_line{$j++};
					$r = $scan_line{$j++};
					$gd_scan_line .= "\x00$r$g$b";
				}
			}
			else if ($bits == 8)
				$gd_scan_line = $scan_line;
			else if ($bits == 4)
			{
				$gd_scan_line = "";
				$j = 0;
				while($j < $scan_line_size) 
				{
					$byte = ord($scan_line{$j++});
					$p1 = chr($byte >> 4);
					$p2 = chr($byte & 0x0F);
					$gd_scan_line .= "$p1$p2";
				} 
				$gd_scan_line = substr($gd_scan_line, 0, $width);
			}
			else if ($bits == 1) 
			{
				$gd_scan_line = "";
				$j = 0;
				while($j < $scan_line_size) 
				{
					$byte = ord($scan_line{$j++});
					$p1 = chr((int) (($byte & 0x80) != 0));
					$p2 = chr((int) (($byte & 0x40) != 0));
					$p3 = chr((int) (($byte & 0x20) != 0));
					$p4 = chr((int) (($byte & 0x10) != 0));
					$p5 = chr((int) (($byte & 0x08) != 0));
					$p6 = chr((int) (($byte & 0x04) != 0));
					$p7 = chr((int) (($byte & 0x02) != 0));
					$p8 = chr((int) (($byte & 0x01) != 0));
					$gd_scan_line .= "$p1$p2$p3$p4$p5$p6$p7$p8";
				} 
				
				$gd_scan_line = substr($gd_scan_line, 0, $width);
			}	
				
			fwrite($dest_f, $gd_scan_line);
		}
		
		fclose($src_f);
		fclose($dest_f);
		
		return true;
	}
	
	
	/**
	 * 生成BMP图片资源
	 *
	 * @param string $filename	图片文件名
	 * @return res|boolean		成功返回图片资源,失败返回false
	 */
	public static function imagecreatefrombmp($filename) 
	{
		$tmp_name = tempnam(ini_get('upload_tmp_dir'), "GD");
		if (self::ConvertBMP2GD($filename, $tmp_name))
		{
			$img = imagecreatefromgd($tmp_name);
			unlink($tmp_name);
			return $img;
		}
		
		return false;
	}		
}
?>