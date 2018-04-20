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
    @header('Access-Control-Allow-Origin : http://zgbh5.mshenpu.com');
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

    $noneedApiList = array('Zhianbao_Company_User_Login','Building_Wechat_JsApi_InfoGet');
    if(in_array($_REQUEST['service'],$noneedApiList)){
        return true;
    }

    $rs = DI()->response;
    $zabHid = DI()->cookie->get('zab_hid');
    if (empty($zabHid)) {
        $rs->setRet(200);
        $rs->setData(array('code'=>129,'msg'=>T('Please login first')));
        $rs->output();
        exit;
    }else{
        $sessionDomain = new Domain_Zhianbao_UserSession();
        $sessionData = $sessionDomain->checkSession($zabHid);
        if($sessionData){
            if($sessionData['parent_id'] == 0){
                $_REQUEST['user_id'] = $sessionData['user_id'];
                $_REQUEST['operate_id'] = $sessionData['user_id'];
                $_REQUEST['company_id'] = $sessionData['company_id'];
            }else{
                $_REQUEST['user_id'] = $sessionData['parent_id'];
                $_REQUEST['operate_id'] = $sessionData['user_id'];
                $_REQUEST['company_id'] = $sessionData['company_id'];
            }
            $whiteApiList = array('Building_SubAccount_Logout','Building_Attendance_InfoGet','Zhianbao_Company_User_SessionCheck','Building_Wechat_JsApi_InfoGet');
            if (!in_array($_REQUEST['service'], $whiteApiList)) {
                $rs->setRet(200);
                $rs->setData(array('code'=>194,'msg'=>'Api No permissions'));
                $rs->output();
                exit;
            }
        }else{
            DI()->cookie->delete('zab_uid');
            $rs->setRet(200);
            $rs->setData(array('code'=>129,'msg'=>T ('Please login first')));
            $rs->output();
            exit;
        }

    }


}



