<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_Company_Update extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'companyName' => array('name' => 'company_name', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '名称'),
//                'country' => array('name' => 'country', 'type' => 'int', 'min' => 0, 'require' => true, 'desc' => '国家'),
//                'province' => array('name' => 'province', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '省份'),
//                'city' => array('name' => 'city', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '城市'),
//                'district' => array('name' => 'district', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '区县'),
                'address' => array('name' => 'address', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '详细地址'),
                'legalPerson' => array('name' => 'legal_person', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '法人代表'),
                'telephone' => array('name' => 'telephone', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '联系电话'),
                'zipCode' => array('name' => 'zip_code', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '邮编'),
            ),
        );
    }


    /**
     * 更新公司基本信息
     * #desc 用于添加公司基本信息
     * #return int code 操作码，0表示成功
     * #return int company_id  公司ID
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
            'company_name' => $this->companyName,
//            'country' => '1',
//            'province' => $this->province,
//            'city' => $this->city,
//            'district' => $this->district,
            'address' => $this->address,
            'legal_person' => $this->legalPerson,
            'telephone' => $this->telephone,
            'zip_code' => $this->zipCode,
        );

        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $companyDomain = new Domain_Jiafubao_Company();
            $companyDomain->update($data,$companyInfo);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }


        $rs['info']['company_id'] = $this->companyId;

        return $rs;
    }

}
