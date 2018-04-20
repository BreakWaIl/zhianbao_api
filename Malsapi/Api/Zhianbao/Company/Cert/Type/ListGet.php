<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_Cert_Type_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'staffId' => array('name' => 'staff_id','type'=>'int','require'=> true,'desc'=> '人员ID'),
            ),
		);
 	}
  
  /**
   * 获取员工证书类型列表
   * #desc 用于获取员工证书类型列表
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
        //查看员工是否存在
        $staffDomain = new Domain_Zhianbao_Staff();
        $staffInfo = $staffDomain->getBaseInfo($this->staffId);
        if(! $staffInfo){
            $rs['code'] = 126;
            $rs['msg'] = T('Staff not exists');
            return $rs;
        }

        $domainNotice = new Domain_Zhianbao_Notice();
        $regulatorIds = $domainNotice->getCompanyIds($this->companyId);
        $filter = array('regulator_id' => $regulatorIds['regulator_id']);
        $certTypeDomain = new Domain_Zhianbao_CertType();
        $list = $certTypeDomain->getAllCertType($filter,$staffInfo);
        $rs['list'] = $list;
        return $rs;
    }
	
}
