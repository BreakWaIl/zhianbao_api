<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_UserGroup_Add extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id','type'=>'int','require'=> true,'desc'=> '公司ID'),
                     'name' => array('name' => 'name','type'=>'string','require'=> true,'desc'=> '角色名称'),
                     'roleIds' => array('name' => 'role_ids','type'=>'string','require'=> true,'desc'=> '权限'),
            ),
		);
 	}
  
  /**
   * 添加角色
   * #desc 用于添加角色
   * #return int code 操作码，0表示成功
   * #return int id  角色ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //检测公司是否存在
        $companyDomain = new Domain_Zhianbao_Company();
        $companyInfo = $companyDomain->getBaseInfo($this->companyId);
        if(! $companyInfo){
            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }

        $userGroupDomain = new Domain_Building_UserGroup();
        $info = $userGroupDomain->checkGroup($this->companyId, $this->name);
        if( !$info){
            $rs['code'] = 214;
            $rs['msg'] = T('Group exist');
            return $rs;
        }
        $data = array(
            'company_id' => $this->companyId,
            'name' => $this->name,
            'role' => $this->roleIds,
            'create_time' => time(),
            'last_modify' => time(),
        );
        $addRs = $userGroupDomain->addUserGroup($data);
        $rs['group_id'] = $addRs;
        return $rs;
    }
	
}
