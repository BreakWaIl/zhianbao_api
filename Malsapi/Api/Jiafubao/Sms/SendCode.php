<?php
/**
 * 默认接口服务类
 *
 * @author: Andy 
 */
class Api_Jiafubao_Sms_SendCode extends PhalApi_Api {
	public function getRules() {
		return array (
				'Go' => array (
                    'mobile' => array ('name' => 'mobile', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '手机号'),
				) 
		);
	}


    /**
     * 家服宝商户发送短信验证码
     * #desc 用于家服宝商户店铺发送短信
     * #return int code 操作码，0表示成功
     */
	public function Go() {
		$rs = array ('code' => 0, 'msg' => '', 'info' => array ());
		$id = false;
		try {
            //发送验证码短信
            $domain = new Domain_Jiafubao_User();
            $id = $domain->SmsCode($this->mobile);

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
