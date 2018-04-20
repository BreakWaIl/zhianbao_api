<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Order_SendStaff_Add extends PhalApi_Api
{

    public function getRules()
    {
        return array(
            'Go' => array(
                'orderId' => array('name' => 'order_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '订单ID'),
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家政公司ID'),
                'jfbCompanyId' => array('name' => 'jfb_company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家政公司家服云ID'),
                'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家政员ID'),
            ),
        );
    }


    /**
     * 订单推荐家政员
     * #desc 用于订单推荐家政员
     * #return int code 操作码，0表示成功
     * #return int status  状态
     */
    public function Go()
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        //判断订单是否存在
        $orderDomain = new Domain_Jiafubao_Order();
        $orderInfo = $orderDomain->getBaseInfo($this->orderId);
        if (empty($orderInfo)) {
            $rs['code'] = 164;
            $rs['msg'] = T('Order not exists');
            return $rs;
        }
        if ($orderInfo['order_status'] != 'confirm' ||  $orderInfo['publish'] != 'y') {
            $rs['code'] = 200;
            $rs['msg'] = T('Order status is error');
            return $rs;
        }
        //判断公司是否存在
        $companyDomain = new Domain_Zhianbao_Company();
        $companyInfo = $companyDomain->getBaseInfo($this->companyId);
        if(! $companyInfo){
            $rs['code'] = 112;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }
        //获取发单公司信息
        $orderCompanyInfo = $companyDomain->getBaseInfo($orderInfo['company_id']);
        if(! $orderInfo) {
            $rs['code'] = 112;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }
        $jfbCompanyDomain = new Domain_Jiafubao_Company();
        $orderJfbCompanyInfo = $jfbCompanyDomain->getBaseInfo($orderCompanyInfo['id']);
        if(! $orderJfbCompanyInfo){
            $rs['code'] = 112;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }
        //判断家政员是否存在
        $staffDomain = new Domain_Jiafubao_CompanyHouseStaff();
        $staffInfo = $staffDomain->getBaseInfo($this->staffId);
        if (empty($staffInfo) || $staffInfo['company_id'] != $companyInfo['id']) {
            $rs['code'] = 126;
            $rs['msg'] = T('Staff not exists');
            return $rs;
        }

        if($this->companyId == $orderInfo['company_id']){
            $rs['code'] = 230;
            $rs['msg'] = T('Send staff fail');
            return $rs;
        }
        $data = array(
            'order_id' => $this->orderId,
            'order_bn' => $orderInfo['bn'],
            'company_id' => $orderJfbCompanyInfo['id'],
            'company_name' => $orderCompanyInfo['name'],
            'send_company_id' => $this->jfbCompanyId,
            'send_company_name' => $companyInfo['name'],
            'send_company_mobile' => $companyInfo['mobile'],
            'staff_id' => $this->staffId,
            'name' => $staffInfo['name'],
            'create_time' => time(),
            'last_modify' => time(),
        );
        $sendStaffDomain = new Domain_Jiafubao_SendStaff();
        try {
            DI()->notorm->beginTransaction('db_api');
            $sendRs = $sendStaffDomain->sendStaff($data,$staffInfo);
            DI()->notorm->commit('db_api');
        } catch (Exception $e) {
            DI()->notorm->rollback('db_api');
            $rs ['code'] = $e->getCode();
            $rs ['msg'] = $e->getMessage();
            return $rs;
        }

        if ($sendRs) {
            $rs['info']['status'] = 0;
        } else {
            $rs['info']['status'] = 1;
        }
        return $rs;
    }

}
