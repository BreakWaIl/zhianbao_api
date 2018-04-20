<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_Customer_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'name' => array('name' => 'name', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '客户姓名'),
                'sex' => array('name' => 'sex', 'type'=>'enum','range' => array('boy','girl'), 'default' => 'girl', 'require'=> true,'desc'=> '性别'),
                'mobile' => array('name' => 'mobile', 'type'=>'string','max' => 11, 'min' => 11,  'require'=> true,'desc'=> '联系方式'),
                'province' => array('name' => 'province', 'type' => 'int', 'min'=> 1, 'require' => true, 'desc' => '省份'),
                'city' => array('name' => 'city', 'type' => 'int', 'min'=> 1, 'require' => true, 'desc' => '城市'),
                'district' => array('name' => 'district', 'type' => 'int', 'min'=> 1, 'require' => true, 'desc' => '区县'),
                'address' => array('name' => 'address', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '地址'),
            ),
        );
    }


    /**
     * 后台手动添加客户
     * #desc 用于后台手动添加客户
     * #return int code 操作码，0表示成功
     * #return int company_id  客户ID
     */
    public function Go() {
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
            'user_id' => $companyInfo['user_id'],
            'company_id' => $this->companyId,
            'name' => $this->name,
            'sex' => $this->sex,
            'mobile' => $this->mobile,
            'province' => $this->province,
            'city' => $this->city,
            'district' => $this->district,
            'address' => $this->address,
        );
        $companyCustomerDomain = new Domain_Jiafubao_CompanyCustomer();
        $rs = $companyCustomerDomain->offlineAdd($data);
        if( !$rs){
            $rs['code'] = 226;
            $rs['msg'] = T('Please improve the shop information first');
            return $rs;
        }

        if(isset($rs['code'])){
            return $rs;
        }else{
            $rs = array('code' => 0, 'msg' => '', 'info' => array( 'customer_id' => $rs));
        }

        return $rs;
    }

}
