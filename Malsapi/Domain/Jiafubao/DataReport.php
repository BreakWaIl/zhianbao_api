<?php
class Domain_Jiafubao_DataReport {
	var $model;

	public function __construct() {
		$this->model = new Model_Jiafubao_Order();
	}
    //获取列表
    public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
        $beginTime = $filter['beginTime'];
        $endTime = $filter['endTime'];
        unset($filter['beginTime']);unset($filter['endTime']);
        $filter['create_time > ?'] = $beginTime;
        $filter['create_time < ?'] = $endTime;
        $order_list = $this->model->getAll ( '*', $filter);
       // print_r($order_list);exit;
        $day = ($endTime - $beginTime) / 86400;
        //获取查询天数
        $lastTime = array();
        for($i = 1; $i <= $day; $i++){
            $lastDaytime = $endTime - 86400;
            $lastTime[$i]['start_time'] = date('Y-m-d H:i:s',$lastDaytime);
            $lastTime[$i]['stop_time'] = date('Y-m-d H:i:s',$endTime);
            $endTime = $lastDaytime;
        }
        //krsort($lastTime);
        $rs = array_values($lastTime);
        foreach ($rs as $key=>$value){
            $total = 0.00;
            $orderNum = 0;
            foreach ($order_list as $kk=>$vv){
                if($value['start_time'] < $vv['create_time'] && $vv['create_time'] < $value['stop_time']){
                    $orderNum++;
                    $total += $vv['total_amount'];
                }
            }
            $rs[$key]['orderTotal'] = $orderNum;
            $rs[$key]['total'] = $total;
        }
        return $rs;
    }
    //获取数量
    public function getCount($filter) {
        $beginTime = $filter['beginTime'];
        $endTime = $filter['endTime'];
        unset($filter['beginTime']);unset($filter['endTime']);
        $filter['create_time > ?'] = $beginTime;
        $filter['create_time < ?'] = $endTime;
        return $this->model->getCount ( $filter );
    }


    //获取服务质量统计
    public function getServiceByPage($filter, $page = 1, $page_size = 20){
        $beginTime = $filter['beginTime'];
        $endTime = $filter['endTime'];
        unset($filter['beginTime']);unset($filter['endTime']);
        $filter['create_time > ?'] = $beginTime;
        $filter['create_time < ?'] = $endTime;
        $order_list = $this->model->getAll ( '*', $filter);
        $day = ($endTime - $beginTime) / 86400;
        if($day > 90){
           return false;
        }
        //获取查询天数
        $lastTime = array();
        for($i = 1; $i <= $day; $i++){
            $lastDaytime = $endTime - 86400;
            $lastTime[$i]['start_time'] = date('Y-m-d H:i:s',$lastDaytime);
            $lastTime[$i]['stop_time'] = date('Y-m-d H:i:s',$endTime);
            $endTime = $lastDaytime;
        }
        $rs = array_values($lastTime);
        foreach ($rs as $key=>$value){
            $orderNum = 0; //订单数
            $closeTotal = 0; //退单数
            $changeTotal= 0; //换单数
            //获取终止的订单
            foreach ($order_list as $kk=>$vv){
                //获取订单数量
                if($value['start_time'] <= $vv['create_time'] && $vv['create_time'] < $value['stop_time']){
                    $orderNum++;
                    //获取退单数量
                    if($vv['order_status'] == 'close'){
                        $closeTotal++;
                    }
                    //获取换单数量
                    if($vv['order_status'] == 'change'){
                        $changeTotal++;
                    }
                }
            }
            //退单率
            if($closeTotal == 0){
                $closeOrderRatio = 0;
            }else{
                $closeOrderRatio = intval(($closeTotal / $orderNum)*100)/100;
            }
            //换单率
            if($changeTotal == 0){
                $orderChangeRatio = 0;
            }else{
                $orderChangeRatio = intval(($changeTotal / $orderNum)*100)/100;
            }
            //获取单位时间内的评分
            $cx_filter = array(
                'company_id' => $filter['company_id'],
                'create_time >= ?' => strtotime($value['start_time']),
                'create_time < ?' => strtotime($value['stop_time']),
            );
            $fraction = $this->averageFraction($cx_filter);
            $rs[$key]['orderTotal'] = $orderNum;
            $rs[$key]['closeTotal'] = $closeTotal;
            $rs[$key]['orderCloseRatio'] = $closeOrderRatio;
            $rs[$key]['orderChangeRatio'] = $orderChangeRatio;
            $rs[$key]['customerFraction'] = $fraction['customerFraction'];
            $rs[$key]['staffFraction'] = $fraction['staffFraction'];
        }
        $start = ($page-1) * $page_size;
        $list = array_slice($rs, $start, $page_size);
        $list['day'] = $day;
//        print_r($list);exit;
        return $list;
    }
    //获取服务平均分
    public function averageFraction($filter){
        $rs = array();

        //家政员订单评价等级
        $staff_level = 0;
        //客户订单评价等级
        $customer_level = 0;
        $appraiseModel = new Model_Jiafubao_OrderAppraise();
        //获取订单列表
        $count = $this->model->getCount($filter);
        $list = $this->model->getAll('id',$filter);
        foreach ($list as $key=>$value){
            //获取存在客户评价的订单
            $customerAppraiseFilter = array(
                'order_id' => $value['id'],
                'type' => 'customer',
            );
            $customer_appraise_list = $appraiseModel->getAll('*',$customerAppraiseFilter);
            if(!empty($customer_appraise_list)){
                foreach ($customer_appraise_list as $kk=>$vv){
                    //获取客户评价等级
                    $customer_level += $vv['level'];
                }
                //获取已评价订单数量
            }else{
                $customer_level += 5;
            }
            //获取家政员评价客户的订单
            $customerAppraiseFilter = array(
                'order_id' => $value['id'],
                'type' => 'staff',
            );
            $staff_appraise_list = $appraiseModel->getAll('*',$customerAppraiseFilter);
            if(!empty($staff_appraise_list)){
                foreach ($staff_appraise_list as $k=>$v){
                    //获取家政员评价等级
                    $staff_level += $v['level'];
                }
                //获取已评价订单数量
            }else{
                $staff_level += 5;
            }

        }
        if($customer_level > 0){
            $customerFraction = intval(($customer_level/$count)*100)/100;
        }else{
            $customerFraction = 5;
        }
        if($staff_level > 0){
            $staffFraction = intval(($staff_level/$count)*100)/100;
        }else{
            $staffFraction = 5;
        }

        $rs['customerFraction'] = $customerFraction;
        $rs['staffFraction'] = $staffFraction;
//        print_r($rs);exit;

        return $rs;
    }

    //获取监管者订单金额统计列表
    public function regulatorGetAllByPage($filter, $beginTime, $endTime, $page = 1, $page_size = 20, $orderby = ''){
        $companyModel = new Model_Zhianbao_Company();
        $rs = $companyModel->getAll ( 'id,name,user_id,mobile', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key=>$value){
            $amountTotal = 0.00;
            $finish_total = 0;
            $order_filter = array();
            $order_filter['company_id'] = $value['id'];
            if(!empty($beginTime) && !empty($endTime)){
                $order_filter['create_time > ?'] = $beginTime;
                $order_filter['create_time < ?'] = $endTime;
            }
            $list = $this->model->getAll('*', $order_filter);
            $order_total = $this->model->getCount($order_filter);
            foreach ($list as $kk=>$vv){
                //完成的订单数
                if($vv['order_status'] == 'finish'){
                    $finish_total++;
                    $amountTotal += $vv['total_amount'];
                }
            }

            $rs[$key]['orderTotal'] = $order_total;
            $rs[$key]['finishTotal'] = $finish_total;
            $rs[$key]['amountTotal'] = $amountTotal;
        }

        return $rs;
    }

    //获取监管者服务质量统计列表
    public function regulatorGetAllService($filter, $beginTime, $endTime, $page = 1, $page_size = 20, $orderby = ''){
        $companyModel = new Model_Zhianbao_Company();
        $rs = $companyModel->getAll ( 'id,name,user_id,mobile', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key=>$value){
            $finish_total = 0;
            $closeTotal = 0;
            $order_filter = array();
            $order_filter['company_id'] = $value['id'];
            if(!empty($beginTime) && !empty($endTime)){
                $order_filter['create_time > ?'] = $beginTime;
                $order_filter['create_time < ?'] = $endTime;
            }
            if(!empty($beginTime)){
              $order_filter['create_time > ?'] = $beginTime;
            }
            if(!empty($endTime)){
              $order_filter['create_time < ?'] = $endTime;
            }
            $list = $this->model->getAll('*', $order_filter);
            $order_total = $this->model->getCount($order_filter);
            foreach ($list as $kk=>$vv){
                //完成的订单数
                if($vv['order_status'] == 'finish'){
                    $finish_total++;
                }
                //退单的数量
                if($vv['order_status'] == 'close'){
                    $closeTotal++;
                }
            }
            if($closeTotal == 0){
                $closeOrderRatio = 0;
            }else{
                $closeOrderRatio = number_format(($closeTotal / $order_total),2)*100;
            }
            $rs[$key]['orderTotal'] = $order_total;
            $rs[$key]['finishTotal'] = $finish_total;
            $rs[$key]['closeTotal'] = $closeTotal;
            $rs[$key]['closeOrderRatio'] = $closeOrderRatio;
        }
        return $rs;
    }
    //获取监管者下属公司数量
    public function RegulatorGetCount($filter) {
        $companyModel = new Model_Zhianbao_Company();
        return $companyModel->getCount ( $filter );
    }

}
