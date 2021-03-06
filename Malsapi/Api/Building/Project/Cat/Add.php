<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Project_Cat_Add extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'projectId' => array('name' => 'project_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '项目ID'),
                     'catId' => array('name' => 'catId', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '班组ID，多个用逗号隔开'),
                     'operateId' => array('name' => 'operate_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '操作人ID'),
            ),
		);
 	}

  
  /**
     * 添加公司项目下的班组
     * #desc 用于添加公司项目下的班组
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
        //判断班组是否已存在项目中
        $filter = array('company_id' => $this->companyId, 'project_id' => $this->projectId, 'cat_id' => $this->catId);
        $projectDomain = new Domain_Building_Project();
        $info = $projectDomain->hashProjectToCat($filter);
        if( !$info){
            $rs['code'] = 213;
            $rs['msg'] = T('Cat exists in the project');
            return $rs;
        }

        $data = array(
            'company_id' => $this->companyId,
            'project_id' => $this->projectId,
            'cat_id' => $this->catId,
            'join_time' => time(),
            'create_time' => time(),
            'last_modify' => time(),
            'operate_id' => $this->operateId,
        );
        $projectDomain = new Domain_Building_Project();
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $res = $projectDomain->addProjectToCatId($data, $projectInfo);
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

