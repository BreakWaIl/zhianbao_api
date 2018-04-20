<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class  Api_Zhianbao_Company_StaffCheck_Update extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'checkId' => array('name' => 'check_id', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '记录ID'),
                     'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '员工ID'),
                     'imgUrl' => array('name' => 'img_url', 'type' => 'string', 'require' => true, 'desc' => '图片路径'),
            ),
		);
 	}
	
  
  /**
     * 更新体检记录
     * #desc 用于更新体检记录
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

        //判断体检记录是否存在
        $checkDomain = new Domain_Zhianbao_StaffCheck();
        $checkInfo = $checkDomain->getBaseInfo($this->checkId);
        if( !$checkInfo) {
            DI()->logger->debug('Check record not found', $this->checkId);

            $rs['code'] = 124;
            $rs['msg'] = T('Check record not exists');
            return $rs;
        }

        //判断员工是否存在
        $staffDomain = new Domain_Zhianbao_Staff();
        $staffInfo = $staffDomain->getBaseInfo($this->staffId);
        if(! $staffInfo){
            $rs['code'] = 126;
            $rs['msg'] = T('Staff not exists');
            return $rs;
        }
        $data = array(
            'check_id' => $this->checkId,
            'staff_id' => $this->staffId,
            'name' => $staffInfo['name'],
            'img_url' => $this->imgUrl,
            'last_modify' => time(),
        );
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $res = $checkDomain->updateCheck($data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
            return $rs;
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
