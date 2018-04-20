<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_Customer_ShenPu_Update extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'customerId' => array('name' => 'customer_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '客户ID'),
                     'realName' => array('name' => 'name', 'type' => 'string', 'require' => false, 'desc' => '真实姓名'),
                     'sex' => array('name' => 'sex', 'type' => 'enum', 'range' => array ('boy', 'girl'),'default' => 'boy', 'require' => false, 'desc' => '性别'),
                     'birthDay' => array('name' => 'birthday', 'type' => 'string', 'require' => false, 'desc' => '生日'),
                     'province' => array('name' => 'province', 'type' => 'int', 'require' => false, 'desc' => '省份'),
                     'city' => array('name' => 'city', 'type' => 'string', 'int' => false, 'desc' => '城市'),
                     'district' => array('name' => 'district', 'type' => 'int', 'require' => false, 'desc' => '区县'),
                     'address' => array('name' => 'address', 'type' => 'string', 'require' => false, 'desc' => '地址'),
                     'remark' => array('name' => 'remark', 'type' => 'string', 'require' => false, 'desc' => '备注'),
            ),
        );
    }

    /**
     * 更新客户
     * #desc 用于更新客户信息
     * #return int code 操作码，0表示成功， 1表示添加失败
     * #return int customer_id 客户ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $data = array(
            'customer_id' => $this->customerId,
            'realname' => $this->realName,
            'sex' => $this->sex,
            'birthday' => strtotime($this->birthDay),
            'province' => $this->province,
            'city' => $this->city,
            'district'=> $this->district,
            'address' => $this->address,
            'remark' => $this->remark,
        );
        try {
            $companyCustomerDomain = new Domain_Jiafubao_CompanyCustomer();
            $info = $companyCustomerDomain->updateCustomer($data);

        } catch ( Exception $e ) {

            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        $rs['info'] = $info;

        return $rs;
    }
    
}
