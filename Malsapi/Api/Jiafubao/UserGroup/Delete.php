<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_UserGroup_Delete extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'userId' => array('name' => 'user_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主账户ID'),
                     'groupId' => array('name' => 'group_id','type'=>'int','require'=> true,'desc'=> '角色ID'),
            ),
		);
 	}
  
  /**
   * 删除家服宝角色
   * #desc 用于家服宝删除角色
   * #return int code 操作码，0表示成功
   * #return int status  0:成功 1:失败
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

        $userDomain = new Domain_Jiafubao_User();
        $delRs = $userDomain->delUserGroup($this->userId,$this->groupId);
        if(!$delRs){
            $status = 1;
        }else{
            $status = 0;
        }
        $rs['status'] = $status;
        return $rs;
    }
	
}
