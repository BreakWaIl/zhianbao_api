<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Attendance_Project_Cat_InfoGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'require' => false, 'desc' => '公司ID'),
                     'projectId' => array('name' => 'project_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '项目ID'),
            ),
		);
 	}

  /**
     * 获取项目下所属班组
     * #desc 用于获取项目下所属班组
     * #return int code 操作码，0表示成功
  */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断公司是否存在
        $domainCompany = new Domain_Zhianbao_Company();
        $companyInfo = $domainCompany->getBaseInfo($this->companyId);
        if (empty($companyInfo)) {
            DI()->logger->debug('Company not exists', $this->companyId);

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
        $filter = array();
        $filter['company_id'] = $this->companyId;
        $filter['project_id'] = $this->projectId;
        $AttendanceDomain = new Domain_Building_Attendance();
        $list = $AttendanceDomain->getAllProjectCat($filter);

        $rs['info'] = $list;

        return $rs;
    }
	
}
