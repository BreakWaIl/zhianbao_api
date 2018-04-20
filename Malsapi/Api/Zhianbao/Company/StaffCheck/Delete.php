<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_StaffCheck_Delete extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'checkId' => array('name' => 'check_id', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '记录ID'),
            ),
		);
 	}
	
  
  /**
     * 删除体检记录
     * #desc 用于删除当前体检记录
     * #return int code 操作码，0表示成功
     * #return int status 状态 0 成功, 1 失败
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

        //判断体检记录是否存在
        $checkDomain = new Domain_Zhianbao_StaffCheck();
        $checkInfo = $checkDomain->getBaseInfo($this->checkId);
        if( !$checkInfo) {
            DI()->logger->debug('Check record not found', $this->checkId);

            $rs['code'] = 124;
            $rs['msg'] = T('Check record not exists');
            return $rs;
        }

        $res = $checkDomain->deleteCheck($this->checkId);
        if( $res){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info'] = $status;

        return $rs;
    }
	
}
