<?php

/**
 *  FileSystem 类
 *
 * @package lib
 * @subpackage util
 * @author 张立冰 <roast@php.net>
 */
class FileSystem {
	/**
	 * 移动文件
	 * @param array $fileList
	 * @param string $from
	 * @param string $to
	 * @param string $option
	 * @return void
	 * @static
	 */
	function mv($fileList, $from, $to, $option='f') {
		if ( is_dir($from) && is_dir($to) ) {
			if (!is_array($fileList)) {
				$fileList = explode('|', $fileList);
			}
			foreach ($fileList as $file) {
				$file_src = $from . '/' . $file;
				if (is_file($file_src))	{
					$file_dest = $to . '/' . $file;
					if (!is_file($file_dest) || strpos($option, 'f')!==false) {
						copy($file_src, $file_dest);
						if (strpos($option, 'k')===false) {
							unlink($file_src);
						}
					}
				}
			}
		}
	}
	/**
	 * 删除文件或文件夹（递归）
	 * @param array $fileList
	 * @param string $option
	 * @return void
	 * @static 
	 */
	function rm($fileList, $option='r') {
		if (!is_array($fileList)) {
			$fileList = explode('|', $fileList);
		}
		foreach ($fileList as $filename) {
			if (is_file($filename))	{
				unlink($filename);
			} elseif (is_dir($filename)) {
				if (strpos($option, 'r')!==false) {
					$file_list_ = FileSystem::ls($filename);
					foreach ($file_list_ as $fi => $file) {
						$file_list_[$fi] = $filename . '/' . $file;
					}
					FileSystem::rm($file_list_, $option);
				}
				rmdir($filename);
			}
		}
	}
	/**
	 * 取文件扩展名  
	 * @param string $fileName
	 * @param bool $withDot
	 * @return string
	 * @static 
	 */
	function fileExt($fileName, $withDot=false) {
		$fileName = basename($fileName);
		$pos = strrpos($fileName, '.');
		if ($pos===false) {
			$result = '';
		} else {
			$result = ($withDot) ? substr($fileName, $pos) : substr($fileName, $pos+1);
		}
		return $result;
	}
	
	/**
	 * 取文件名 (除去扩展名 )
	 * @param string $fileName
	 * @param bool $withDot
	 * @return string
	 * @static 
	 */
	function getBasicName($fileName, $withDot=false) {
		$pos = strrpos($fileName, '.');
		if ($pos===false) {
			$result = $fileName;
		} else {
			$result = ($withDot) ? substr($fileName,0, $pos+1) : substr($fileName,0, $pos);
		}
		return $result;
	}
	/**
	 * 返回指定路径中符合条件的文件和文件夹列表  
	 * @param string $path
	 * @param array $condition
	 * @param string $sort
	 * @return array
	 * @static 
	 */
	function ls($path, $condition=array(), $sort='ASC', $withPath=false) {
		$result = array();
		if (is_dir($path) && $handle=opendir($path)) {
			while (false !== ($file = readdir($handle))) {
				if ($file!='.' && $file!='..') {
					$valid = true;
					if ($condition)	{
						foreach ($condition as $name => $value)	{
							switch ($name) {
								case 'fileext':
									if (!isset($fileext_list)) {
										$fileext_list = explode('|', $value);
									}
									if ($fileext_list) {
										$valid = in_array(FileSystem::fileExt($file), $fileext_list);
									}
									break;
								case 'fileonly':
									$valid = ( !$value || is_file($path . '/' . $file) );
									break;
								case 'folderonly':
									$valid = ( !$value || is_dir($path . '/' . $file) );
									break;
								case 'filename':
									if ($value) {
										$valid = ( $value == basename($file, FileSystem::fileExt($file, true)) );
									}
									break;
							}
							if (!valid) {
								break;
							}
						}
					}
					if ($valid) {
						$result[] = ($withPath) ? ($path . '/' . $file) : $file;
					}
				}
			}
			closedir($handle); 
		}
		switch ($sort) {
			case 'ASC':
				sort($result);
				break;
			case 'DESC':
				rsort($result);
				break;
		}
		return $result;
	}
	
	/**
	 * 返回指定路径中符合条件的文件列表  
	 * @param string $path
	 * @param string $fileext
	 * @param string $sort
	 * @return array
	 * @static 
	 */
	function fileList($path, $fileext='', $sort='ASC', $withPath=false) {
		$condition = array(
				'fileonly'		=> true,
				'fileext'		=> $fileext,
				);
		$result = FileSystem::ls($path, $condition, $sort, $withPath);
		return $result;
	}
	
	/**
	 * 返回指定路径中符合条件的文件夹列表  
	 * @param string $path
	 * @param string $sort
	 * @return array
	 * @static 
	 */
	function folderList($path, $sort='ASC', $withPath=false) {
		$condition = array(
				'folderonly'		=> true,
				);
		$result = FileSystem::ls($path, $condition, $sort, $withPath);
		return $result;
	}

	/**
	 * 自动创建目录
	 * @param string $destFolder 服务器路径
	 * @static 
	 */
	function makeDir($destFolder) {
		if (!is_dir($destFolder) && $destFolder!='./' && $destFolder!='../') {
			$dirname = '';
			$folders = explode('/',$destFolder);
			foreach ($folders as $folder) {
				$dirname .= $folder . '/';
				if ($folder!='' && $folder!='.' && $folder!='..' && !is_dir($dirname)) {
					mkdir($dirname);
				}
			}
			chmod($destFolder,0777);
		}	
	}
}
?>