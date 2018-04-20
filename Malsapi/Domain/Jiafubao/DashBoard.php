<?php
class Domain_Jiafubao_DashBoard {

	//获取通知详情
    public function storeDashBoard($companyId){
        $rs = array ();
        $staffModel = new Model_Jiafubao_CompanyHouseStaff();
        $keepCardModel = new Model_Jiafubao_CompanyHouseStaffCard();
        $healthCardModel = new Model_Jiafubao_CompanyStaffHealthCard();
        $insuranceModel = new Model_Jiafubao_CompanyStaffInsurance();
        $abilityModel = new Model_Jiafubao_CompanyStaffAbilityCert();
        $skillModel = new Model_Jiafubao_CompanyStaffSkillCert();
        $orderModel = new Model_Jiafubao_Order();
        //获取家政员列表
        $filter = array('company_id' => $companyId);
        $staff_list = $staffModel->getAll('*', $filter);
        //市场订单数
        $orderTotal = $orderModel->getCount($filter);
        //待上传的家政证
        $keepCardTotal = 0;
        foreach ($staff_list as $key=>$value){
            $filter['staff_id'] = $value['id'];
            $keepCardInfo = $keepCardModel->getByWhere($filter,'*');
            if(empty($keepCardInfo)){
                $keepCardTotal++;
            }
        }
        //待上传的健康卡
        $healthCardTotal = 0;
        foreach ($staff_list as $key=>$value){
            $health_filter = array('company_id' => $companyId, 'staff_id' => $value['id'], 'status' => 'y');
            $healthCardInfo = $healthCardModel->getByWhere($health_filter,'*');
            if(empty($healthCardInfo)){
                $healthCardTotal++;
            }
        }
        //待上传的保险记录
        $waitInsuranceTotal = 0;
        foreach ($staff_list as $key=>$value){
            $filter['staff_id'] = $value['id'];
            $insuranceInfo = $insuranceModel->getByWhere($filter,'*');
            if(empty($insuranceInfo)){
                $waitInsuranceTotal++;
            }
        }

        //待上传的专项技能卡
        $certTotal = 0;
        foreach ($staff_list as $key=>$value){
            $cert_filter = array('company_id' => $companyId, 'staff_id' => $value['id'],  'status' => 'y');
            $abilityInfo = $abilityModel->getByWhere($cert_filter,'*');
            $skillInfo = $skillModel->getByWhere($cert_filter,'*');
            if( !$abilityInfo || !$skillInfo){
                $certTotal++;
            }
        }
        //支付标记数
        $filter = array(
            'accept_company_id' => $companyId,
            'pay_notice' => 'y',
        );
        $payCount = $orderModel->getCount($filter);
        //回访标记数
        $filter = array(
            'accept_company_id' => $companyId,
            'visit_notice' => 'y',
        );
        $visitCount = $orderModel->getCount($filter);
        $rs['visitCount'] = $visitCount;
        $rs['payCount'] = $payCount;
        $rs['keepCardTotal'] = $keepCardTotal;
        $rs['healthCardTotal'] = $healthCardTotal;
        $rs['certTotal'] = $certTotal;
        $rs['waitInsuranceTotal'] = $waitInsuranceTotal;
        $rs['orderTotal'] = $orderTotal;
        return $rs;
    }

    public function getDashBoard($regulatorId){
        $rs = array();

        $regulatorDomain = new Domain_Zhianbao_Regulator();
        $orderModel = new Model_Jiafubao_Order();
       // $demandModel = new Model_Jiafubao_Demand();
        $staffModel = new Model_Jiafubao_CompanyHouseStaff();
        $abilityModel = new Model_Jiafubao_CompanyStaffAbilityCert();
        $skillModel = new Model_Jiafubao_CompanyStaffSkillCert();
        $companyIds = $regulatorDomain->getCompanyIds($regulatorId);
        //企业数量
        $companyTotal = COUNT($companyIds);
        //家政员数量
        $staff_filter = array('company_id' => $companyIds);
        $staffTotal = $staffModel->getCount($staff_filter);
        //正规家政员数量
        $qualifiedTotal = 0;
        $staff_list = $staffModel->getAll('*', $staff_filter);
        foreach ($staff_list as $key=>$value){
            $cert_filter = array('company_id' => $value['company_id'], 'staff_id' => $value['id'], 'status' => 'y');
            $abilityInfo = $abilityModel->getByWhere($cert_filter,'*');
            $skillInfo = $skillModel->getByWhere($cert_filter,'*');
            if( $abilityInfo && $skillInfo){
                $qualifiedTotal++;
            }
        }
        //需要补充证件的家政员数量
        $unqualifiedTotal = $staffTotal - $qualifiedTotal;
        //近一周订单数
        $filter = array(
            'company_id' => $companyIds,
			'create_time > ?'=>strtotime(date("Y-m-d",time()-7*86400)),
			'create_time < ?'=>strtotime(date("Y-m-d")),
        );
        $weekOrderTotal = $orderModel->getCount($filter);
        //未接到的需求数
        $demand_filter = array(
            'company_id' => $companyIds,
            'order_status' => 'wait',
        );
        $demandTotal = $orderModel->getCount($demand_filter);
        //平均退单率
        $order_filter = array('company_id' => $companyIds);
        $orderTotal = $orderModel->getCount($order_filter);
        $order_filter['order_status'] = 'close';
        $closeOrderTotal = $orderModel->getCount($order_filter);
//        $closeOrderRatio = number_format(($closeOrderTotal / $orderTotal),2)*100;
        if($closeOrderTotal == 0 || $orderTotal == 0){
            $closeOrderRatio = 0;
        }else{
            $closeOrderRatio = number_format(($closeOrderTotal / $orderTotal),2)*100;
        }

        //平均换单率
        $replaceOrderTotal = 0;

        $rs['companyTotal'] = $companyTotal;
        $rs['staffTotal'] = $staffTotal;
        $rs['qualifiedTotal'] = $qualifiedTotal;
        $rs['unqualifiedTotal'] = $unqualifiedTotal;
        $rs['orderTotal'] = $weekOrderTotal;
        $rs['demandTotal'] = $demandTotal;
        $rs['closeOrderRatio'] = $closeOrderRatio;
        $rs['replaceOrderTotal'] = $replaceOrderTotal;
        return $rs;
    }

}
