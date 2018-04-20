<?php
/**
 * 默认接口服务类
 *
 * @author: Andy 
 */
class Api_Zhianbao_WechatAuth_AuthUrlGet extends PhalApi_Api {
	public function getRules() {
		return array (
				'Go' => array (

						'regulatorId' => array (
								'name' => 'regulator_id',
								'type' => 'string',
								'require' => true,
								'desc' => '监管者ID'
						),
						'callback' => array (
								'name' => 'callback',
								'type' => 'string',
								'require' => true,
								'desc' => '回调地址'
						),
				) 
		);
	}
	
 	protected function filterCheck()
 	{
 	}
	
	
	/**
	 * 获取开放平台授权地址
	 *
	 * @return int code 操作码，0表示成功

	 * @return string url 
	 * @return string msg 提示信息
	 */
	public function Go() {
		$rs = array (
				'code' => 0,
				'msg' => '',
				'info' => array ()
				//'info' => array ('auth_url'=>'https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=wx8985ef1670e746ed&pre_auth_code=preauthcode@@@hvtaL2jB4ehmspfgfXke7i27_9BoeF5wKQhDja670qy33VeBsPfmrjnl_LLv6J7w&redirect_uri=http%3A%2F%2Fapi.mshenpu.com%2Fprivate%2FwechatOAuth%2Fcallback.php%3Fuser_id%3D1%26shop_id%3D1')
		);
		
		try {
			
		    $appId = DI ()->config->get ( 'app.wechat.mp_appid' );
		    $shenpuApiUrl = DI ()->config->get ( 'app.shenpuApi.get_api_url' );
		    //$callback = sprintf('http://%s/private/wechatOAuth/callback.php?user_id=%s&shop_id=%s',$shenpuApiUrl,$this->userId,$this->shopId);
		    $callback = urlencode($this->callback);
			$domain = new Domain_Zhianbao_WechatApp ();
			$authUrl = $domain->getAuthUrl($appId,$callback);
			
			$rs ['info'] = array (
					'auth_url' => $authUrl 
			);
		} catch ( Exception $e ) {
			$rs ['code'] = $e->getCode ();
			$rs ['msg'] = $e->getMessage ();
		}
		
		return $rs;
	}
}
