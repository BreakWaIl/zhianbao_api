<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_User_ChangePwd extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'userId' => array('name' => 'user_id', 'type' => 'string', 'require' => true, 'desc' => '用户ID'),
                    // 'operateId' => array('name' => 'operate_id', 'type' => 'string', 'require' => true, 'desc' => '操作者ID'),
                     'oldLoginPwd' => array('name' => 'old_login_password', 'type' => 'string', 'min' => 6, 'max' => 20 , 'require' => true, 'desc' => '旧密码'),
                     'newLoginPwd' => array('name' => 'new_login_password', 'type' => 'string', 'min' => 6, 'max' => 20 , 'require' => true, 'desc' => '新密码'),
            ),
		);
 	}

  
  /**
     * 商户修改密码
     * #desc 用于商户修改密码
     * return string user_id 用户ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //检测是否存在
        $domain = new Domain_Zhianbao_User();
        $info = $domain->getBaseInfo($this->userId);
        if(empty($info)){
            $rs['code'] = 112;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }
//        if($this->userId != $this->operateId){
//            //检测操作者是否存在
//            $domain = new Domain_Zhianbao_User();
//            $info = $domain->getBaseInfo($this->operateId);
//            if(empty($info)){
//                $rs['code'] = 112;
//                $rs['msg'] = T('Company not exists');
//                return $rs;
//            }
//        }

        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $domain->changePwd($info,$this->oldLoginPwd,$this->newLoginPwd);
            DI ()->notorm->commit( 'db_api' );
        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
            return $rs;
        }
        $rs['user_id'] = $this->userId;
        return $rs;
    }

}

