<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Order_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'orderId' => array('name' => 'order_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '订单ID'),
                     'source' => array('name' => 'source', 'type' => 'enum', 'range' => array('jfb','shenpu'), 'default' => 'jfb', 'require' => true, 'desc' => '获取来源'),
                 ),
        );
    }
  
  /**
     * 获取单个订单信息
     * #desc 用于获取当前订单信息
     * #return int code 操作码，0表示成功
     * #return int order_status 订单状态，work:工作中,active:活动中,cancel:已撤销,close:已关闭,finish:已完成
  */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $orderDomain = new Domain_Jiafubao_Order();
        $orderInfo = $orderDomain->getBaseInfo($this->orderId);
        if (empty($orderInfo)) {
            $rs['code'] = 164;
            $rs['msg'] = T('Order not exists');
            return $rs;
        }
        $orderInfo['pay_time'] =  $orderInfo['pay_time'] == 0 ? '-': date('Y-m-d H:i:s',$orderInfo['pay_time']);
        $orderInfo['create_time'] = date('Y-m-d H:i:s',$orderInfo['create_time']);
        $orderInfo['last_modify'] = date('Y-m-d H:i:s',$orderInfo['last_modify']);
        //判断获取来源
        if($this->source == 'jfb'){
            //判断是否为商品订单
            if($orderInfo['goods_order_id'] > 0){
                $goodsOrderInfo = $orderDomain->spOrderInfo($orderInfo['goods_order_id']);
                $orderInfo['goods_order_info'] = $goodsOrderInfo;
            }else{
                $orderInfo['goods_order_info'] = array();
            }
        }

        $rs['info'] = $orderInfo;

        return $rs;
    }
    
}
