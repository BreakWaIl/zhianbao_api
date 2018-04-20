<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Wechat_JsApi_InfoGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                'url' => array('name' => 'url', 'type' => 'string',  'require' => true, 'desc' => '地址'),
            ),
		);
 	}
	
  
  /**
     * 获取JSapi凭证
     * #desc 用于获取JSapi凭证
     * #return int code 操作码，0表示成功
     * #return string appid 公众号appid
     * #return string noncestr 随机码
     * #return string timestamp 时间戳
     * #return string signature 签名
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $domainWechat = new Domain_Building_Wechat();
        $appid = $appId = DI ()->config->get ( 'app.wechat.mp_appid' );
        $url = urldecode($this->url);
        try {
            $info = $domainWechat->getJsApi($appid,$url);
        } catch ( Exception $e ) {
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
            return $rs;
        }
        $rs['info'] = $info;

        return $rs;
    }
	
}
