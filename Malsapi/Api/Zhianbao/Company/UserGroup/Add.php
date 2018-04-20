<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_UserGroup_Add extends PhalApi_Api {
	
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
   * 添加公司角色
   * #desc 用于添加公司角色
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

        $userGroupDomain = new Domain_Zhianbao_UserGroup();
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
