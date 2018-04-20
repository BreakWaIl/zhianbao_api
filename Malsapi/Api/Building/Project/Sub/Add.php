<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Project_Sub_Add extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'projectId' => array('name' => 'project_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '项目ID'),
                     'subId' => array('name' => 'sub_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '管理员ID'),
                     'operateId' => array('name' => 'operate_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '操作人ID'),
            ),
		);
 	}

  
  /**
     * 添加公司项目下的管理员
     * #desc 用于添加公司项目下的管理员
     * #return int info
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
            'create_time' => time(),
            'last_modify' => time(),
            'operate_id' => $this->operateId,
        );
        $projectDomain = new Domain_Building_Project();
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $res = $projectDomain->addProjectSub($data);
            DI ()->notorm->commit( 'db_api' );

            $rs['info'] = $res;

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        return $rs;
    }

}

