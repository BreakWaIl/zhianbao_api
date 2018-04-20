<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Order_Mark extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(

            ),
        );
    }


    /**
     * 订单跟踪标记
     * #desc 用于订单跟踪标记
     * #return int code 操作码，0表示成功
     * #return int demand_id  需求ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $orderDomain = new Domain_Jiafubao_Order();
        $orderDomain->markOrder();
        return $rs;
    }

}
