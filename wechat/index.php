<?php
/**
 * 响应微信服务器入口 -没有签名
 *
 */


require_once dirname(__FILE__) . '/init.php';

$debug = DI ()->config->get ( 'sys.debug' );
if($debug){
    DI ()->logger->debug ( 'HTTP_RAW_POST_DATA Data',$GLOBALS['HTTP_RAW_POST_DATA'] );
    DI ()->logger->debug ( '$_GET Data',$_GET);
}

if (!isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
    die('Access denied!');
}


//装载项目代码和扩展类库
DI()->loader->addDirs(array('Malsapi','Library'));


/** ---------------- 微信轻聊版 ---------------- **/

$robot = new Wechat_Lite();
$rs = $robot->response();
$rs->output();