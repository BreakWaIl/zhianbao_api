<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Order_UnAccept extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'orderId' => array('name' => 'order_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '订单ID'),
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '接单公司ID'),
            ),
        );
    }


    /**
     * 企业取消市场接单
     * #desc 用于企业取消在需求库中接单
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


        if($orderInfo['publish'] != 'order'){
            $rs['code'] = 182;
            $rs['msg'] = T('UnPublish failed');
            return $rs;
        }
        //发布公司与接单公司不能为同一家
        if($orderInfo['accept_company_id'] != $this->companyId){
            $rs['code'] = 182;
            $rs['msg'] = T('UnPublish failed');
            return $rs;
        }
        $data = array(
            'company_id' => $this->companyId,
            'order_info' => $orderInfo,
        );

        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $orderId = $orderDomain->unAcceptOrder($data);
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
