<?php
class Domain_Jiafubao_Order {
	var $model;

	public function __construct() {
		$this->model = new Model_Jiafubao_Order();
	}

	//获取详情
    public function getBaseInfo($id, $cols = '*'){
        $companyModel = new Model_Zhianbao_Company();
        $customerModel = new Model_Jiafubao_Customer();
        $appraiseModel = new Model_Jiafubao_OrderAppraise();
        $orderLogModel = new Model_Jiafubao_OrderLog();
        $rs = $this->model->get ( $id , $cols);
        if(! $rs){
            return false;
        }
        //判断是否为平台订单
        if($rs['is_jfy'] == 'n'){
            $rs['company_info'] = $companyModel->get($rs['company_id']);
            $companyCustomerDomain = new Domain_Jiafubao_CompanyCustomer();
            $rs['customer_info'] = $companyCustomerDomain->getBaseInfo($rs['customer_id']);
        }else{
            $rs['accept_company_info'] = $companyModel->get($rs['accept_company_id']);
            $customer = $this->getCustomerInfo($rs['bbc_customer_id']);
            $rs['customer_info'] = $customer;
        }
        //拼接服务地址
        $rs['country_id'] = $rs['country'];
        $rs['province_id'] = $rs['province'];
        $rs['city_id'] = $rs['city'];
        $rs['district_id'] = $rs['district'];
        $domainArea = new Domain_Area();
        $country = $domainArea->getAreaNameById($rs['country']);
        $province = $domainArea->getAreaNameById($rs['province']);
        $city = $domainArea->getAreaNameById($rs['city']);
        $district = $domainArea->getAreaNameById($rs['district']);
        $rs['address_details'] = $country.$province.$city.$district.$rs['address'];
        $rs['country'] = $country;
        $rs['province'] = $province;
        $rs['city'] = $city;
        $rs['district'] = $district;
        //获取评价
        $customerAppraiseFilter = array(
            'order_id' => $id,
            'type' => 'customer'
        );
        $rs['customer_appraise_list'] = $appraiseModel->getAll('*',$customerAppraiseFilter);
        $staffAppraiseFilter = array(
            'order_id' => $id,
            'type' => 'staff'
        );
        $rs['staff_appraise_list'] = $appraiseModel->getAll('*',$staffAppraiseFilter);
        //获取订单操作记录
        $orderlogList = $orderLogModel->getAll('*',array('order_id' => $id));
        $rs['order_log'] = $orderlogList;
        //获取合同
        $contractModel = new Model_Jiafubao_OrderStaffContract();
        $filter = array('order_id' => $id);
        $contractList = $contractModel->getAll('*',$filter);
        $rs['contract_list'] = $contractList;
        //获取订单出入账信息
        $orderPayLogModel = new Model_Jiafubao_OrderPayLog();
        $filter = array('order_id' => $id);
        $orderPayList = $orderPayLogModel -> getAll('*',$filter);
        $rs['order_pay_list'] = $orderPayList;

        //判断是否为平台订单
        if($rs['is_jfy'] == 'y'){
            //获取优惠券使用情况
            $coupon = $this->getCouponInfo($rs['id'],$rs['bbc_customer_id']);
            if(!empty($coupon)){
                $rs['coupon_info'] = $coupon;
            }else{
                $rs['coupon_info'] = false;
            }
        }else{
            $rs['coupon_info'] = false;
        }

        //获取订单推荐记录
        $sendModel = new Model_Jiafubao_OrderSend();
        $sendFilter = array('order_id' =>$id,'status' => 'active');
        $sendList = $sendModel->getAll('*',$sendFilter);
        $rs['send_list'] = $sendList;
        return $rs;
    }
    //获取列表
	public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
        $companyModel = new Model_Zhianbao_Company();
        $customerModel = new Model_Jiafubao_Customer();
//        $demandModel = new Model_Jiafubao_Demand();
        $domainArea = new Domain_Area();
        $appraiseModel = new Model_Jiafubao_OrderAppraise();
		$rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        foreach ( $rs as $key => $value){
            if($value['company_id'] > 0) {
                $rs[$key]['company_info'] = $companyModel->get($value['company_id']);
            }else{
                $rs[$key]['company_info'] = array();
            }
            if($value['accept_company_id'] > 0) {
                $rs[$key]['accept_company_info'] = $companyModel->get($value['accept_company_id']);
            }else{
                $rs[$key]['company_info'] = array();
            }
            $customerInfo = $customerModel->get($value['customer_id']);
            $rs[$key]['customer_info'] = $customerInfo;
//            $demandInfo = $demandModel->get($value['demand_id']);
            $country = $domainArea->getAreaNameById($value['country']);
            $province = $domainArea->getAreaNameById($value['province']);
            $city = $domainArea->getAreaNameById($value['city']);
            $district = $domainArea->getAreaNameById($value['district']);
            $value['address_details'] = $country.$province.$city.$district.$value['address'];
            $rs[$key]['address_details'] = $value['address_details'];
//            $rs[$key]['demand_info'] = $demandInfo;

            //查找评价
            $appraiseFilter = array('order_id' => $value['id'],'type' => 'customer');
            $appraiseList = $appraiseModel->getByWhere($appraiseFilter);
            if($appraiseList){
                $rs[$key]['customer_can_appraise'] = false;
            }else{
                $rs[$key]['customer_can_appraise'] = true;
            }
        }
		return $rs;
	}
	//获取数量
	public function getCount($filter) {
		return $this->model->getCount ( $filter );
	}
//	//取消订单
//    public function cancelOrder($data){
//	    $demandModel = new Model_Jiafubao_Demand();
//	    $companyModel = new Model_Zhianbao_Company();
//	    $orderLogDomain = new Domain_Jiafubao_OrderLog();
//	    $demandLogDomain = new Domain_Jiafubao_DemandLog();
//	    $companyInfo = $companyModel->get($data['company_id']);
//        $orderInfo = $data['order_info'];
//        //恢复需求为待接单
//        $demandData = array(
//            'status' => 'wait',
//            'last_modify' => time(),
//        );
//        $demandRs = $demandModel->update($orderInfo['demand_id'],$demandData);
//        if(! $demandRs){
//            throw new LogicException ( T ( 'Cancel order failed' ), 165 );
//        }
//        //添加需求日志
//        $demandLogRs = $demandLogDomain->addLog($orderInfo['demand_id'],$companyInfo['name'].'撤销了接单');
//        if(! $demandLogRs){
//            throw new LogicException ( T ( 'Cancel order failed' ), 165 );
//        }
//        //更新订单为取消状态
//        $orderData = array(
//            'order_status' => 'cancel',
//            'last_modify' => time(),
//        );
//        $orderRs = $this->model->update($orderInfo['id'],$orderData);
//        if(! $orderRs){
//            throw new LogicException ( T ( 'Cancel order failed' ), 165 );
//        }
//        //添加订单日志
//        $orderLogRs = $orderLogDomain->addLog($orderInfo['id'],$companyInfo['name'].'撤销了接单');
//        if(! $orderLogRs){
//            throw new LogicException ( T ( 'Cancel order failed' ), 165 );
//        }
//        return true;
//    }

    //订单支付成功
    public function payOrder($orderId,$money){
        $orderLogDomain = new Domain_Jiafubao_OrderLog();
        //更新订单状态为支付成功
        $data = array(
            'pay_status' => 1,
            'pay_time' => time(),
            'payment_code' => 'none',
            'last_modify' => time(),
        );
        $rs = $this->model->update($orderId,$data);
        //添加支付成功日志
        $orderLogRs = $orderLogDomain->addLog($orderId,'商家操作,订单支付成功');
        if(! $orderLogRs){
            throw new LogicException ( T ( 'Cancel order failed' ), 165 );
        }

        //添加订单支付记录
        $payLogModel = new Model_Jiafubao_OrderPayLog();
        $data = array(
            'order_id' => $orderId,
            'amount' => $money,
            'reason' => '订单支付',
            'create_time' => time(),
        );
        $payLogId = $payLogModel->insert($data);
        if(! $payLogId){
            throw new LogicException ( T ( 'Cancel order failed' ), 165 );
        }
        return $rs;
    }
    //订单完成
    public function finishOrder($data){
        $companyModel = new Model_Zhianbao_Company();
        $orderLogDomain = new Domain_Jiafubao_OrderLog();
        $orderStaffModel = new Model_Jiafubao_OrderStaff();
        $contractModel = new Model_Jiafubao_OrderStaffContract();
        $companyInfo = $companyModel->get($data['company_id']);
        $orderInfo = $data['order_info'];
        //更新订单为完成状态
        $orderData = array(
            'order_status' => 'finish',
            'last_modify' => time(),
        );
        $orderRs = $this->model->update($orderInfo['id'],$orderData);
        if(! $orderRs){
            throw new LogicException ( T ( 'Cancel order failed' ), 165 );
        }
        //添加订单日志
        $orderLogRs = $orderLogDomain->addLog($orderInfo['id'],$companyInfo['name'].'完成了接单');
        if(! $orderLogRs){
            throw new LogicException ( T ( 'Cancel order failed' ), 165 );
        }

        //更新订单阿姨为完工
        $filter = array(
            'order_id' => $orderInfo['id'],
            'staff_id' => $orderInfo['staff_id'],
        );
        $orderStaff = array(
            'status' => 'finish',
            'last_modify' => time(),
        );
        $rs = $orderStaffModel->updateByWhere($filter,$orderStaff);
        if(! $rs){
            throw new LogicException ( T ( 'Cancel order failed' ), 165 );
        }
        //更新合同为完成
        $rs = $contractModel->updateByWhere($filter,$orderStaff);
        if(! $rs){
            throw new LogicException ( T ( 'Cancel order failed' ), 165 );
        }
        return true;
    }
    //订单直接上单
    public function workOrder($data){
        $orderId = $data['order_info']['id'];
        //更新订单状态
        $orderData = array(
            'staff_id' => $data['staff_info']['id'],
            'total_amount' => $data['amount'],
            'order_status' => 'work',
            'last_modify' => time(),
        );
        if($data['staff_info']['company_id'] != $data['company_info']['id']){
            //选择的家政员不是自己公司的家政员
            $orderData['publish'] = 'n';
            $orderData['accept_company_id'] = $data['company_info']['id'];
            //生成合作单
            $partnerOrderId = $this->makePartnerOrder($data);
            if(! $partnerOrderId){
                throw new LogicException ( T ( 'Work Order failed' ), 171 );
            }
        }
        if($data['order_info']['order_status'] == 'change'){
            $updateData['change_time'] = new NotORM_Literal('change_time + 1 ');
        }
        $orderRs = $this->model->update($orderId,$orderData);
        if(! $orderRs){
            throw new LogicException ( T ( 'Work Order failed' ), 171 );
        }
        $orderStaffModel = new Model_Jiafubao_OrderStaff();
        //添加上单阿姨信息
        $staffData = array(
            'order_id' => $orderId,
            'staff_id' => $data['staff_info']['id'],
            'staff_name' => $data['staff_info']['name'],
            'status' => 'work',
            'create_time' => time(),
        );
        $orderStaffId = $orderStaffModel->insert($staffData);
        if(! $orderStaffId){
            throw new LogicException ( T ( 'Work Order failed' ), 171 );
        }
        //添加订单日志
        $orderLogDomain = new Domain_Jiafubao_OrderLog();
        $orderLogDomain ->addLog($orderId,'家政员'.$data['staff_info']['name'].'直接上单.');
        //添加合同
        $contractModel = new Model_Jiafubao_OrderStaffContract();
        $contractData = array(
            'order_id' => $orderId,
            'company_id' => $data['order_info']['company_id'],
            'staff_id' => $data['staff_info']['id'],
            'staff_name' => $data['staff_info']['name'],
            'monthly_pay_time' => $data['monthly_pay_time'],
            'work_month' => $data['work_month'],
            'rest_day' => $data['rest_day'],
            'amount' => $data['amount'],
            'customer_intermediary_fee' => $data['customer_intermediary_fee'],
            'staff_intermediary_fee' => $data['staff_intermediary_fee'],
            'manage_fee' => $data['manage_fee'],
            'work_content' => $data['order_info']['content'],
            'create_time' => time()
        );
        if(isset($data['mark'])){
            $contractData['mark'] = $data['mark'];
        }
        if(isset($data['attachment'])){
            $contractData['attachment'] = $data['attachment'];
        }
        $contractId = $contractModel->insert($contractData);
        if(! $contractId){
            throw new LogicException ( T ( 'Work Order failed' ), 171 );
        }
        return true;
    }
    //订单终止
    public function closeOrder($data){
        $orderLogDomain = new Domain_Jiafubao_OrderLog();
        $orderInfo = $data['order_info'];
        $orderId = $orderInfo['id'];
        //更新订单为终止状态
        $orderData = array(
            'order_status' => 'close',
            'last_modify' => time(),
        );
        $orderRs = $this->model->update($orderInfo['id'],$orderData);
        if(! $orderRs){
            throw new LogicException ( T ( 'Cancel order failed' ), 165 );
        }
        //添加订单日志
        $orderLogRs = $orderLogDomain->addLog($orderInfo['id'],'家政公司终止订单');
        if(! $orderLogRs){
            throw new LogicException ( T ( 'Cancel order failed' ), 165 );
        }

        //更新订单阿姨为订单终止
        $orderStaffModel = new Model_Jiafubao_OrderStaff();
        $filter = array(
            'order_id' => $orderId,
            'staff_id' => $orderInfo['staff_id'],
        );
        $orderStaffData = array(
            'status' => 'close',
            'last_modify' => time(),
        );
        $updateRs = $orderStaffModel->updateByWhere($filter,$orderStaffData);
        if(! $updateRs){
            throw new LogicException ( T ( 'Cancel order failed' ), 165 );
        }
        //处理合同
        $contractModel = new Model_Jiafubao_OrderStaffContract();
        $filter = array(
            'order_id' => $orderId,
            'staff_id' => $data['order_info']['staff_id'],
        );
        $contractData = array(
            'status' => 'close',
            'last_modify' => time(),
        );
        $contractRs = $contractModel->updateByWhere($filter,$contractData);
        if(! $contractRs){
            throw new LogicException ( T ( 'Cancel order failed' ), 165 );
        }

        return true;
    }

    //订单取消
    public function cancelOrder($data){
        $orderLogDomain = new Domain_Jiafubao_OrderLog();
        $orderInfo = $data['order_info'];
        //更新订单为取消状态
        $orderData = array(
            'order_status' => 'cancel',
            'last_modify' => time(),
        );
        $orderRs = $this->model->update($orderInfo['id'],$orderData);
        if(! $orderRs){
            throw new LogicException ( T ( 'Cancel order failed' ), 165 );
        }
        //添加订单日志
        $orderLogRs = $orderLogDomain->addLog($orderInfo['id'],'取消订单');
        if(! $orderLogRs){
            throw new LogicException ( T ( 'Cancel order failed' ), 165 );
        }
        return true;
    }



    public function getAllCustomer($filter,$mobile, $page = 1, $page_size = 20, $orderby = ''){
        $rs = array();
        $total = 0;
        $list = $this->model->getAll( '*', $filter);
        $customer = array();
        foreach ($list as $key=>$value){
            $customer[] = $value['customer_id'];
        }
        if(!empty($customer)){
            $new_list = array_values(array_unique($customer));
            if(!empty($new_list)){
                $customerModel = new Model_Jiafubao_Customer();
                $customer_filter = array('id' => $new_list);
                if(!empty($mobile)){
                    $customer_filter['mobile'] = $mobile;
                }
                $rs = $customerModel->getAll( '*', $customer_filter, $page, $page_size, $orderby );
                foreach ($rs as $key=>$value){
                    $rs[$key]['create_time'] = strtotime($value['create_time']) == 0 ? '-': $value['create_time'];
                    $rs[$key]['last_modify'] = strtotime($value['last_modify']) == 0 ? '-': $value['last_modify'];
                    unset($rs[$key]['login_pwd']);
                    unset($rs[$key]['salt']);
                }
                $total = COUNT($rs);
            }
        }
        $rs['total'] = $total;
        return $rs;
    }

    //生成订单
    public function addOrder($data){
        $serviceType = $data['service_type'];
        switch($serviceType){
            case 'depthClean' : $data['server_img'] = 'http://img.mshenpu.com/15145193829999.png ' ;break;
            case 'tempClean' : $data['server_img'] = 'http://img.mshenpu.com/15124463705185.png ' ;break;
            case 'quickClean' : $data['server_img'] = 'http://img.mshenpu.com/15124463705185.png ' ;break;
            case 'longHours' : $data['server_img'] = 'http://img.mshenpu.com/15124457735864.png ' ;break;
            case 'nanny' : $data['server_img'] = 'http://img.mshenpu.com/15124457735864.png ' ;break;
            case 'careElderly' : $data['server_img'] = 'http://img.mshenpu.com/15124457734370.png ' ;break;
            case 'careBaby' : $data['server_img'] = 'http://img.mshenpu.com/15124457737535.png ' ;break;
            case 'matron' : $data['server_img'] = 'http://img.mshenpu.com/15124457733527.png ' ;break;
        }
        $data ['bn'] = $this->gen_id();
        $orderId = $this->model->insert($data);
        if(! $orderId){
            throw new LogicException ( T ( 'Cancel order failed' ), 165 );
        }
        //添加订单日志
        $orderLogDomain = new Domain_Jiafubao_OrderLog();
        $orderLogDomain ->addLog($orderId,'并生成订单:'.$data ['bn']);
        return $orderId;
    }

    //生成订单编号
    function gen_id() {
        $orderModel = new Model_Jiafubao_Order();
        $i = rand ( 0, 99999 );
        do {
            if (99999 == $i) {
                $i = 0;
            }
            $i ++;
            $return_bn = 'O'.date ( 'ymdHi' ) . str_pad ( $i, 5, '0', STR_PAD_LEFT );
            $row = $orderModel->getByWhere ( array (
                'bn' => $return_bn
            ), 'id' );
        } while ( $row );
        return $return_bn;
    }
    //订单确认
    public function comfirmOrder($orderInfo){
        $data = array(
           // 'accept_company_id' => $companyId,
            'order_status' => 'confirm',
            'last_modify' => time()
        );
        $rs = $this->model->update($orderInfo['id'],$data);
        if($rs){
            //添加订单日志
            $orderLogDomain = new Domain_Jiafubao_OrderLog();
            $orderLogDomain ->addLog($orderInfo['id'],'确认订单');
            return true;
        }else{
            return false;
        }
    }
//    //指派家政员
//    public function assignStaff($data){
//        $orderId = $data['order_info']['id'];
//        $updateData = array(
//            'staff_id' => $data['staff_info']['id'],
//            'total_amount' => $data['price'],
//            'order_status' => 'assign',
//            'last_modify' => time(),
//        );
//        $rs = $this->model->update($orderId,$updateData);
//        if($rs){
//            //添加订单日志
//            $orderLogDomain = new Domain_Jiafubao_OrderLog();
//            $orderLogDomain ->addLog($orderId,'指派阿姨'.$data['staff_info']['name'].'成功');
//        }else{
//            return false;
//        }
//        return true;
//    }
    //发布订单到市场
    public function publishOrder($orderId){
        $updateData = array(
            'accept_company_id' => 0,
            'order_status' => 'confirm',
            'publish' => 'y',
            'last_modify' => time(),
        );
        $rs = $this->model->update($orderId,$updateData);
        if($rs){
            //添加订单日志
            $orderLogDomain = new Domain_Jiafubao_OrderLog();
            $orderLogDomain ->addLog($orderId,'订单发布到市场');
        }else{
            return false;
        }
        return true;
    }
    //撤回发布到市场的订单
    public function unPublishOrder($orderInfo){
        $orderId = $orderInfo['id'];
        $updateData = array(
            'accept_company_id' =>$orderInfo['company_id'],
            'publish' => 'n',
            'last_modify' => time(),
        );
        $rs = $this->model->update($orderId,$updateData);
        if($rs){
            //添加订单日志
            $orderLogDomain = new Domain_Jiafubao_OrderLog();
            $orderLogDomain ->addLog($orderId,'从市场撤回订单');
        }else{
            return false;
        }
        return true;
    }
    //接受订单
    public function acceptOrder($data){
        $orderId = $data['order_info']['id'];
        $updateData = array(
            'accept_company_id' => $data['company_id'],
            'order_status' => 'confirm',
            'publish' => 'order',
            'last_modify' => time(),
        );
        $rs = $this->model->update($orderId,$updateData);
        if($rs){
            //添加订单日志
            $orderLogDomain = new Domain_Jiafubao_OrderLog();
            $orderLogDomain ->addLog($orderId,'从市场接单');
        }else{
            return false;
        }
        return true;
    }
    //取消接单
    public function unAcceptOrder($data){
        $orderId = $data['order_info']['id'];
        $updateData = array(
            'accept_company_id' => 0,
            'publish' => 'y',
            'last_modify' => time(),
        );
        $rs = $this->model->update($orderId,$updateData);
        if($rs){
            //添加订单日志
            $orderLogDomain = new Domain_Jiafubao_OrderLog();
            $orderLogDomain ->addLog($orderId,'取消从市场接单');
        }else{
            return false;
        }
        return true;
    }

    //订单试工
    public function testWork($data){
        //试工
        $orderId = $data['order_info']['id'];
        $updateData = array(
            'staff_id' => $data['staff_info']['id'],
           // 'total_amount' => $data['price'],
            'order_status' => 'test',
            'last_modify' => time(),
        );
        if($data['staff_info']['company_id'] != $data['company_info']['id']){
            //选择的家政员不是自己公司的家政员
            $updateData['publish'] = 'n';
            $updateData['accept_company_id'] = $data['company_info']['id'];
            //生成合作单
            $partnerOrderId = $this->makePartnerOrder($data);
            if(! $partnerOrderId){
                throw new LogicException ( T ( 'Test order fail' ), 232 );
            }
        }
        if($data['order_info']['order_status'] == 'change'){
            $updateData['change_time'] = new NotORM_Literal('change_time + 1 ');
        }
        $rs = $this->model->update($orderId,$updateData);
        if($rs){
            $orderStaffModel = new Model_Jiafubao_OrderStaff();
            //添加试工阿姨信息
            $staffData = array(
                'order_id' => $orderId,
                'staff_id' => $data['staff_info']['id'],
                'staff_name' => $data['staff_info']['name'],
                'status' => 'test',
                'create_time' => time(),
                'last_modify' => time(),
            );
            $orderStaffId = $orderStaffModel->insert($staffData);
            if(! $orderStaffId){
                throw new LogicException ( T ( 'Test order fail' ), 232 );
            }
            //添加订单日志
            $orderLogDomain = new Domain_Jiafubao_OrderLog();
            $orderLogDomain ->addLog($orderId,'指派家政员'.$data['staff_info']['name'].'试工. 试工时间: '.$data['testTime']);
        }else{
            throw new LogicException ( T ( 'Test order fail' ), 232 );
        }
        return true;
    }
    //生成合作订单
    public function makePartnerOrder($data){
        $orderInfo = $data['order_info'];
        $staffInfo = $data['staff_info'];
        //判断该订单是否存在活动的合作单
        $orderPartnerModel = new Model_Jiafubao_OrderPartner();
        $filter = array('order_id' => $orderInfo['id'],'status' => 'active');
        $orderPartnerInfo = $orderPartnerModel->getByWhere($filter);
        if($orderPartnerInfo){
            if($orderPartnerInfo['staff_id'] == $staffInfo['id']){
                //当想来试工的家政员的ID是合作单的ID-则不需要重新生成
                return $orderPartnerInfo['id'];
            }else{
                //先更新以前的合作单为失效-创建新的合作单
                $orderPartnerModel->updateByWhere(array('order_id' => $orderInfo['id']),array('status' => 'delete','last_modify' => time()));
            }
        }
        //获取合作家服云公司ID
        $zabCompanyModel = new Model_Zhianbao_Company();
        $jfbCompanyModel = new Model_Jiafubao_Company();
//        $companyInfo = $zabCompanyModel->get($orderInfo['company_id']);
        $companyInfo = $data['company_info'];
        $partnerZabCompanyInfo = $zabCompanyModel->get($staffInfo['company_id']);
        $filter = array('company_id' => $partnerZabCompanyInfo['id']);
        $partnerJfbCompanyInfo = $jfbCompanyModel->getByWhere($filter);
        //创建新的合作单
        $orderPartnerData = array(
            'order_id' => $orderInfo['id'],
            'order_bn' => $orderInfo['bn'],
            'company_id' => $orderInfo['company_id'],
            'company_name' => $companyInfo['name'],
            'company_mobile' => $companyInfo['mobile'],
            'partner_company_id' =>$partnerJfbCompanyInfo['id'],
            'staff_id' => $staffInfo['id'],
            'name' => $staffInfo['name'],
            'status' => 'active',
            'create_time' => time(),
            'last_modify' => time(),
        );
        $orderPartnerId = $orderPartnerModel->insert($orderPartnerData);
        return $orderPartnerId;
    }
    //试工通过
    public function testWorkPass($data){
        $orderId = $data['order_info']['id'];
        $updateData = array(
            'total_amount' => $data['amount'],
            'order_status' => 'work',
            'last_modify' => time(),
        );
        $rs = $this->model->update($orderId,$updateData);
        if($rs){
            $orderStaffModel = new Model_Jiafubao_OrderStaff();
            //更新阿姨状态为试工通过-上工
            $filter = array(
                'order_id' => $orderId,
                'status' => 'test',
            );
            $orderStaffInfo = $orderStaffModel->getByWhere($filter);
            $staffData = array(
                'status' => 'work',
                'last_modify' => time(),
            );
            $updateStatus = $orderStaffModel->updateByWhere($filter,$staffData);
            if(! $updateStatus ){
                return false;
            }
            //添加合同
            $contractModel = new Model_Jiafubao_OrderStaffContract();
            $contractData = array(
                'order_id' => $orderId,
                'company_id' => $data['order_info']['company_id'],
                'staff_id' => $orderStaffInfo['staff_id'],
                'staff_name' => $orderStaffInfo['staff_name'],
                'monthly_pay_time' => $data['monthly_pay_time'],
                'work_month' => $data['work_month'],
                'rest_day' => $data['rest_day'],
                'amount' => $data['amount'],
                'customer_intermediary_fee' => $data['customer_intermediary_fee'],
                'staff_intermediary_fee' => $data['staff_intermediary_fee'],
                'manage_fee' => $data['manage_fee'],
                'work_content' => $data['order_info']['content'],
                'create_time' => time(),
                'last_modify' => time()
            );
            if(isset($data['mark'])){
                $contractData['mark'] = $data['mark'];
            }
            if(isset($data['attachment'])){
                $contractData['attachment'] = $data['attachment'];
            }
            $contractId = $contractModel->insert($contractData);
            if(! $contractId){
                return false;
            }
            //添加订单日志
            $orderLogDomain = new Domain_Jiafubao_OrderLog();
            $orderLogDomain ->addLog($orderId,'家政员'.$orderStaffInfo['staff_name'].'试工通过,开始上单.');
        }else{
            return false;
        }
        return true;
    }

    //试工未通过
    public function testWorkClose($orderId,$amount,$ifChange,$mark){
        $updateData = array(
            'order_status' => 'close',
            'last_modify' => time(),
        );
        if($ifChange > 0){
            //换工
            $updateData['order_status'] = 'change';
            $updateData['staff_id'] = 0;
            $updateData['change_time'] = new NotORM_Literal('change_time + 1 ');
        }
        $rs = $this->model->update($orderId,$updateData);
        if($rs){
            $orderStaffModel = new Model_Jiafubao_OrderStaff();
            //更新阿姨状态为试工未通过
            $filter = array(
                'order_id' => $orderId,
                'status' => 'test',
            );
            $orderStaffInfo = $orderStaffModel->getByWhere($filter);
            $staffData = array(
                'status' => 'test_close',
                'last_modify' => time(),
            );
            $updateStatus = $orderStaffModel->updateByWhere($filter,$staffData);
            if(! $updateStatus ){
                throw new LogicException ( T ( 'Cancel order failed' ), 165 );
            }
            //添加未通过支付金额
            $closeAmountModel = new Model_Jiafubao_OrderStaffCloseAmount();
            $closeAmountData = array(
                'order_id' => $orderId,
                'staff_id' => $orderStaffInfo['staff_id'],
                'staff_name' => $orderStaffInfo['staff_name'],
                'amount' => $amount,
                'create_time' => time(),
                'last_modify' => time(),
            );
            $closeAmountId = $closeAmountModel->insert($closeAmountData);
            if(! $closeAmountId){
                throw new LogicException ( T ( 'Cancel order failed' ), 165 );
            }
            //添加订单日志
            $orderLogDomain = new Domain_Jiafubao_OrderLog();
            $orderLogDomain ->addLog($orderId,'家政员'.$orderStaffInfo['staff_name'].'试工未通过. 备注:'.$mark.'.需支付金额:￥'.$amount);
//            if($staffId > 0){
//                //换工
//                $orderInfo = $this->model->get($orderId);
//                $staffDomain = new Domain_Jiafubao_CompanyHouseStaff();
//                $staffInfo = $staffDomain->getBaseInfo($staffId);
//                $workData = array(
//                    'order_info' => $orderInfo,
//                    'staff_info' => $staffInfo,
//                );
//               $changeRs = $this->testWork($workData);
//               if(! $changeRs){
//                   throw new LogicException ( T ( 'Cancel order failed' ), 165 );
//               }
//            }
        }else{
            throw new LogicException ( T ( 'Cancel order failed' ), 165 );
        }
        return true;
    }

    //更换家政员
    public function changeStaff($data)
    {
        $orderId = $data['order_info']['id'];
        //更新当前家政员状态为上单中换工
        $orderStaffModel = new Model_Jiafubao_OrderStaff();
        $filter = array(
            'order_id' => $orderId,
            'status' => 'work',
        );
        $orderStaffData = array(
            'status' => 'work_change',
            'last_modify' => time(),
        );
        $updateRs = $orderStaffModel->updateByWhere($filter,$orderStaffData);
        if(! $updateRs){
            throw new LogicException ( T ( 'Cancel order failed' ), 165 );
        }
        //处理合同
        $contractModel = new Model_Jiafubao_OrderStaffContract();
        $filter = array(
            'order_id' => $orderId,
            'staff_id' => $data['order_info']['staff_id'],
        );
        $contractData = array(
            'return_customer_fee' => $data['return_customer_fee'],
            'return_staff_fee' => $data['return_staff_fee'],
            'receive_fee' => $data['receive_fee'],
            'status' => 'close',
            'last_modify' => time(),
        );
//        if(isset($data['mark'])){
//            $contractData['mark'] = $data['mark'];
//        }
        $contractRs = $contractModel->updateByWhere($filter,$contractData);
        if(! $contractRs){
            throw new LogicException ( T ( 'Cancel order failed' ), 165 );
        }
        //更新订单状态
        $updateData = array(
            'order_status' => 'change',
            'last_modify' => time()
        );
       // $updateData['change_time'] = new NotORM_Literal('change_time + 1 ');
        $rs = $this->model->update($orderId, $updateData);
        if(! $rs){
            throw new LogicException ( T ( 'Cancel order failed' ), 165 );
        }
        //添加订单日志
        $orderLogDomain = new Domain_Jiafubao_OrderLog();
        $orderLogDomain ->addLog($orderId,'订单换工,处理合同成功.备注:'.$data['mark']);


        //添加出入账日志
        $orderPayLogModel = new Model_Jiafubao_OrderPayLog();
        if($contractData['return_customer_fee'] > 0){
            $data = array(
                'order_id' => $orderId,
                'amount' => $contractData['return_customer_fee'],
                'reason' => '订单退款给客户',
                'create_time' => time(),
            );
            $rs = $orderPayLogModel ->insert($data);
            if(! $rs){
                throw new LogicException ( T ( 'Cancel order failed' ), 165 );
            }
        }
        if($contractData['return_staff_fee'] > 0){
            $data = array(
                'order_id' => $orderId,
                'amount' => $contractData['return_staff_fee'],
                'reason' => '订单退款给家政员',
                'create_time' => time(),
            );
            $rs = $orderPayLogModel ->insert($data);
            if(! $rs){
                throw new LogicException ( T ( 'Cancel order failed' ), 165 );
            }
        }
        if($contractData['receive_fee'] > 0){
            $data = array(
                'order_id' => $orderId,
                'amount' => $contractData['receive_fee'],
                'reason' => '订单收入金额',
                'create_time' => time(),
            );
            $rs = $orderPayLogModel ->insert($data);
            if(! $rs){
                throw new LogicException ( T ( 'Cancel order failed' ), 165 );
            }
        }
        return true;
    }

    //订单跟踪标记
    public function markOrder(){
        $contractModel = new Model_Jiafubao_OrderStaffContract();
        $setDomain = new Domain_Jiafubao_CompanySetting();

        $filter = array('status' => 'active');
        $contractList = $contractModel->getAll('id,order_id,company_id,monthly_pay_time',$filter);
        $companyOrderIds = array();
       foreach ($contractList as $key => $value){
            $companyOrderIds[$value['company_id']][] = $value;
       }
       foreach ($companyOrderIds as $key => $value){
           $payNotice = $setDomain->get($key,'pay_notice');
           if($payNotice){
               //更新订单为支付提醒
               $updateData = array(
                   'pay_status' => 0,
                   'pay_notice' => 'y'
               );
               foreach ($value as $k => $v){
                   $needPayDay = date('d',strtotime("+".$payNotice." days") );
                   if($needPayDay == $v['monthly_pay_time']) {
                       $this->model->update($v['order_id'], $updateData);
                   }
               }
           }
           $visitNotice = $setDomain->get($key,'visit_notice');
           if($visitNotice){
               //更新订单为回访
               $updateData = array(
                   'visit_notice' => 'y'
               );
               foreach ($value as $k => $v){
                   $needVisitDay = date('d',strtotime("now") );
                   if($needVisitDay == $visitNotice) {
                       $this->model->update($v['order_id'], $updateData);
                   }
               }
           }
       }
       return true;
    }
    //取消支付标记
    public function PayUnMark($orderId){
        $data = array(
            'pay_notice' => 'n',
            'last_modify' => time()
        );
        $rs = $this->model->update($orderId,$data);
        return $rs;
    }

    //取消回访标记
    public function VisitUnMark($orderId){
        $data = array(
            'visit_notice' => 'n',
            'last_modify' => time()
        );
        $rs = $this->model->update($orderId,$data);
        return $rs;
    }
    //获取弹窗信息
    public function getWindow($companyId){
        //支付标记数
        $filter = array(
            'accept_company_id' => $companyId,
            'pay_notice' => 'y',
        );
        $payCount = $this->model->getCount($filter);
        //回访标记数
        $filter = array(
            'accept_company_id' => $companyId,
            'visit_notice' => 'y',
        );
        $visitCount = $this->model->getCount($filter);
        $rs['pay_count'] = $payCount;
        $rs['visit_count'] = $visitCount;
        return $rs;
    }
    //获取神铺商品订单信息
    public function spOrderInfo($goods_order_id){
        $rs = array();
        //获取订单详情
        $params= array(
            'order_id' => $goods_order_id,
            'source' => 'jfb',
        );

        $result = malsapi_request('Shenpu_Order_InfoGet.Go',$params);
        $result = $result['data'];
        if( $result['code'] == 0 ){
            $rs[] = $result['list'];
        }
        return $rs;
    }
    //生成二维码
    public function qrcode($orderId,$shareUserId){
        $params = array('user_id' => $shareUserId);
        $result = malsapi_request('Shenpu_User_InfoGet.Go',$params);
        $result = $result['data'];
        $mobile  = $result['info']['login_name'];
//        $h5Url = DI()->config->get('app.jfbh5.get_h5_url');
//        $url = sprintf('http://%s/newQrCodeDetail?orderId='.$orderId ,$h5Url);
        $url = 'http://jfbadh5.mshenpu.com/shareOrder?orderId='.$orderId.'&mobile='.$mobile;
        $rs =  'http://pan.baidu.com/share/qrcode?w=150&h=150&url='.urlencode($url);
        return $rs;
    }
    //订单分享
    public function share($orderId){
        $rs = array();
        $orderDomain = new Domain_Jiafubao_Order();
        $orderInfo = $orderDomain->getBaseInfo($orderId);
 //       print_r($orderInfo);exit;
        if( !$orderInfo){
            return false;
        }else{
            $rs['id'] = $orderInfo['id'];
            $rs['staff_id'] = $orderInfo['staff_id'];
            $rs['bn'] = $orderInfo['bn'];
            $rs['total_amount'] = $orderInfo['total_amount'];
            $rs['pay_status'] = $orderInfo['pay_status'];
            $rs['order_status'] = $orderInfo['order_status'];
            $rs['custom_mark_text'] = $orderInfo['custom_mark_text'];
            $rs['admin_mark_text'] = $orderInfo['admin_mark_text'];
            $rs['mark'] = $orderInfo['mark'];
            $rs['server_img'] = $orderInfo['server_img'];
            $rs['service_type'] = $orderInfo['service_type'];
            $rs['content'] = json_decode($orderInfo['content'],true);
            $rs['supplement_mark_text'] = $orderInfo['supplement_mark_text'];
            //判断是否是平台订单
            if($orderInfo['is_jfy'] == 'y'){
                $rs['company_name'] = '家服云平台';
                $rs['company_telephone'] = '';
            }else{
                //公司信息
                $rs['company_name'] = $orderInfo['company_info']['name'];
                //获取公司电话
                $companyModel = new Model_Jiafubao_Company();
                $filter = array('company_id' => $orderInfo['company_id']);
                $company = $companyModel->getByWhere($filter,'telephone');
                if($company['telephone'] != NULL){
                    $rs['company_telephone'] = $company['telephone'];
                }else{
                    $rs['company_telephone'] = $orderInfo['company_info']['mobile'];
                }
            }

            //客户信息
            $rs['customer']['consignee'] = $orderInfo['consignee'];
            $rs['customer']['address_details'] = $orderInfo['country'].$orderInfo['province'].$orderInfo['city'].$orderInfo['district'];
            //客户评价
            $rs['customer_appraise_list'] = $orderInfo['customer_appraise_list'];
            //订单合同
            if(isset($orderInfo['contract_list']['work_content'])){
                $orderInfo['contract_list']['work_content'] = json_decode($orderInfo['contract_list']['work_content'],true);
            }
            $rs['contract_list'] = $orderInfo['contract_list'];
        }

        return $rs;
    }
    //订单补充说明
    public function updateSupplementOrder($data){
        $orderLogDomain = new Domain_Jiafubao_OrderLog();
        $orderInfo = $data['order_info'];
        $orderId = $orderInfo['id'];

        $orderData = array(
            'supplement_mark_text' => $data['supplement_mark_text'],
            'last_modify' => time(),
        );
        //更新订单
        $orderRs = $this->model->update($orderId,$orderData);
        if(! $orderRs){
            throw new LogicException ( T ( 'Update failed' ), 104 );
        }

        if(empty($data['order_info']['supplement_mark_text'])){
            //添加订单日志
            $orderLogRs = $orderLogDomain->addLog($orderId,'家政公司添加订单补充信息');
            if(! $orderLogRs){
                throw new LogicException ( T ( 'Update failed' ), 104 );
            }
        }else{
            //添加订单日志
            $orderLogRs = $orderLogDomain->addLog($orderId,'家政公司更新订单补充信息');
            if(! $orderLogRs){
                throw new LogicException ( T ( 'Update failed' ), 104 );
            }
        }

        return true;
    }
    //获取平台订单优惠券使用情况
    public function getCouponInfo($orderId,$bbcCustomerId){
        $rs = array();
        $params= array(
            'order_id' => $orderId,
            'bbc_customer_id' => $bbcCustomerId,
        );

        $result = jfyapi_request('Order_Coupon_Get.Go',$params);
        if($result['ret'] == 200 && $result['data']['code'] == 0){
            $rs = $result['data']['info'];
        }
        return $rs;
    }
    //获取神铺客户信息
    public function getCustomerInfo($bbcCustomerId){
        $params= array(
            'customer_id' => $bbcCustomerId,
        );

        $result = malsapi_request('Bbc_Customer_InfoGet.Go',$params);
        if($result['ret'] == 200 && $result['data']['code'] == 0){
            $rs = $result['data']['info'];
        }else{
            return false;
        }
        return $rs;
    }
    //检测平台客户是否加入神铺
    public function checkCustomer($orderInfo,$companyInfo){
        $shopInfo = $this->shopInfo($companyInfo['user_id']);
        $shopId = $shopInfo['id'];
        if($shopId == 0){
            return false;
        }
        $params= array(
            'shop_id' => $shopId,
            'bbc_customer_id' => $orderInfo['bbc_customer_id'],
        );
        $result = malsapi_request('Shenpu_Customer_Jfy_Add.Go',$params);
        if($result['ret'] == 200 && $result['data']['code'] == 0){
            $rs = $result['data']['info']['customer_id'];
        }else{
            return false;
        }
        return $rs;
    }
    function shopInfo($userId){
        //获取用户的店铺
        $shop_params = array(
            'user_id' => $userId,
            'operator_id' => $userId,
        );
        $list = malsapi_request('Shenpu_Shop_ListGet.Go',$shop_params);
        $list = $list['data']['list'];
        if( ! isset($list[0])){
            return array('id' => 0);
        }else{
            $rs = $list[0];
        }
        return $rs;
    }

    //订单编辑
    public function updateOrder($data){
        $orderId = $data['order_id'];
        unset($data['order_id']);
        $rs = $this->model->update($orderId, $data);
        if( ! $rs){
            throw new LogicException ( T ( 'Update failed' ), 104 );
        }else{
            //添加订单日志
            $orderLogDomain = new Domain_Jiafubao_OrderLog();
            $orderLogDomain ->addLog($orderId,'订单编辑成功');
        }
        return $rs;
    }


}
