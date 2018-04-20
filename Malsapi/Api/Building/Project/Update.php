<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Project_Update extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'projectId' => array('name' => 'project_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '项目ID'),
                     'name' => array('name' => 'name', 'type' => 'string' , 'min' => 1, 'require' => true, 'desc' => '项目名称'),
                     'province' => array('name' => 'province', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '省份'),
                     'city' => array('name' => 'city', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '城市'),
                     'district' => array('name' => 'district', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '区县'),
                     'address' => array('name' => 'address', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '详细地址'),
                     'operateId' => array('name' => 'operate_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '操作人ID'),
                     'signConfig' => array('name' => 'sign_config', 'type' => 'array', 'format'=>'json', 'require'=> true,'desc'=> '打卡时间设置'),
            ),
		);
 	}

  
  /**
     * 更新公司项目信息
     * #desc 用于更新公司项目信息
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
        //判断是否已完成
        if($projectInfo['status'] == 'finish') {
            $rs['code'] = 211;
            $rs['msg'] = T('Project finish');
            return $rs;
        }
        $data = array(
            'project_id' => $this->projectId,
            'name' => $this->name,
            'province' => $this->province,
            'city' => $this->city,
            'district' => $this->district,
            'address' => $this->address,
            'last_modify' => time(),
            'operate_id' => $this->operateId,
            'sign_config' => json_encode($this->signConfig),
        );
        //检测设置时间
        $config = $projectDomain->checkConfig($this->signConfig);
        if( !$config){
            $rs['code'] = 216;
            $rs['msg'] = T('Config time is wrong');
            return $rs;
        }
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $res = $projectDomain->update($data,$this->companyId);
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

        $rs['info']['status'] = $status;

        return $rs;
    }

}

