<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Project_Sub_Delete extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'projectId' => array('name' => 'project_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '项目ID'),
                     'subId' => array('name' => 'sub_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '管理员ID'),
            ),
		);
 	}
	
  
  /**
     * 删除项目下的管理员
     * #desc 删除项目下的管理员
     * #return int code 操作码，0表示成功
     * #return int status 状态 0 成功, 1 失败
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
        //判断项目是否完成
        if($projectInfo['status'] == 'finish'){
            $rs['code'] = 211;
            $rs['msg'] = T('Project finish');
            return $rs;
        }
        //判断管理员是否存在
        $subDomain = new Domain_Building_SubAccount();
        $subInfo = $subDomain->getBaseInfo($this->subId);
        if ( !$subInfo) {
            $rs['code'] = 215;
            $rs['msg'] = T('sub account not exists');
            return $rs;
        }
        $data = array(
            'company_id' => $this->companyId,
            'project_id' => $this->projectId,
            'sub_id' => $this->subId,
        );
        $projectDomain = new Domain_Building_Project();
        $res = $projectDomain->deleteSub($data);
        if( $res){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info'] = $status;

        return $rs;
    }
	
}
