<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_StaffInsurance_Delete extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'recordId' => array('name' => 'record_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '记录ID'),
            ),
		);
 	}
	
  
  /**
     * 删除保险记录
     * #desc 用于删除当前保险记录
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
        //判断保险记录是否存在
        $staffInsuranceDomain = new Domain_Jiafubao_StaffInsurance();
        $recordInfo = $staffInsuranceDomain->getBaseInfo($this->recordId);
        if( !$recordInfo) {
            $rs['code'] = 153;
            $rs['msg'] = T('Insurance record not exist');
            return $rs;
        }

//        $res = $staffInsuranceDomain->deleteRecord($this->recordId);
        $res = 0;
        if( $res){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info'] = $status;

        return $rs;
    }
	
}
