<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_User_ChangePwd extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'userId' => array('name' => 'user_id', 'type' => 'string', 'require' => true, 'desc' => '用户ID'),
                     'oldLoginPwd' => array('name' => 'old_login_password', 'type' => 'string', 'min' => 6, 'max' => 20 , 'require' => true, 'desc' => '旧密码'),
                     'newLoginPwd' => array('name' => 'new_login_password', 'type' => 'string', 'min' => 6, 'max' => 20 , 'require' => true, 'desc' => '新密码'),
            ),
		);
 	}

  
  /**
     * 家服宝商户修改密码
     * #desc 用于家服宝商户修改密码
     * return string user_id 用户ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        //获取用户名
        $domain = new Domain_Jiafubao_User();
        $info = $domain->getBaseByUserId($this->userId);
        if( empty($info)){
            $rs['code'] = 112;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }

        try {
            $loginName = $info['mobile'];
            $domain->changePwd($loginName,$this->oldLoginPwd,$this->newLoginPwd);

        } catch ( Exception $e ) {
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }
        $rs['user_id'] = $this->userId;
        return $rs;
    }

}

