<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Customer_Register extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'loginPwd' => array('name' => 'login_pwd', 'type' => 'string', 'min' => 6, 'max' => 16, 'require' => false, 'desc' => '登录密码'),
                     'mobile' => array('name' => 'mobile', 'type' => 'string', 'require' => true, 'desc' => '电话号码'),
                     'code' => array ('name' => 'code', 'type' => 'string', 'require' => true, 'min' => 6, 'desc' => '验证码'),
                     'source' => array('name' => 'source', 'type' => 'string', 'require' => true, 'desc' => '客户来源'),
            ),
		);
 	}

  
  /**
     * 客户注册
     * #desc 用于获取当客户注册信息
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $data = array(
            'mobile'=>$this->mobile,
            'source'=>$this->source,
            'create_time'=>time(),
            'last_modify'=>time(),
        );
        if(isset($this->loginPwd)){
            $salt = PhalApi_Tool::createRandStr ( 8 );
            $loginPwd = MD5(MD5($this->loginPwd.$salt));
            $data['salt'] = $salt;
            $data['login_pwd'] = $loginPwd;
        }
        $domain = new Domain_Jiafubao_Customer();

        DI ()->notorm->beginTransaction ( 'db_api' );
        try {
            //判断验证码

            $domainSms = new Domain_Zhianbao_Sms();
            $domainSms->checkCode($this->mobile,$this->code);
            $customerId =  $domain->CustomerRegister($data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
            return $rs;
        }
        unset($data['customer_pwd']);
        unset($data['salt']);
        $customerSession = new Domain_Jiafubao_CustomerSession();
        $session =  $customerSession->makeSession($customerId);
        $rs['info'] = $data;
        $rs['info']['session'] = $session;
        return $rs;
    }

}

