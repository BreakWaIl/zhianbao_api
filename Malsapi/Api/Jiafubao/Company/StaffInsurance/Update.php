<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class  Api_Jiafubao_Company_StaffInsurance_Update extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'recordId' => array('name' => 'record_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '记录ID'),
                     'title' => array('name' => 'title', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '保险类别'),
                     'insuredName' => array('name' => 'insured_name', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '投保人'),
                     'policyBn' => array('name' => 'policy_bn', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '保单号'),
                     'endTime' => array('name' => 'end_time', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '截至有效期'),
            ),
		);
 	}
	
  
  /**
     * 更新保险记录信息
     * #desc 用于更新保险记录信息
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

        $data = array(
            'record_id' => $this->recordId,
            'title' => $this->title,
            'insured_name' => $this->insuredName,
            'policy_bn' => $this->policyBn,
            'end_time' => strtotime($this->endTime),
            'is_check' => 'n',
            'last_modify' => time(),
        );

        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $res = $staffInsuranceDomain->updateRecord($data,$recordInfo);
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
