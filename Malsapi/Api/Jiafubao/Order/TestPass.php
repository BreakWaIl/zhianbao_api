<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Order_TestPass extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'orderId' => array('name' => 'order_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '订单ID'),
                'monthlyPayTime' => array('name' => 'monthly_pay_time', 'type' => 'int', 'min' => 0,'max' => 31, 'require' => true, 'desc' => '每月支付时间'),
                'workMonth' => array('name' => 'work_month', 'type' => 'int', 'min' => 0,'max' => 12, 'require' => true, 'desc' => '签订周期'),
                'amount' => array('name' => 'amount', 'type' => 'float',  'require' => true, 'desc' => '合同金额'),
                'customerIntermediaryFee' => array('name' => 'customer_intermediary_fee', 'type' => 'float', 'default' => 0.00, 'require' => true, 'desc' => '雇主中介费'),
                'staffIntermediaryFee' => array('name' => 'staff_intermediary_fee', 'type' => 'float', 'default' => 0.00, 'require' => true, 'desc' => '家政员中介费'),
                'manageFee' => array('name' => 'manage_fee', 'type' => 'float', 'default' => 0.00, 'require' => true, 'desc' => '管理费(每月)'),
                'restDay' => array('name' => 'rest_day', 'type' => 'string', 'require' => true, 'desc' => '休息天数(每周/每月)'),
                'mark' => array('name' => 'mark', 'type' => 'string', 'require' => false, 'desc' => '备注'),
                'attachment' => array('name' => 'attachment', 'type' => 'array','format' => 'json', 'require' => false, 'desc' => '附件'),
            ),
        );
    }


    /**
     * 订单家政员试工通过
     * #desc 用于订单家政员试工通过
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
       $data = array(
           'order_info' => $orderInfo,
           'monthly_pay_time' => $this->monthlyPayTime,
           'work_month' => $this->workMonth,
           'amount' => $this->amount,
           'customer_intermediary_fee' => $this->customerIntermediaryFee,
           'staff_intermediary_fee' => $this->staffIntermediaryFee,
           'manage_fee' => $this->manageFee,
           'rest_day' => $this->restDay,
       );
       if(isset($this->attachment)){
           $data['attachment'] = json_encode($this->attachment);
       }
        if(isset($this->mark)){
            $data['mark'] = $this->mark;
        }
        $publishRs = $orderDomain->testWorkPass($data);
        if($publishRs){
            $rs['info']['status'] = 0;
        }else{
            $rs['info']['status'] = 1;
        }
        return $rs;
    }

}
