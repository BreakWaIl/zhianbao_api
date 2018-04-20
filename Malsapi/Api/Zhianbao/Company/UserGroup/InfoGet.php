<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_UserGroup_InfoGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'groupId' => array('name' => 'group_id','type'=>'int','require'=> true,'desc'=> '角色ID'),
            ),
		);
 	}

  
  /**
     * 获取角色信息详情
     * #desc 用于获取角色信息详情
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

        $rs['info'] = $groupInfo;
        return $rs;
    }

}

