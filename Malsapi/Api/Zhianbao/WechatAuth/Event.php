<?php
/**
 * 默认接口服务类
 *
 * @author: Andy 
 */
class Api_Zhianbao_WechatAuth_Event extends PhalApi_Api {
	public function getRules() {
		return array (
				'Go' => array (
						'signature' => array (
								'name' => 'signature',
								'type' => 'string',
								'require' => true,
								'desc' => 'signature' 
						),
						'timestamp' => array (
								'name' => 'timestamp',
								'type' => 'string',
								'require' => true,
								'desc' => 'timestamp' 
						),
						'nonce' => array (
								'name' => 'nonce',
								'type' => 'string',
								'require' => true,
								'desc' => 'nonce' 
						),
						'encryptType' => array (
								'name' => 'encrypt_type',
								'type' => 'string',
								'require' => true,
								'desc' => 'encrypt_type' 
						),
						'msgSignature' => array (
								'name' => 'msg_signature',
								'type' => 'string',
								'require' => true,
								'desc' => 'msg_signature' 
						),
					
				) 
		);
	}
	
	protected function filterCheck()
	{
	}
	
	/**
	 * 微信开放平台授权时间(不需要签名)
	 */
	public function Go() {
		
		// 第三方发送消息给公众平台
		$timeStamp = $this->timestamp;
		$nonce = $this->nonce;
		$msgSignature = $this->msgSignature;
		
		$encryptMsg = file_get_contents ( 'php://input' );
		/*$encryptMsg = '<xml>
    <AppId><![CDATA[wx8985ef1670e746ed]]></AppId>
    <Encrypt><![CDATA[Xo/a9Imim/c3XPmn8pxBrnT4Pgu2oUaDO2BtjGQTA9Gj1D7zkJFZ8xJ4ZAaHhEHMMx8zvgVmvtDudSkYeHmf5ByrJdYrcSMIqUnU7xQBZRhaFgi/QdUOZGVZgt4EqdrKlKb/xY7Bk5nsv12WT/cKbR4WT/0I1Wt3z+KCZxrCaXa21ich2qkEnS+eV4eujgt8DJOh7tk0+7t4XvvYFdJTJyGi4zLkrby0ekn3F7shWgBXc4rmIT6ZQ+PTKC5OjS/AaakDmkoVWGpw5pDjf3mO8zfbzI8oqaupbYK6UFuidjYkgtOqMI9o089UJ+b0cEjfkQmywC51esgGvATQ4wusL1X3j++PtEG88osRHpILZx04uwdvDI3LIDE5bnhJYEduhM/SDkdPiuz55tW76FWlJuEy7sRD5faLytmoxahh6sAw4tFJLKGczK7p+W18bvaUSgKCOOtK1PSmlzvQNf5ZtA==]]></Encrypt>
</xml>';*/
		//DI ()->logger->error ( 'event=>', $encryptMsg );
		
		if (empty ( $encryptMsg )) {
			exit ( 'success' );
		}
		
		$xml_tree = new DOMDocument ();
		$xml_tree->loadXML ( $encryptMsg );
		$arrayAppId = $xml_tree->getElementsByTagName ( 'AppId' );
		$appId = $arrayAppId->item ( 0 )->nodeValue;
		$arrayEncrypt = $xml_tree->getElementsByTagName ( 'Encrypt' );
		$encrypt = $arrayEncrypt->item ( 0 )->nodeValue;
		$format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
		$from_xml = sprintf ( $format, $encrypt );

		// 第三方收到公众号平台发送的消息
		$domain = new Domain_Zhianbao_WechatApp ();
		$info = $domain->getByAppId ( $appId );
		
		if (empty ( $info )) {
			exit ( 'success' );
		}
		
		$id = $info ['id'];
		$encodingAesKey = $info ['proxy_key'];
		$token = $info ['proxy_token'];
		$appId = $info ['appid'];
		$pc = new WechatAuth_WxBizMsgCrypt ( $token, $encodingAesKey, $appId );
		$msg = '';
		$errCode = $pc->decryptMsg ( $msgSignature, $timeStamp, $nonce, $from_xml, $msg );
		
		//日志
		DI ()->logger->info ( 'event msg=>', $msg );
		
		if ($errCode == 0) {
			$xml = new DOMDocument ();
			$xml->loadXML ( $msg );
			$array_InfoType = $xml->getElementsByTagName ( 'InfoType' );
			$InfoType = $array_InfoType->item ( 0 )->nodeValue;
			
			switch ($InfoType){
				case 'component_verify_ticket'://用于获取第三方平台接口调用凭据
					$array_e = $xml->getElementsByTagName ( 'ComponentVerifyTicket' );
					$component_verify_ticket = $array_e->item ( 0 )->nodeValue;
					$domain->updateTicket ( $id, $component_verify_ticket );
					break;
				case 'unauthorized'://是取消授权
					
					$array_e = $xml->getElementsByTagName ( 'AuthorizerAppid' );
					$AuthorizerAppid = $array_e->item ( 0 )->nodeValue;
					$domain->eventUnauthorized ( $AuthorizerAppid );
					break;
				case 'updateauthorized'://是更新授权
					$domain->eventUpdateauthorized (  );
					break;
				case 'authorized'://是授权成功通知
					$domain->eventAuthorized (  );
					break;
			}
			
		} else {
		}
		exit ( 'success' );
	}
}
