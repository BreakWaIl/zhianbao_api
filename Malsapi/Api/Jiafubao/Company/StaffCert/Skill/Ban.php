<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_StaffCert_Skill_Ban extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'certId' => array('name' => 'cert_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '证书ID'),
            ),
		);
 	}
	
  
  /**
     * 作废职业技能证书
     * #desc 用于作废职业技能证书
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
        //判断技能证书是否存在
        $staffCertDomain = new Domain_Jiafubao_StaffSkillCert();
        $certInfo = $staffCertDomain->getBaseInfo($this->certId);
        if( !$certInfo) {
            $rs['code'] = 157;
            $rs['msg'] = T('Skill cert not exists');
            return $rs;
        }
        $res = $staffCertDomain->banSkillCert($this->certId);
        if( $res){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info']['status'] = $status;

        return $rs;
    }
	
}
