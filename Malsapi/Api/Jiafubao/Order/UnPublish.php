<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Order_UnPublish extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'orderId' => array('name' => 'order_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '订单ID'),
            ),
        );
    }


    /**
     * 撤回发布的需求
     * #desc 用于撤回已发布的需求
     * #return int code 操作码，0表示成功
     * #return int order_id  订单ID
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
        //判断订单是为平台订单
        if($orderInfo['is_jfy'] == 'y'){
            $rs['code'] = 227;
            $rs['msg'] = T('This order does not support posting to the market');
            return $rs;
        }

        if(! ($orderInfo['order_status'] == 'confirm' && $orderInfo['publish'] == 'y')){
            $rs['code'] = 182;
            $rs['msg'] = T('UnPublish failed');
            return $rs;
        }
        $publishRs = $orderDomain->unPublishOrder($orderInfo);
        $rs['info']['status'] = $publishRs;

        return $rs;
    }

}
