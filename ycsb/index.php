<?php
session_start();
ini_set('display_errors', 'on');
error_reporting(E_ALL);
define('THINK_PATH', './Framework/');
define('ROOT', str_replace('\\', '/', dirname(__FILE__)) . '/');

//定义项目名称和路径
define('APP_NAME', 'bian');
define('APP_PATH', '.');
header("Content-Type:text/html; charset=utf-8");

// 加载框架入口文件
require(THINK_PATH."/ThinkPHP.php");

App::run();
?>
