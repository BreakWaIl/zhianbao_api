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
    @header('Access-Control-Allow-Origin : http://jfbregu.mshenpu.com');
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
    $noneedApiList = array('Zhianbao_Regulator_User_Login','Zhianbao_Regulator_User_FindPwd','Zhianbao_Company_Sms_SendCode','Jiafubao_Yuyue_Delete','Jiafubao_Yuyue_InfoGet','Jiafubao_Yuyue_ListGet','Jiafubao_Yuyue_Pass','Jiafubao_Yuyue_UnPass','Jiafubao_Yuyue_Update','Jiafubao_Yuyue_Confirm');
    if(in_array($_REQUEST['service'],$noneedApiList)){
        return true;
    }

    $rs = DI()->response;
    $zabRid = DI()->cookie->get('zab_rid');
    if (empty($zabRid)) {
        $rs->setRet(200);
        $rs->setData(array('code'=>129,'msg'=>T('Please login first')));
        $rs->output();
        exit;
    }
    $sessionDomain = new Domain_Zhianbao_RegulatorSession();
    $sessionData = $sessionDomain->checkSession($zabRid);
    if($sessionData){
        $_REQUEST['regulator_id'] = $sessionData['regulator_id'];
    }else{
        DI()->cookie->delete('zab_rid');
        $rs->setRet(200);
        $rs->setData(array('code'=>129,'msg'=>T ('Please login first')));
        $rs->output();
        exit;
    }

}



