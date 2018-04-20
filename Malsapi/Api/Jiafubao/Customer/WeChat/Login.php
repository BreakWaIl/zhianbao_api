<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Customer_WeChat_Login extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
//                'shopId' => array('name' => 'shop_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '店铺ID'),
            ),
        );
    }


    /**
     * 微信客户登录
     * #desc 用于微信客户免登录
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $domain = new Domain_Jiafubao_Customer();
        $info = $domain->wechatLogin(array());

        if (empty($info)) {
            $rs['code'] = 160;
            $rs['msg'] = T('Customer not exists');
            return $rs;
        }

 
        $rs['session'] = $info;

        return $rs;
    }

}

