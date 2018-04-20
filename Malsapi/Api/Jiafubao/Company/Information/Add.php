<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_Information_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'telephone' => array('name' => 'telephone', 'type' => 'string', 'min'=> 1, 'require' => true, 'desc' => '公司电话'),
                'legalPerson' => array('name' => 'legal_person', 'type' => 'string', 'min'=> 1, 'require' => true, 'desc' => '法人名称'),
                'mobile' => array('name' => 'mobile', 'type' => 'string', 'min'=> 1, 'require' => true, 'desc' => '手机号'),
                'intermediaryFee' => array('name' => 'intermediary_fee', 'type' => 'array', 'format'=>'json', 'require' => true, 'desc' => '中介费'),
                'teacherNumber' => array('name' => 'teacher_number', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '老师数量'),
                'staffNumber' => array('name' => 'staff_number', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家政员数量'),
                'business' => array('name' => 'business', 'type' => 'array', 'format'=>'json', 'require' => true, 'desc' => '经营项目'),
                'partWorkCharge' => array('name' => 'part_work_charge', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '钟点工收费标准'),
                'isIntermediaryFee' => array('name' => 'is_intermediary_fee', 'type'=>'array','format' => 'json', 'require'=> true,'desc'=> '钟点工是否有中介费'),
                'isCleaning' => array('name' => 'is_cleaning', 'type'=>'enum','range' => array('y','n'), 'default' => 'n', 'require'=> true,'desc'=> '是否做开荒保洁:y 是 n 否'),
                'charges' => array('name' => 'charges', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '收费标准'),
                'introduction' => array('name' => 'introduction', 'type' => 'string', 'min'=> 1, 'require' => true, 'desc' => '公司简介'),
                'remark' => array('name' => 'remark', 'type' => 'string', 'require' => false, 'desc' => '备注'),
                'companyName' => array('name' => 'company_name', 'type' => 'string', 'require' => false, 'desc' => '公司名称'),
                'address' => array('name' => 'address', 'type'=>'string',  'require'=> false,'desc'=> '详细地址'),
                'zipCode' => array('name' => 'zip_code', 'type' => 'int', 'min' => 0, 'require' => false, 'desc' => '邮编'),
            ),
        );
    }


    /**
     * 添加公司登记表
     * #desc 用于添加公司登记表
     * #return int code 操作码，0表示成功
     * #return int form_id 登记表ID
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
        $data = array(
            'company_id' => $this->companyId,
            'telephone' => $this->telephone,
            'legal_person' => $this->legalPerson,
            'mobile' => $this->mobile,
            'intermediary_fee' => json_encode($this->intermediaryFee),
            'teacher_number' => $this->teacherNumber,
            'staff_number' => $this->staffNumber,
            'business' => json_encode($this->business),
            'part_work_charge' => $this->partWorkCharge,
            'is_intermediary_fee' => json_encode($this->isIntermediaryFee),
            'is_cleaning' => $this->isCleaning,
            'charges' => $this->charges,
            'introduction' => $this->introduction,
            'remark' => $this->remark,
            'create_time' => time(),
            'last_modify' => time(),
        );
        $company_data = array(
            'company_name' => $this->companyName,
            'address' => $this->address,
            'zip_code' => $this->zipCode,
        );
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $companyInformationDomain = new Domain_Jiafubao_CompanyInformation();
            $id = $companyInformationDomain->add($data,$company_data,$companyInfo);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        $rs['info']['form_id'] = $id;

        return $rs;
    }

}
