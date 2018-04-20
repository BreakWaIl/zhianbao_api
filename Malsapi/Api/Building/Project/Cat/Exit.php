<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Project_Cat_Exit extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'projectId' => array('name' => 'project_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '项目ID'),
                     'catId' => array('name' => 'cat_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '班组ID'),
                     'operateId' => array('name' => 'operate_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '操作人ID'),
                ),
		);
 	}

  
  /**
     * 项目班组退场
     * #desc 用于更新项目班组退场
     * #return int status 状态 0 成功 1 失败
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
        //判断项目是否存在
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
        //判断班组是否存在
        $catDomain = new Domain_Building_Cat();
        $catInfo = $catDomain->getBaseInfo($this->catId);
        if (empty($catInfo)) {
            $rs['code'] = 106;
            $rs['msg'] = T('Categroy not exists');
            return $rs;
        }
        $filter = array(
            'company_id' => $this->companyId,
            'project_id' => $this->projectId,
            'cat_id' => $this->catId,
        );
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $projectDomain = new Domain_Building_Project();
            $res = $projectDomain->exitCat($filter,$projectInfo,$catInfo,$this->operateId);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }
        if( $res){
            $status = 0;
        }else{
            $status = 1;
        }
        $rs['status'] = $status;

        return $rs;
    }

}

