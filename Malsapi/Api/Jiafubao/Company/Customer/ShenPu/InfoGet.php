<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_Customer_ShenPu_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'customerId' => array('name' => 'customer_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '客户ID'),
            ),
        );
    }

    /**
     * 获取单个客户信息
     * #desc 用于获取当前客户信息
     * #return int code 操作码，0表示成功
     * #return int shop_id 店铺id
     * #return string login_name 客户名称
     * #return string realname 真实姓名
     * #return string mobile 电话号码
     * #return int customer_point 客户积分
     * #return string customer_advance 客户预存款
     * #return string source 客户来源
     * #return string sex 性别
     * #return string birthday 生日
     * #return string remark 备注
     * #return string country 国家
     * #return string province 省份
     * #return string city 城市
     * #return string district 区县
     * #return string address 地址
     * #return int create_time 注册时间
     * #return int last_modify 最后更新时间
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        try {
            $companyCustomerDomain = new Domain_Jiafubao_CompanyCustomer();
            $customerInfo = $companyCustomerDomain->getBaseInfo($this->customerId);
            if (!$customerInfo) {
                $rs['code'] = 160;
                $rs['msg'] = T('Customer not exists');
                return $rs;
            }
        } catch ( Exception $e ) {

            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }
        if($customerInfo['birthday'] > 0){
            $customerInfo['birthday'] = date("Y-m-d H:i:s", $customerInfo['birthday']);
        }
        $rs['info'] = $customerInfo;

        return $rs;
    }
    
}
