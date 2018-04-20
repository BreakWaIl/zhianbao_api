<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Order_Share_Share extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'orderId' => array('name' => 'order_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '订单ID'),
            ),
        );
    }


    /**
     * 订单分享
     * #desc 用于完成分享
     * #return int code 操作码，0表示成功
     * #return array info 订单信息
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        //获取订单信息
        $orderDomain = new Domain_Jiafubao_Order();
        $orderInfo = $orderDomain->share($this->orderId);
        if (! $orderInfo) {
            $rs['code'] = 164;
            $rs['msg'] = T('Order not exists');
            return $rs;
        }

        $rs['info'] = $orderInfo;

        return $rs;
    }

}
