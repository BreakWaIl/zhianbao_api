<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Order_Pay extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'orderId' => array('name' => 'order_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '订单ID'),
                'money' => array('name' => 'money', 'type' => 'float',  'require' => true, 'desc' => '金额'),
            ),
        );
    }


    /**
     * 企业设置订单支付成功
     * #desc 用于企业将订单更新为已支付
     * #return int code 操作码，0表示成功
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        //判断订单是否存在
        $orderDomain = new Domain_Jiafubao_Order();
        $orderInfo = $orderDomain->getBaseInfo($this->orderId);
        if (empty($orderInfo)) {
            $rs['code'] = 164;
            $rs['msg'] = T('Order not exists');
            return $rs;
        }

        $status = $orderDomain->payOrder($this->orderId,$this->money);
        $rs['info']['status'] = $status;

        return $rs;
    }

}
