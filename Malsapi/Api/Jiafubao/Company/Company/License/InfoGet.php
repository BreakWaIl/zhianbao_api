<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_Company_License_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'licenseId' => array('name' => 'license_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '执照ID'),
            ),
        );
    }
  
  /**
     * 获取公司执照详情
     * #desc 用于获取公司执照详情
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

        //判断公司执照是否存在
        $licenseDomain = new Domain_Jiafubao_CompanyLicense();
        $licenseInfo = $licenseDomain->getBaseInfo($this->licenseId);
        if( !$licenseInfo) {
            DI()->logger->debug('License not found', $this->licenseId);

            $rs['code'] = 156;
            $rs['msg'] = T('License not exists');
            return $rs;
        }

        $rs['info'] = $licenseInfo;

        return $rs;
    }
    
}
