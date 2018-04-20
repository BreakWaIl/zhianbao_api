<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_User_FindPwd extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'loginName' => array('name' => 'login_name', 'type' => 'string', 'min' => 11, 'max' => 11 , 'require' => true, 'desc' => '用户名'),
                     'loginPwd' => array('name' => 'login_password', 'type' => 'string', 'min' => 6, 'max' => 20 , 'require' => true, 'desc' => '密码'),
                     'code' => array ('name' => 'code', 'type' => 'string', 'require' => true, 'min' => 6, 'desc' => '验证码'),
            ),
		);
 	}

  
  /**
     * 监管者找回密码
     * #desc 用于监管者找回密码
     * return string user_id 用户ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //检测监管者是否存在
        $regulatorDomain = new Domain_Zhianbao_Regulator();
        $info = $regulatorDomain->checkRegister($this->loginName);
        if(empty($info)){
            $rs['code'] = 118;
            $rs['msg'] = T('Regulator not found');
            return $rs;
        }
        //
        //检测验证码
        $domainSms = new Domain_Zhianbao_Sms();
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $domainSms->checkCode($this->loginName,$this->code);
            $regulatorDomain->findPwd($info['id'],$this->loginPwd);
            DI ()->notorm->commit( 'db_api' );
        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
            return $rs;
        }
        $rs['login_name'] = $this->loginName;
        return $rs;
    }

}

