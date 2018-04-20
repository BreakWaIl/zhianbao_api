<?php
/**
 * 默认接口服务类
 *
 * @author: Andy 
 */
class Api_Zhianbao_WechatAuth_AccessTokenGet extends PhalApi_Api {
	public function getRules() {
		return array (
				'Go' => array (
						/*'appId' => array (
								'name' => 'app_id',
								'type' => 'string',
								'require' => true,
								'desc' => '应用ID'
						),*/
						'wechatId' => array (
								'name' => 'wechat_id',
								'type' => 'int',
								'min' => 1,
								'require' => true,
								'desc' => '商户公众号ID' 
						) 
				) 
		);
	}
	
	/**
	 * 获取公众号access_token
	 * 用于获取单个公众号access_token
	 *
	 * @return int code 操作码，0表示成功
	 * @return int wechat_id 公众号ID
	 * @return json access_token 公众号access_token
	 * @return string msg 提示信息
	 */
	public function Go() {
		$rs = array (
				'code' => 0,
				'msg' => '',
				'info' => array () 
		);
		
		try {
		    
		    $appId = DI ()->config->get ( 'app.wechat.mp_appid' );
			$domain = new Domain_Zhianbao_Wechat ();
			$accessToken = $domain->getAccessToken ( $appId,$this->wechatId );
			
			$rs ['info'] = array (
					'wechat_id' => $this->wechatId,
					'access_token' => $accessToken 
			);
		} catch ( Exception $e ) {
			$rs ['code'] = $e->getCode ();
			$rs ['msg'] = $e->getMessage ();
			return $rs;
		}
		
		return $rs;
	}
}
