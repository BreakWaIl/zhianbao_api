<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Customer_Logout extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'customerId' => array('name' => 'customer_id', 'type' => 'int', 'require' => true, 'desc' => '客户ID'),
            ),
        );
    }


    /**
     * 客户退出
     * #desc 用于客户退出登录
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $domain = new Domain_Jiafubao_Customer();
        $info = $domain->logout($this->customerId);
        $rs['info'] = $info;

        return $rs;
    }

}

