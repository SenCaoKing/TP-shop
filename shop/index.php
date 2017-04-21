<?php

//调试模式、生产模式
define('APP_DEBUG',true); //调试模式（调试模式，每个文件每次请求都一次加载，不使用旧的缓存内容）
//define('APP_DEBUG',false); //生产模式
//引入tp框架的接口文件
include("../ThinkPHP/ThinkPHP.php");

