<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Order_TestClose extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'orderId' => array('name' => 'order_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '订单ID'),
                'ifChange' => array('name' => 'if_change', 'type' => 'int',  'require' => false, 'desc' => '是否换工'),
                'amount' => array('name' => 'amount', 'type' => 'float',  'require' => true,'default' => 0.00, 'desc' => '支付金额'),
                'mark' => array('name' => 'mark', 'type' => 'string', 'require' => false, 'desc' => '备注'),
            ),
        );
    }


    /**
     * 订单家政员试工不通过
     * #desc 用于订单家政员试工不通过
     * #return int code 操作码，0表示成功
     * #return int demand_id  需求ID
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
       if($orderInfo['order_status'] != 'test'){
           $rs['code'] = 200;
           $rs['msg'] = T('Order status is error');
           return $rs;
       }
//        if(isset($this->ifChange)){
//            //判断家政员是否存在
//            $staffDomain = new Domain_Jiafubao_CompanyHouseStaff();
//            $staffInfo = $staffDomain->getBaseInfo($this->staffId);
//            if (empty($staffInfo)) {
//                $rs['code'] = 126;
//                $rs['msg'] = T('Staff not exists');
//                return $rs;
//            }
//            $staffId = $this->staffId;
//        }else{
//            $staffId = 0;
//        }
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $closeRs = $orderDomain->testWorkClose($this->orderId,$this->amount,$this->ifChange,$this->mark);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();

        }
        if($closeRs){
            $rs['info']['status'] = 0;
        }else{
            $rs['info']['status'] = 1;
        }
        return $rs;
    }

}
