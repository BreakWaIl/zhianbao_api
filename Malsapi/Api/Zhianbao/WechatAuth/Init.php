<?php
/**
 * 默认接口服务类
 *
 * @author: Andy
 */
class Api_Zhianbao_WechatAuth_Init extends PhalApi_Api {
    public function getRules() {
        return array (
				'Go' => array (
						'regulatorId' => array (
								'name' => 'regulator_id',
								'type' => 'string',
								'min' => 1,
								'require' => true,
								'desc' => '监管者ID'
								),
						'authCode' => array (
								'name' => 'auth_code',
								'type' => 'string',
								'require' => true,
								'desc' => '授权码'
								),
						'expiresIn' => array (
								'name' => 'expires_in',
								'type' => 'string',
								'require' => true,
								'desc' => '有效期'
								),
								)
								);
    }

    protected function filterCheck()
    {
    }


    /**
     * 授权成功后，初始化公众号
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
        );

        try {
             
            $appId = DI ()->config->get ( 'app.wechat.mp_appid' );
            $domain = new Domain_Zhianbao_WechatApp ();
             
            //公众号初始化
            $wechatId = $domain->init($appId,$this->regulatorId,$this->authCode,$this->expiresIn);
             
            $rs ['info'] = array (
                    'status'=>1,
					'regulator_id' => $this->regulatorId,
					'wechat_id' => $wechatId 
            );
        } catch ( Exception $e ) {
            $rs ['info'] = array (
					'status' =>0,
					'msg' => $e->getMessage () 
            );
        }

        return $rs;
    }
}
