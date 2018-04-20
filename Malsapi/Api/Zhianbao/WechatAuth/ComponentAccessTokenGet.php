<?php
/**
 * 默认接口服务类
 *
 * @author: Andy
 */
class Api_Zhianbao_WechatAuth_ComponentAccessTokenGet extends PhalApi_Api {
    public function getRules() {
        return array (
				'Go' => array (
        /*'appId' => array (
         'name' => 'app_id',
         'type' => 'string',
         'require' => true,
         'desc' => '应用ID'
         ),*/

        )
        );
    }

    /**
     * 获取平台component_access_token
     * 用于获取单个平台component_access_token
     *
     * @return int code 操作码，0表示成功
     * @return int app_id 平台ID
     * @return json component_access_token 平台component_access_token
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
             
            $domain = new Domain_Zhianbao_WechatApp();
            $info = $domain->getByAppId ( $appId, '*' );
            if (empty ( $info )) {
                throw new LogicException ( T ( 'Appid does not exist' ), 126 );
            }
            	
            $accessToken = $domain->getComponentAccessToken ( $info );
            	
            $rs ['info'] = array (
					'app_id' => $appId,
					'component_access_token' => $accessToken 
            );
        } catch ( Exception $e ) {
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        return $rs;
    }
}
