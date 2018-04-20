<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_UserGroup_Delete extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'groupId' => array('name' => 'group_id','type'=>'int','require'=> true,'desc'=> '角色ID'),
            ),
		);
 	}
  
  /**
   * 删除角色
   * #desc 用于删除角色
   * #return int code 操作码，0表示成功
   * #return int status  0:成功 1:失败
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $userGroupDomain = new Domain_Zhianbao_UserGroup();
        $delRs = $userGroupDomain->delUserGroup($this->groupId);
        if(!$delRs){
            $rs['code'] = 218;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }else{
            $status = 0;
        }
        $rs['status'] = $status;
        return $rs;
    }
	
}
