<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Order_Work extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'require' => true, 'desc' => '公司ID'),
                'orderId' => array('name' => 'order_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '订单ID'),
                'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家政员ID'),
                'monthlyPayTime' => array('name' => 'monthly_pay_time', 'type' => 'int', 'min' => 0,'max' => 31, 'require' => true, 'desc' => '每月支付时间'),
                'workMonth' => array('name' => 'work_month', 'type' => 'int', 'min' => 0,'max' => 12, 'require' => true, 'desc' => '签订周期'),
                'amount' => array('name' => 'amount', 'type' => 'float',  'require' => true, 'desc' => '合同金额'),
                'customerIntermediaryFee' => array('name' => 'customer_intermediary_fee', 'type' => 'float', 'default' => 0.00, 'require' => true, 'desc' => '雇主中介费'),
                'staffIntermediaryFee' => array('name' => 'staff_intermediary_fee', 'type' => 'float', 'default' => 0.00, 'require' => true, 'desc' => '家政员中介费'),
                'manageFee' => array('name' => 'manage_fee', 'type' => 'float', 'default' => 0.00, 'require' => true, 'desc' => '管理费(每月)'),
                'restDay' => array('name' => 'rest_day', 'type' => 'string', 'require' => true, 'desc' => '休息天数(每周/每月)'),
                'mark' => array('name' => 'mark', 'type' => 'string', 'require' => false, 'desc' => '备注'),
                'attachment' => array('name' => 'attachment', 'type' => 'string', 'require' => false, 'desc' => '附件'),
            ),
        );
    }


    /**
     * 家政员直接上单
     * #desc 用于家政员直接上单
     * #return int code 操作码，0表示成功
     * #return int demand_id  需求ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断公司是否存在
        $domainCompany = new Domain_Zhianbao_Company();
        $companyInfo = $domainCompany->getBaseInfo($this->companyId);
        if (empty($companyInfo)) {
            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }
        //判断订单是否存在
        $orderDomain = new Domain_Jiafubao_Order();
        $orderInfo = $orderDomain->getBaseInfo($this->orderId);
        if (empty($orderInfo)) {
            $rs['code'] = 164;
            $rs['msg'] = T('Order not exists');
            return $rs;
        }
        //判断订单状态
        if($orderInfo['order_status'] != 'confirm' && $orderInfo['order_status'] != 'change'){
            $rs['code'] = 171;
            $rs['msg'] = T('Work Order failed');
            return $rs;
        }
        //判断家政员是否存在
        $staffDomain = new Domain_Jiafubao_CompanyHouseStaff();
        $staffInfo = $staffDomain->getBaseInfo($this->staffId);
        if (empty($staffInfo)) {
            $rs['code'] = 126;
            $rs['msg'] = T('Staff not exists');
            return $rs;
        }

        $data = array(
            'company_info' => $companyInfo,
            'order_info' => $orderInfo,
            'staff_info' => $staffInfo,
            'monthly_pay_time' => $this->monthlyPayTime,
            'work_month' => $this->workMonth,
            'amount' => $this->amount,
            'customer_intermediary_fee' => $this->customerIntermediaryFee,
            'staff_intermediary_fee' => $this->staffIntermediaryFee,
            'manage_fee' => $this->manageFee,
            'rest_day' => $this->restDay,
        );
        if(isset($this->attachment)){
            $data['attachment'] = $this->attachment;
        }
        if(isset($this->mark)){
            $data['mark'] = $this->mark;
        }
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $return = $orderDomain->workOrder($data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();

        }
        $rs['info']['status'] = $return;


        return $rs;
    }

}
