<?php
class Domain_Building_DataReport {

    //获取项目成本记录
    public function getProjectCost($projectInfo,$filter, $page = 1, $page_size = 20){
        $beginTime = $filter['beginTime'];
        $endTime = $filter['endTime'];
        unset($filter['beginTime']);unset($filter['endTime']);
        $day = ($endTime - $beginTime) / 86400;
        if($day > 90){
            return false;
        }
        //获取查询天数
        $lastTime = array();
        for($i = 1; $i <= $day; $i++){
            $lastDaytime = $endTime - 86400;
            $lastTime[$i]['start_time'] = $lastDaytime;
            $lastTime[$i]['stop_time'] = $endTime;
            $endTime = $lastDaytime;
        }
        krsort($lastTime);
        $rs = array_values($lastTime);
        $projectStatisticsModel = new Model_Building_ProjectStatistics();
        $staffSignItemModel = new Model_Building_ProjectStaffSign();
        foreach ($rs as $key=>$value){
            //获取项目人员信息
            $log_filter = array('company_id' =>  $filter['company_id'], 'project_id' =>  $filter['project_id'], 'record_time' => $value['start_time']);
            $staff_info = $projectStatisticsModel->getByWhere($log_filter,'staff_info');
            $list = json_decode($staff_info['staff_info'],true);
            //预算人天
            $budgetTotal = COUNT($list);
            //实际签到人天
            $signTotal = 0;
            $sign_list = $staffSignItemModel->getAll('*',$log_filter);
            foreach ($sign_list as $kk=>$vv){
                $signTotal += $vv['cost'];
            }

            $rs[$key]['start_time'] = date("Y-m-d",$value['start_time']);
            $rs[$key]['stop_time'] = date("Y-m-d",$value['stop_time']);
            $rs[$key]['budgetTotal'] = $budgetTotal;
            $rs[$key]['signTotal'] = $signTotal;
        }

        $list = $rs;
        $list['day'] = $day;
        return $list;
    }

    //获取项目在岗统计
    public function getProjectWorkInfo($filter,$projectInfo){
        $rs = array();
        $workTotal = 0;
        $releaseTotal = 0;
        $projectToStaffModel = new Model_Building_ProjectToStaff();
        $list = $projectToStaffModel->getAll('*',$filter);
        foreach ($list as $key=>$value){
            if($value['status'] == 'y'){
                ++$workTotal;
            }else{
                ++$releaseTotal;
            }
        }
        $rs['project_id'] = $projectInfo['id'];
        $rs['project_name'] = $projectInfo['name'];
        $rs['workTotal'] = $workTotal;
        $rs['releaseTotal'] = $releaseTotal;

        return $rs;
    }
    //项目出入账统计
    public function getProjectBillInfo($filter, $page = 1, $page_size = 20){
        $beginTime = $filter['beginTime'];
        $endTime = $filter['endTime'];
        unset($filter['beginTime']);unset($filter['endTime']);
        $day = ($endTime - $beginTime) / 86400;
        if($day > 90){
            return false;
        }
        //获取查询天数
        $lastTime = array();
        for($i = 1; $i <= $day; $i++){
            $lastDaytime = $endTime - 86400;
            $lastTime[$i]['start_time'] = $lastDaytime;
            $lastTime[$i]['stop_time'] = $endTime;
            $endTime = $lastDaytime;
        }
        krsort($lastTime);
        $rs = array_values($lastTime);

        //获取统计
        $billProjectModel = new Model_Building_BillProject();
        foreach ($rs as $key=>$value){
            //支出、收入、借支
            $expenditureTotal = 0.00;
            $incomeTotal = 0.00;
            $borrowTotal = 0.00;
            $filter['create_time > ?'] = $value['start_time'];
            $filter['create_time < ?'] = $value['stop_time'];
            //获取该时间段内的数据
            $list = $billProjectModel->getAll('*',$filter);
            foreach ($list as $kk=>$vv){
                //支出
                if($vv['type'] == 'expenditure'){
                    $expenditureTotal += $vv['amount'];
                }
                //收入
                if($vv['type'] == 'income'){
                    $incomeTotal += $vv['amount'];
                }
                //借支
                if($vv['type'] == 'borrow'){
                    $borrowTotal += $vv['amount'];
                }
            }
            $rs[$key]['start_time'] = date("Y-m-d",$value['start_time']);
            $rs[$key]['stop_time'] = date("Y-m-d",$value['stop_time']);
            $rs[$key]['expenditureTotal'] = $expenditureTotal;
            $rs[$key]['incomeTotal'] = $incomeTotal;
            $rs[$key]['borrowTotal'] = $borrowTotal;
        }
        $result = $rs;
        $result['day'] = $day;
        return $result;
    }
    //项目资金汇总
    public function getProjectAmount($filter,$projectInfo){
        $rs = array();
        //项目收入、支出
        $incomeTotal = 0.00;
        $expenditureTotal = 0.00;
        $borrowTotal = 0.00;
        $billProjectModel = new Model_Building_BillProject();
        $list = $billProjectModel->getAll('*',$filter);
    //    print_r($list);exit;
        foreach ($list as $key=>$value){
            //项目收入
            if($value['type'] == 'income'){
                $incomeTotal += $value['amount'];
            }
            //项目支出
            if($value['type'] == 'expenditure'){
                $expenditureTotal += $value['amount'];
            }
            //项目借支
            if($value['type'] == 'borrow'){
                $borrowTotal += $value['amount'];
            }
        }
        $rs['project_id'] = $projectInfo['id'];
        $rs['project_name'] = $projectInfo['name'];
        $rs['amountTotal'] = $projectInfo['amount'];
        $rs['incomeTotal'] = $incomeTotal;
        $rs['expenditureTotal'] = $expenditureTotal;
        $rs['borrowTotal'] = $borrowTotal;

        return $rs;
    }

}
