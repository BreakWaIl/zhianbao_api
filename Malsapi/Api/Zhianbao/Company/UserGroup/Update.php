<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_UserGroup_Update extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'groupId' => array('name' => 'group_id','type'=>'int','require'=> true,'desc'=> '角色ID'),
                     'name' => array('name' => 'name','type'=>'string','require'=> true,'desc'=> '角色名称'),
                     'roleIds' => array('name' => 'role_ids','type'=>'string','require'=> true,'desc'=> '权限'),
            ),
		);
 	}
  
  /**
   * 更新角色信息
   * #desc 用于更新角色信息
   * #return int code 操作码，0表示成功
   * #return int id  客户id
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //查看角色是否存在
        $userGroupDomain = new Domain_Zhianbao_UserGroup();
        $groupInfo = $userGroupDomain->getBaseInfo($this->groupId);
        if(! $groupInfo){
            $rs['code'] = 150;
            $rs['msg'] = T('Role not exist');
            return $rs;
        }

        $userGroupDomain = new Domain_Zhianbao_UserGroup();
        $data = array(
            'name' => $this->name,
            'role' => $this->roleIds,
            'last_modify' => time(),
        );
        $updatedRs = $userGroupDomain->updateUserGroup($this->groupId,$data);
        if($updatedRs){
            $rs['status'] = 0;
        }else{
            $rs['status'] = 1;
        }
        return $rs;
    }
	
}
