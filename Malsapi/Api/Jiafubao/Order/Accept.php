<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Order_Accept extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'orderId' => array('name' => 'order_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '订单ID'),
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '接单公司ID'),
            ),
        );
    }


    /**
     * 企业接单
     * #desc 用于企业在需求库中接单
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


        if($orderInfo['order_status'] != 'wait'){
            $rs['code'] = 162;
            $rs['msg'] = T('Demand  is already order');
            return $rs;
        }
        //发布公司与接单公司不能为同一家
        if($orderInfo['company_id'] == $this->companyId){
            $rs['code'] = 183;
            $rs['msg'] = T('Can not accept yourself demand');
            return $rs;
        }
        $data = array(
            'company_id' => $this->companyId,
            'order_info' => $orderInfo,
        );
        //判断是否为平台订单
        if($orderInfo['is_jfy'] == 'y'){
            //检测客户是否已存在
            $customerId = $orderDomain->checkCustomer($orderInfo,$companyInfo);
            if( !$customerId){
                $rs['code'] = 160;
                $rs['msg'] = T('Customer not exists');
                return $rs;
            }
        }

        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $orderId = $orderDomain->acceptOrder($data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        $rs['info']['order_id'] = $orderId;

        return $rs;
    }

}
