<?php
/**
 * 默认接口服务类
 *
 * @author: Andy 
 */
class Api_Zhianbao_Company_Sms_SendCode extends PhalApi_Api {
	public function getRules() {
		return array (
				'Go' => array (
                    'mobile' => array ('name' => 'mobile', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '手机号'),
				) 
		);
	}


    /**
     * 商户发送短信验证码
     * #desc 用于商户店铺发送短信
     * #return int code 操作码，0表示成功
     * #return int user_id 商户ID
     * #return int shop_id 店铺ID
     */
	public function Go() {
		$rs = array ('code' => 0, 'msg' => '', 'info' => array ());
		$id = false;
		try {
            //发送验证码短信
            $domainShopSms = new Domain_Zhianbao_Sms();
            $id = $domainShopSms->sendCode($this->mobile);

		} catch ( Exception $e ) {
			$rs ['code'] = $e->getCode ();
			$rs ['msg'] = $e->getMessage ();
		}
		
		if($id){
			$rs['status'] = true;
			$rs['id'] = $id;
		}else{
			$rs['status'] = false;
		}
		return $rs;
	}
}
