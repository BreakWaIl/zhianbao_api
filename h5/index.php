<?php
/**
 *
 * 神铺商户管理后台入口
 *
 */

require_once dirname(__FILE__) . '/init.php';

$debug = DI ()->config->get ( 'sys.debug' );
if($debug){

}else{
    @header('Access-Control-Allow-Credentials : true');
    @header('Access-Control-Allow-Origin : http://zabreguh5.mshenpu.com');
}

//装载你的接口
DI()->loader->addDirs(array('Malsapi','Library'));

$api = new PhalApi();

checkLogin();
if(isset($_REQUEST['service']))$_REQUEST['service'] = $_REQUEST['service'].'.Go';
$rs = $api->response();
$rs->output();
/** ---------------- 响应接口请求 ---------------- **/

function checkLogin(){
    $noneedApiList = array('Zhianbao_Regulator_WeChat_AgentAuthorize_FansInfoGet');
    if(in_array($_REQUEST['service'],$noneedApiList)){
        return true;
    }
    $rs = DI()->response;
    $zabRid = DI()->cookie->get('zab_h5reguid');
    if (empty($zabRid)) {
        $rs->setRet(200);
        $rs->setData(array('code'=>118,'msg'=>T('Regulator not exists')));
        $rs->output();
        exit;
    }else{
        $_REQUEST['regulator_id'] = $zabRid;
    }


}



