<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_MiniSoft_Customer_WeChat_Check extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'customerId' => array('name' => 'customer_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '会员ID'),
                'openId' => array('name' => 'openid', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '微信openid'),
            ),
        );
    }


    /**
     * 微信免登授权检测
     * #desc 用于微信免登授权检测
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //查找用户
        $domainCustomer = new Domain_Jiafubao_Customer();
        $info = $domainCustomer->getBaseInfo($this->customerId);
        if (empty($info)) {
            $rs['code'] = 160;
            $rs['msg'] = T('Customer not exists');
            return $rs;
        }

        $data = array(
            'customer_id' => $this->customerId,
            'identify' => $this->openId,
            'source' => 'wechat',
        );
        $info = $domainCustomer->miniSoftWechatCheck($data);

        $rs['info'] = $info;

        return $rs;
    }

}

