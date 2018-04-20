<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_WeChat_AgentAuthorize_FansInfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'code' => array('name' => 'code' , 'type' => 'string' , 'require' => true, 'desc' => 'CODE'),
                     'state' => array('name' => 'state' , 'type' => 'string' , 'require' => true, 'desc' => '附加参数'),
                     'appid' => array('name' => 'appid', 'type' => 'string', 'require' => true, 'desc' => '公众号appid'),
                     'redirectUrl' => array('name' => 'redirect_url', 'type' => 'string', 'require' => true, 'desc' => '跳转地址'),
            ),
        );
    }
  
  /**
     * 获取粉丝信息
     * #desc 用于获取当前粉丝信息
     * #return int code 操作码，0表示成功
     * #return string openid 粉丝openid
  */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $domain = new Domain_Zhianbao_Wechat();
        $appid = DI()->config->get('app.Pay.wechat.appid');
        $mp_appid = $appId = DI ()->config->get ( 'app.wechat.mp_appid' );
        $code = $this->code;
        try {
            $accessTokenInfo = $domain->getAgentAuthUserInfo($appid,$mp_appid,$code);
        } catch ( Exception $e ) {
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
            return $rs;
        }
        $fansInfo = json_encode($accessTokenInfo);
        DI ()->cookie->set('zab_agent',$fansInfo);
        header("Location:".$this->redirectUrl);
        exit();
    }
    
}
