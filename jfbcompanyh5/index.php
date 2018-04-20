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
    @header('Access-Control-Allow-Origin : http://jfbadh5.mshenpu.com');
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

    $noneedApiList = array('Jiafubao_User_Login','Jiafubao_Company_HouseStaff_Share_Share','Jiafubao_Order_Share_Share','Building_Wechat_JsApi_InfoGet','Jiafubao_Yuyue_Add');
    if(in_array($_REQUEST['service'],$noneedApiList)){
        return true;
    }

    $rs = DI()->response;
    $zabHid = DI()->cookie->get('zab_uid');
    if (empty($zabHid)) {
        $rs->setRet(200);
        $rs->setData(array('code'=>129,'msg'=>T('Please login first')));
        $rs->output();
        exit;
    }else{
        $domain = new Domain_Jiafubao_User();
        $sessionData = $domain->checkSession($zabHid);
        if($sessionData){
            //判断主账号
            if($sessionData['jfb']['parent_id'] == 0){
                $_REQUEST['user_id'] = $sessionData['jfb']['user_id'];
                $_REQUEST['operate_id'] = $sessionData['jfb']['user_id'];
                $_REQUEST['company_id'] = $sessionData['jfb']['company_id'];
            }else{
                $_REQUEST['user_id'] = $sessionData['jfb']['parent_id'];
                $_REQUEST['operate_id'] = $sessionData['jfb']['user_id'];
                $_REQUEST['company_id'] = $sessionData['jfb']['company_id'];
            }
            $whiteApiList = array( 'Jiafubao_User_Logout','Jiafubao_User_SessionCheck','Jiafubao_Company_Company_InfoGet','Jiafubao_Company_Information_Get','Jiafubao_Company_Information_InfoGet','Jiafubao_Company_Information_Add','Jiafubao_Company_Information_Update');
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



