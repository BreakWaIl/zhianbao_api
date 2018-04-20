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
    @header('Access-Control-Allow-Origin : http://jfbayh5.mshenpu.com');
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

    $rs = DI()->response;
    $jfbstid = DI()->cookie->get('jfb_stid');

    //屏蔽不需要登录权限的接口
    $noneedApiList = array('Jiafubao_Sms_SendCode','Jiafubao_Staff_WeChat_Login','Jiafubao_Company_HouseStaff_Register','Zhianbao_Regulator_WeChat_AgentAuthorize_CodeGet','Jiafubao_Staff_Login');
    if(in_array($_REQUEST['service'],$noneedApiList)){
        return true;
    }
    if (empty($jfbstid)) {
        $rs->setRet(200);
        $rs->setRet(200);
        $rs->setData(array('code'=>178,'msg'=>T ('Please login first')));
        $rs->output();
        exit;
    }else{
        $staffSessionDomain = new Domain_Jiafubao_StaffSession();
        $sessionData = $staffSessionDomain->checkSession($jfbstid);
        if (!empty($sessionData)) {
            $_REQUEST['staff_id'] = $sessionData['staff_id'];
        } else {
            DI()->cookie->delete('jfb_csid');
            $rs->setRet(200);
            $rs->setData(array('code'=>178,'msg'=>T ('Please login first')));
            $rs->output();
            exit;
        }
    }

}



