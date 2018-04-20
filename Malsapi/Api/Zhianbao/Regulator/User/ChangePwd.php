<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_User_ChangePwd extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id', 'type' => 'string', 'require' => true, 'desc' => '监管ID'),
                     'oldLoginPwd' => array('name' => 'old_login_password', 'type' => 'string', 'min' => 6, 'max' => 20 , 'require' => true, 'desc' => '旧密码'),
                     'newLoginPwd' => array('name' => 'new_login_password', 'type' => 'string', 'min' => 6, 'max' => 20 , 'require' => true, 'desc' => '新密码'),
            ),
		);
 	}

  
  /**
     * 监管修改密码
     * #desc 用于监管修改密码
     * return string user_id 用户ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //检测监管者是否存在
        $regulatorDomain = new Domain_Zhianbao_Regulator();
        $info = $regulatorDomain->getBaseInfo($this->regulatorId);
        if(empty($info)){
            $rs['code'] = 118;
            $rs['msg'] = T('Regulator not found');
            return $rs;
        }

        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $regulatorDomain->changePwd($info,$this->oldLoginPwd,$this->newLoginPwd);
            DI ()->notorm->commit( 'db_api' );
        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
            return $rs;
        }
        $rs['regulator_id'] = $this->regulatorId;
        return $rs;
    }

}

