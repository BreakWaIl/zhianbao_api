<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Order_Confirm extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'orderId' => array('name' => 'order_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '订单ID'),
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '接单公司ID'),
            ),
        );
    }


    /**
     * 订单确认
     * #desc 用于家政员订单确认
     * #return int code 操作码，0表示成功
     * #return int status  状态码
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
        if($orderInfo['accept_company_id'] != $this->companyId){
            $rs['code'] = 148;
            $rs['msg'] = T('Get failed');
            return $rs;
        }
        //判断订单状态
        if($orderInfo['order_status'] != 'wait'){
            $rs['code'] = 200;
            $rs['msg'] = T('Order status is error');
            return $rs;
        }

        $status = $orderDomain->comfirmOrder($orderInfo);
        if($status){
            $rs['info']['status'] = 0;
        }else{
            $rs['info']['status'] = 1;
        }


        return $rs;
    }

}
