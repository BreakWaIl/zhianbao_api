<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Project_Sub_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'projectId' => array('name' => 'project_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '项目ID'),
            ),
		);
 	}
  
  /**
   * 获取公司项目下的管理员列表
   * #desc 用于获取公司管理员列表
   * #return int code 操作码，0表示成功
   * #return int id ID
   * #return string login_name 管理员账号
   * #return string name 管理员名称
   * #return int group_id 角色ID
   * #return string group_name 角色名称
   * #return int create_time 创建时间
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断公司是否存在
        $domainCompany = new Domain_Zhianbao_Company();
        $companyInfo = $domainCompany->getBaseInfo($this->companyId);
        if (empty($companyInfo)) {
            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }
        //判断公司项目是否存在
        $projectDomain = new Domain_Building_Project();
        $projectInfo = $projectDomain->getBaseInfo($this->projectId);
        if (empty($projectInfo)) {
            $rs['code'] = 192;
            $rs['msg'] = T('Project not exists');
            return $rs;
        }

        $filter = array('company_id' => $this->companyId, 'project_id' => $this->projectId);
        $projectDomain = new Domain_Building_Project();
        $list = $projectDomain->getAllSub($filter);
        $total = $projectDomain->getCountSub($filter);

        $rs['list'] = $list;
        $rs['total'] = $total;

        return $rs;
    }
	
}
