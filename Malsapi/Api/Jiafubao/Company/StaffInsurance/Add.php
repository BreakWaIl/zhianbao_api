<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_StaffInsurance_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家政员ID'),
                'title' => array('name' => 'title', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '保险类别'),
                'insuredName' => array('name' => 'insured_name', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '投保人'),
                'policyBn' => array('name' => 'policy_bn', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '保单号'),
                'endTime' => array('name' => 'end_time', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '截至有效期'),
            ),
        );
    }


    /**
     * 添加保险记录信息
     * #desc 用于添加保险记录信息
     * #return int code 操作码，0表示成功
     * #return int record_id  记录ID
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
        //判断家政人员是否存在
        $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
        $staffInfo = $houseStaffDomain->getBaseInfo($this->staffId);
        if( !$staffInfo) {
            DI()->logger->debug('Staff not exists', $this->staffId);

            $rs['code'] = 126;
            $rs['msg'] = T('Staff not exists');
            return $rs;
        }

        $data = array(
            'company_id' => $this->companyId,
            'staff_id' => $this->staffId,
            'title' => $this->title,
            'insured_name' => $this->insuredName,
            'policy_bn' => $this->policyBn,
            'end_time' => strtotime($this->endTime),
            'create_time' => time(),
            'last_modify' => time(),
        );
        $recordId = 0;
        $staffInsuranceDomain = new Domain_Jiafubao_StaffInsurance();
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $recordId = $staffInsuranceDomain->addRecord($data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        $rs['info']['record_id'] = $recordId;

        return $rs;
    }

}
