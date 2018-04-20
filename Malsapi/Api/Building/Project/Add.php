<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Project_Add extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
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
     * 添加公司项目
     * #desc 用于添加公司项目
     * #return int project_id 项目ID
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

        $data = array(
            'company_id' => $this->companyId,
            'name' => $this->name,
            'province' => $this->province,
            'city' => $this->city,
            'district' => $this->district,
            'address' => $this->address,
            'create_time' => time(),
            'last_modify' => time(),
            'operate_id' => $this->operateId,
            'sign_config' => json_encode($this->signConfig),
        );
        $projectDomain = new Domain_Building_Project();
        //检测设置时间
        $config = $projectDomain->checkConfig($this->signConfig);
        if( !$config){
            $rs['code'] = 216;
            $rs['msg'] = T('Config time is wrong');
            return $rs;
        }
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $projectId = $projectDomain->add($data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        $rs['project_id'] = $projectId;

        return $rs;
    }

}

