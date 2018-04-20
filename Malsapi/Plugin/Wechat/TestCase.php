<?php
class Plugin_Wechat_TestCase{

    public function process($inMessage, &$outMessage) {

        $outMessage = new Wechat_OutMessage_Text();
         
        if ($inMessage->getMsgType() == 'event') {
            $outMessage->setContent($inMessage->getEvent() . 'from_callback');
        }

        if ($inMessage->getContent() == 'TESTCOMPONENT_MSG_TYPE_TEXT') {
            $outMessage->setContent('TESTCOMPONENT_MSG_TYPE_TEXT_callback');
        }


        if ( !(strpos($inMessage->getContent(), 'QUERY_AUTH_CODE') === FALSE) ) {
            list ( $sufixx, $authcode ) = explode ( ':', $inMessage->getContent() );
            $authInfo = $this->getAuthInfo ( $authcode );
            // WeUtility::logging ( 'platform-test-send-message', var_export ( $auth_info, true ) );
            if(!empty($authInfo)){

                $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=%s";
                $url = sprintf($url,$authInfo ['authorization_info'] ['authorizer_access_token']);
                $curl = new PhalApi_CUrl(2);
                $data = array (
					'touser' => $inMessage->getFromUserName(),
					'msgtype' => 'text',
					'text' => array ('content' => $authcode . '_from_api')
                );


                $rs = $curl->post($url, urldecode ( json_encode ( $data ) ));

                DI ()->logger->info ( 'custom_send Data',$data );
                DI ()->logger->info ( 'custom_send Result',$rs );
            }

            exit ( '' );
        }
         
    }

    public function getAuthInfo($code) {
        $domainWechatApp = new Domain_Zhianbao_WechatApp();
        $appId = DI ()->config->get ( 'app.wechat.mp_appid' );
        $info = $domainWechatApp->getByAppId ( $appId, '*' );
        $accessToken = $domainWechatApp->getComponentAccessToken ( $info );

        DI ()->logger->info ( 'getComponentAccessToken Data',$accessToken );

        $response = array();
        if($accessToken){

            $curl = new PhalApi_CUrl(2);
            $post = array (
				'component_appid' => $appId,
				'authorization_code' => $code 
            );

            $url =  'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=%s';
            $url = sprintf($url,$accessToken);
            $response = $curl->post ($url, json_encode($post) );
            DI ()->logger->info ( 'api_query_auth Data',$response );
            if(!empty($response)){
                $response = json_decode($response,true);
                if(isset($response['errcode'])){
                    return array();
                }
            }
        }
        return $response;
    }
}
