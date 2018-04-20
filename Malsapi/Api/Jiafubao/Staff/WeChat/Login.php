<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Staff_WeChat_Login extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
//                'shopId' => array('name' => 'shop_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '店铺ID'),
            ),
        );
    }


    /**
     * 微信家政员登录
     * #desc 用于微信家政员免登录
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $domain = new Domain_Jiafubao_CompanyHouseStaff();
        $info = $domain->wechatLogin(array());

        if (empty($info)) {
            $rs['code'] = 126;
            $rs['msg'] = T('Staff not exists');
            return $rs;
        }

 
        $rs['session'] = $info;

        return $rs;
    }

}

