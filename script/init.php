<?php
/**
 * 统一初始化
 */
 
/** ---------------- 根目录定义，自动加载 ---------------- **/

date_default_timezone_set('Asia/Shanghai');

defined('API_ROOT') || define('API_ROOT', dirname(__FILE__) . '/..');

require_once API_ROOT . '/PhalApi/PhalApi.php';
require_once dirname(__FILE__) . '/Malsapi.php';
require_once dirname(__FILE__) . '/SignFilter.php';
$loader = new PhalApi_Loader(API_ROOT, 'Library');

/** ---------------- 注册&初始化 基本服务组件 ---------------- **/

//自动加载
DI()->loader = $loader;

//配置
DI()->config = new PhalApi_Config_File(API_ROOT . '/Config');

//调试模式，$_GET['__debug__']可自行改名
DI()->debug = !empty($_GET['__debug__']) ? true : DI()->config->get('sys.debug');

//日记纪录
DI()->logger = new PhalApi_Logger_File(API_ROOT . '/Runtime', PhalApi_Logger::LOG_LEVEL_DEBUG | PhalApi_Logger::LOG_LEVEL_INFO | PhalApi_Logger::LOG_LEVEL_ERROR);

//数据操作 - 基于NotORM，$_GET['__sql__']可自行改名
DI()->notorm = new PhalApi_DB_NotORM(DI()->config->get('dbs'), !empty($_GET['__sql__']));

//翻译语言包设定
SL('zh_cn');


//数据操作 - 基于NotORM
DI()->notorm = function() {
	$debug = (isset($_GET['debug']) || isset($_POST['debug'])) ? true : false;
	return new PhalApi_DB_NotORM(DI()->config->get('dbs'), $debug);
};
/** ---------------- 定制注册 可选服务组件 ---------------- **/
function malsapi_request($service,$params = array(),$timeout = 3000){

    $api_url  =  DI ()->config->get ( 'app.shenpu.get_api_url' );
    $client = Malsapi::create()->withHost($api_url);
    $client->reset()->withService($service);
    if ($params) {
        foreach ($params as $k => $v) {
            $client->withParams($k,$v);
        }
    }

    $filter = new SignFilter();
    $client->withFilter($filter);
    $rs = $client->withTimeout($timeout)->request();

    if($rs->getRet() != 200){
        $response = DI()->response;
        $response->setRet($rs->getRet());
        $response->setMsg($rs->getMsg());
    }

    return array(
        'ret' => $rs->getRet(),
        'data' => $rs->getData(),
        'msg' => $rs->getMsg(),
    );
}

//签名验证服务
if(!defined('IN_SYS') || !IN_SYS){
    DI()->filter = 'Common_SignFilter';
}
 

/**
//缓存 - Memcache/Memcached
DI()->cache = function () {
    return new PhalApi_Cache_Memcache(DI()->config->get('sys.mc'));
};
 */

/**
//支持JsonP的返回
if (!empty($_GET['callback'])) {
    DI()->response = new PhalApi_Response_JsonP($_GET['callback']);
}
 */
