<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Order_Supplement_Update extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'orderId' => array('name' => 'order_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '订单ID'),
                'supplementMarkText' => array('name' => 'supplement_mark_text', 'type' => 'string', 'min' => 1, 'require' => true, 'default' => '', 'desc' => '补充说明'),
            ),
        );
    }


    /**
     * 更新订单信息补充说明
     * #desc 用于更新订单信息补充说明
     * #return int code 操作码，0表示成功
     * #return int order_id  订单ID
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
        //判断订单状态（待确认、待派单）
        if(($orderInfo['order_status'] != 'wait' && $orderInfo['order_status'] != 'confirm') ){
            $rs['code'] = 137;
            $rs['msg'] = T('Being used');
            return $rs;
        }

        $data = array(
            'order_info' => $orderInfo,
            'supplement_mark_text' => $this->supplementMarkText,
        );

        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $orderId = $orderDomain->updateSupplementOrder($data);
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
