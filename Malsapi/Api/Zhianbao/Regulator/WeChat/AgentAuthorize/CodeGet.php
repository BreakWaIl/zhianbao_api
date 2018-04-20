<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_WeChat_AgentAuthorize_CodeGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'callbackUrl' => array('name' => 'callback_url', 'type' => 'string', 'require' => true, 'desc' => '回调地址'),
            ),
        );
    }
  
  /**
     * 获取代理微信静默授权地址
     * #desc 用于获取代理微信静默授权地址
     * #return int code 操作码，0表示成功
     * #return string code_url 跳转地址
  */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $appid = DI()->config->get('app.Pay.wechat.appid');
        $componentAppid = DI ()->config->get ( 'app.wechat.mp_appid' );
        $shenpuApiUrl = DI ()->config->get ( 'app.shenpuApi.get_api_url' );
        $redirect_url = urlencode($this->callbackUrl);
        $callbackUrl = sprintf('http://%s/h5/authorize/agent_callback.php?redirect_url=',$shenpuApiUrl);
        $callbackUrl .= $redirect_url;
        $callbackUrl = urlencode($callbackUrl);
        $codeUrl = DI ()->config->get ( 'app.wechat.get_web_base_authorizer_code_url' );
        $codeUrl = sprintf($codeUrl,$appid,$callbackUrl,$componentAppid);
        $rs['code_url'] = $codeUrl;
        return $rs;
    }
    
}
