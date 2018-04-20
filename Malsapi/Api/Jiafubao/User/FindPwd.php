<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_User_FindPwd extends PhalApi_Api {
	
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
     * 家服宝商户找回密码
     * #desc 用于家服宝商户找回密码
     * return string user_id 用户ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //检测是否存在
        $domain = new Domain_Jiafubao_User();
        $info = $domain->checkRegister($this->loginName);
        if(empty($info)){
            $rs['code'] = 112;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }

        $rs = $domain->userFindPwd($this->loginName,$this->loginPwd,$this->code);

        return $rs;
    }

}

