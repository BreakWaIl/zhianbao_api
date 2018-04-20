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
    @header('Access-Control-Allow-Origin : http://jfbuserh5.mshenpu.com');
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
    if(isset($_REQUEST['jfb_csid'])){
        $jfbCsid = $_REQUEST['jfb_csid'];//customer_session
    }
    if(isset($_REQUEST['jfb_cpid'])){
        $jfbCpid = $_REQUEST['jfb_cpid'];//company_id
    }

    if (empty($jfbCpid)) {
        $rs->setRet(200);
        $rs->setData(array('code'=>160,'msg'=>T ('Customer not exists')));
        $rs->output();
        exit;
    }else{
        $_REQUEST['company_id'] = $jfbCpid;
    }

    //屏蔽不需要登录权限的接口
    $noneedApiList = array('Zhianbao_Company_Sms_SendCode','Jiafubao_MiniSoft_Customer_Login','Jiafubao_MiniSoft_Customer_WeChat_Login');
    if(in_array($_REQUEST['service'],$noneedApiList)){
        return true;
    }

    if (empty($jfbCsid)) {
        $rs->setRet(200);
        $rs->setData(array('code'=>160,'msg'=>T ('Customer not exists')));
        $rs->output();
        exit;
    }else{
        $customerSessionDomain = new Domain_Jiafubao_CustomerSession();
        $sessionData = $customerSessionDomain->checkSession($jfbCsid);
        if (!empty($sessionData)) {
            $_REQUEST['customer_id'] = $sessionData['customer_id'];
        } else {
            DI()->cookie->delete('jfb_csid');
            $rs->setRet(200);
            $rs->setData(array('code'=>160,'msg'=>T ('Customer not exists')));
            $rs->output();
            exit;
        }
    }

}



