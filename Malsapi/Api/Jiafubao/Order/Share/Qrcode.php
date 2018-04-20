<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Order_Share_Qrcode extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'orderId' => array('name' => 'order_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '订单ID'),
                'shareUserId' => array('name' => 'share_user_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '分享用户ID'),
            ),
        );
    }


    /**
     * 生成二维码
     * #desc 用于生成二维码
     * #return int code 操作码，0表示成功
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
        $qrCode = $orderDomain->qrcode($this->orderId,$this->shareUserId);

        $rs['info']['qrcode'] = $qrCode;

        return $rs;
    }

}
