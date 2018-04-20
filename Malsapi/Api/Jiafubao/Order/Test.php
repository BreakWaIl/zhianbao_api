<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Order_Test extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'orderId' => array('name' => 'order_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '订单ID'),
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'require' => true, 'desc' => '公司ID'),
                'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家政员ID'),
                'testTime' => array('name' => 'test_time', 'type'=>'string', 'min' => 1, 'require'=> true, 'desc' => '试工时间'),
            ),
        );
    }


    /**
     * 订单分派家政员试工
     * #desc 用于为订单分派家政员试工
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
        if($orderInfo['order_status'] != 'confirm' && $orderInfo['order_status'] != 'change'){
            $rs['code'] = 200;
            $rs['msg'] = T('Order status is error');
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
            'testTime' => $this->testTime,
        );
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $publishRs = $orderDomain->testWork($data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }
        if($publishRs){
            $rs['info']['status'] = 0;
        }else{
            $rs['info']['status'] = 1;
        }
        return $rs;
    }

}
