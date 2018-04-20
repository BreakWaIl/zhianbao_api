<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_User_Register extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'loginName' => array('name' => 'login_name', 'type' => 'string', 'min' => 11, 'max' => 11 , 'require' => true, 'desc' => '用户名'),
                     'name' => array('name' => 'name', 'type' => 'string' , 'require' => true, 'desc' => '公司名称'),
                     'loginPwd' => array('name' => 'login_password', 'type' => 'string', 'min' => 6, 'max' => 20 , 'require' => true, 'desc' => '密码'),
                     'mobile' => array('name' => 'mobile',  'type' => 'string', 'min' => 11,'max'=> 11,   'require' => true, 'desc' => '手机号码'),
                     'sysType' => array('name' => 'sys_type', 'type' => 'enum', 'range' => array('zab','jfb','zgb'), 'require' => true, 'desc' => '系统类型'),
                 ),
		);
 	}

  
  /**
     * 监管者注册
     * #desc 用于监管者注册
     * #return int user_id 商户ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $regulatorDomain = new Domain_Zhianbao_Regulator();
        $data = array(
                'login_name' => $this->loginName,
                'login_password' => $this->loginPwd,
                'mobile' => $this->mobile,
                'name' => $this->name,
                'type' => $this->sysType
        );

        $userId = $regulatorDomain->regulatorRegister($data);
        $rs['regulator_id'] = $userId;

        return $rs;
    }

}

