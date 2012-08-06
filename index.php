<?php


/**
 * 设置源静态文件的根目录的URL地址,请修改为需要加速的网站地址
 * */
define('STATIC_URL','http://www.baidu.com/');

/**
 * SAE storage的domain ,使用KVDB 做缓存层.该变量失效
 * */
define('DOMAIN','cdn');

/**
 * 空请求时是否显示文档
 * */
define('WELCOME_DOC',FALSE);

/**
 * 缓存有效期,秒数  一个月,由于失效机制不够实用.已经废弃
 * */
define('EXPIREDTIME',3600*24*30);
/**
 * 运行环境:development/testing/production
 * */
define('ENVIRONMENT','development');

//========================================================


if (defined('ENVIRONMENT'))
{
	switch (ENVIRONMENT)
	{
		case 'development':
			error_reporting(E_ALL);
		break;
	
		case 'testing':
		case 'production':
			error_reporting(0);
		break;

		default:
			exit('The application environment is not set correctly.');
	}
}

//本地根目录
define('BASE_PATH',dirname(__FILE__).'/');

define('BASE_URL', rtrim(STATIC_URL,'/').'/');

define('IS_SAE', defined('SAE_SECRETKEY'));

require_once BASE_PATH.'include/start.php';

