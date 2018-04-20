<?php
class Domain_Building_Statistics {
    var $model;

    public function __construct() {
        $this->model = new Model_Building_ProjectStatistics();

    }
    //计划任务，更新项目信息
    public function projectData(){
        $companyModel = new Model_Zhianbao_Company();
        $filter = array('type' => 'building');
        $list = $companyModel->getAll('*',$filter);
        foreach ($list as $key=>$value){
            $this->statistics($value['id']);
        }
    }

    //统计项目人员信息
    public function statistics($companyId){
        $projectModel = new Model_Building_Project();
        //获取公司下项目信息
        //获取前一天时间
        $time =  strtotime(date("Y-m-d")) - 86400;
        $filter = array('company_id' => $companyId);
        $filter['status'] = 'active';
        $project_list = $projectModel->getAll( '*', $filter);
        unset($filter['status']);
        foreach ($project_list as $key=>$value){
            //获取项目状态
            if($value['status'] == 'active') {
                $filter['project_id'] = $value['id'];
                //获取项目人员、数量
                $list_info = $this->getAllStaff($companyId, $value['id']);
                $filter['record_time'] = $time;
                $projectInfo = $this->model->getByWhere($filter, '*');
                if(empty($projectInfo)){
                    //插入项目总人数统计信息
                    $data = array(
                        'company_id' => $companyId,
                        'project_id' => $value['id'],
                        'staff_total' => $list_info['total'],
                        'staff_info' => json_encode($list_info['list']),
                        'sign_config' => $value['sign_config'],
                        'record_time' => $time,
                        'create_time' => time(),
                        'last_modify' => time(),
                    );
                    $res = $this->model->insert($data);
                    if(!$res){
                        throw new LogicException (T('Add failed'), 102);
                    }
                } else{
                    //更新项目总人数统计信息
                    $data = array(
                        'staff_total' => $list_info['total'],
                        'staff_info' => json_encode($list_info['list']),
                        'sign_config' => $value['sign_config'],
                        'last_modify' => time(),
                    );
                    $res = $this->model->update($projectInfo['id'],$data);
                    if(!$res){
                        throw new LogicException (T('Add failed'), 102);
                    }
                }
                //统计项目下的员工签到信息
                //正常
                $this->active($value, $filter);
                //异常
                $this->absent($value, $filter);
                //未签到
                $this->never($filter);
            }
        }
    }

    function getAllStaff($companyId,$projectId){
        $staffModel = new Model_Building_Staff();
        $projectToStaffModel = new Model_Building_ProjectToStaff();
        //获取在项目中的员工
        $filter = array(
            'company_id' => $companyId,
            'project_id' => $projectId,
            'status' => 'y',
        );
        $list = $projectToStaffModel->getAll('*', $filter);
        $staff_list = array();
        foreach ($list as $key=>$value){
            $info = $staffModel->get($value['staff_id']);
            $staff_list[$key]['pid'] = $value['project_id'];
            $staff_list[$key]['id'] = $info['id'];
            $staff_list[$key]['name'] = $info['name'];
        }
        $total = $projectToStaffModel->getCount($filter);
        $rs['list'] = $staff_list;
        $rs['total'] = $total;

        return $rs;
    }

    //获取正常签到人员
    public function active($projectInfo,$filter){
        $projectStatisticsModel = new Model_Building_ProjectStatistics();
        $staffSignItemModel = new Model_Building_ProjectStaffSign();
        $signLogModel = new Model_Building_SignLog();
        //时间段打卡时间
        $morning_first_time = 0;
        $morning_end_time = 0;
        $after_first_time = 0;
        $after_end_time = 0;
        $night_first_time = 0;
        $night_end_time = 0;
        //当前时间
        $time = date("Y-m-d",$filter['record_time']);
        //获取当前时间项目下的人员
        $info = $projectStatisticsModel->getByWhere($filter,'*');
        $staff_list = json_decode($info['staff_info'],true);
        if(is_array($staff_list) && !empty($staff_list)){
            $rs = array();
            //获取项目设置的打卡时间
            $config = json_decode($info['sign_config'],true);
            //获取第一时间段打卡设置
            $morning_startTime  = strtotime($time.$config['day_config']['startWork']);
            $morning_endTime = strtotime($time.$config['day_config']['endWork']);
            //获取第二时间段打卡设置
            $after_startTime  = strtotime($time.$config['after_config']['startWork']);
            $after_endTime = strtotime($time.$config['after_config']['endWork']);
            //获取第三时间段打卡设置
            $night_startTime  = strtotime($time.$config['night_config']['startWork']);
            $night_endTime = strtotime($time.$config['night_config']['endWork']);
            foreach ($staff_list as $kk=>$vv){
                //获取员工的签到记录
                $filter['staff_id'] = $vv['id'];
                    //判断是否满足第一个时间段
                    $morning_filter = $filter;
                    $morning_filter['sign_time >= ?'] = strtotime($time);
                    $morning_filter['sign_time <= ?'] = $morning_startTime;
                    $morning_list = $signLogModel->getAll('*',$morning_filter);
//                print_r($morning_list);exit;
                    if(is_array($morning_list) && !empty($morning_list)){
                        //获取上午的首次打卡时间
                        $morning_first_time = $morning_list[0]['sign_time'];

                        //判断是否满足第二个时间段
                        $after_filter = $filter;
                        $after_filter['sign_time >= ?'] = $morning_endTime;
                        $after_filter['sign_time <= ?'] = $after_startTime;
                        $after_list = $signLogModel->getAll('*',$after_filter);
                        //   print_r($after_list);exit;
                        if(is_array($after_list) && !empty($after_list)){
                            //判断至少有二次打卡记录
                            $total = count($after_list);
                            // var_dump($total);exit;
                            if($total >= 2){
                                //获取上午下班的打卡时间
                                $morning_end_time = $after_list[0]['sign_time'];
                                //获取下午首次的打卡时间
                                $after_time = array_slice($after_list,-1,1);
                                $after_first_time = $after_time[0]['sign_time'];

                                //获取下午下班打卡次数
                                $all_filter = $filter;
                                $all_filter['sign_time >= ?'] = $after_endTime;
                                $all_filter['sign_time < ?'] = strtotime($time)+86400;
                                $all_list = $signLogModel->getAll('*',$all_filter);
//                                print_r($end_list);exit;
                                if(is_array($all_list) && !empty($all_list)){
                                    //获取下午下班的首次打卡时间
                                    $after_end_time = $all_list[0]['sign_time'];
                                    //如果下班后打卡次数超过2次，就认为是加班
                                    $after_total = count($after_list);
                                    if($after_total >= 2){
                                        //判断是否满足第三个时间段
                                        $night_filter = $filter;
                                        $night_filter['sign_time >= ?'] = $after_endTime;
                                        $night_filter['sign_time <= ?'] = $night_startTime;
                                        $night_filter = $signLogModel->getAll('*',$night_filter);
                                        if(is_array($night_filter) && !empty($night_filter)){
                                            $end_total = count($night_filter);
                                            if($end_total >= 2){
                                                //获取加班的首次打卡时间
                                                $end_time = array_slice($night_filter,-1,1);
                                                $night_first_time = $end_time[0]['sign_time'];
                                                //获取加班下班打卡记录
                                                $end_filter = $filter;
                                                $end_filter['sign_time >= ?'] = $night_endTime;
                                                $end_filter['sign_time < ?'] = strtotime($time)+86400;
                                                $end_list = $signLogModel->getAll('*',$end_filter);
                                                if(is_array($end_list) && !empty($end_list)){
                                                    //获取加班的下班打卡时间
                                                    $endTime = array_slice($end_list,-1,1);
                                                    $night_end_time = $endTime[0]['sign_time'];

                                                    $rs[$kk]['id'] = $vv['id'];
                                                    $rs[$kk]['morning_first_time'] = $morning_first_time;
                                                    $rs[$kk]['morning_end_time'] = $morning_end_time;
                                                    $rs[$kk]['after_first_time'] = $after_first_time;
                                                    $rs[$kk]['after_end_time'] = $after_end_time;
                                                    $rs[$kk]['night_first_time'] = $night_first_time;
                                                    $rs[$kk]['night_end_time'] = $night_end_time;
                                                    $rs[$kk]['sign_config'] = $info['sign_config'];
                                                }
                                            }
                                        }
                                    }else{
                                        $rs[$kk]['id'] = $vv['id'];
                                        $rs[$kk]['morning_first_time'] = $morning_first_time;
                                        $rs[$kk]['morning_end_time'] = $morning_end_time;
                                        $rs[$kk]['after_first_time'] = $after_first_time;
                                        $rs[$kk]['after_end_time'] = $after_end_time;
                                        $rs[$kk]['night_first_time'] = $night_first_time;
                                        $rs[$kk]['night_end_time'] = $night_end_time;
                                        $rs[$kk]['sign_config'] = $info['sign_config'];
                                    }
                                }
                            }
                        }
                    }
            }
//            print_r($rs);exit;
            $staffDomain = new Domain_Building_Staff();
            if(!empty($rs)){
                foreach ($rs as $kk=>$vv){
                    $staffInfo = $staffDomain->getBaseInfo($vv['id']);
                    $rs[$kk]['staff_id'] = $staffInfo['id'];
                    $rs[$kk]['mobile'] = $staffInfo['mobile'];
                    $rs[$kk]['company_id'] = $staffInfo['company_id'];;
                    $rs[$kk]['project_id'] = $projectInfo['id'];
                    $rs[$kk]['morning_first_time'] = $vv['morning_first_time'];
                    $rs[$kk]['morning_end_time'] = $vv['morning_end_time'];
                    $rs[$kk]['after_first_time'] = $vv['after_first_time'];
                    $rs[$kk]['after_end_time'] = $vv['after_end_time'];
                    $rs[$kk]['night_first_time'] = $vv['night_first_time'];
                    $rs[$kk]['night_end_time'] = $vv['night_end_time'];
                    $rs[$kk]['record_time'] = $time;
                    $rs[$kk]['sign_config'] = $vv['sign_config'];
                }
            }
        }
//        print_r($rs);exit;
        //插入到员工打卡明细里
        if(!empty($rs)){
            foreach ($rs as $k =>$vv){
                $sign_filter = $filter;
                $sign_filter['staff_id'] = $vv['id'];
                $sign = $staffSignItemModel->getByWhere($sign_filter,'*');
                if(empty($sign)){
                    $data = array(
                        'company_id' => $vv['company_id'],
                        'project_id' => $vv['project_id'],
                        'staff_id' => $vv['staff_id'],
                        'morning_first_time' => $vv['morning_first_time'],
                        'morning_end_time' => $vv['morning_end_time'],
                        'after_first_time' => $vv['after_first_time'],
                        'after_end_time' => $vv['after_end_time'],
                        'night_first_time' => $vv['night_first_time'],
                        'night_end_time' => $vv['night_end_time'],
                        'sign_config' => $vv['sign_config'],
                        'record_time' => strtotime($vv['record_time']),
                        'create_time' => time(),
                        'last_modify' => time(),
                        'cost' => 1,
                        'status' => 'active',
                    );
//            print_r($data);exit;
                    $res = $staffSignItemModel->insert($data);
                    if( !$res){
                        return false;
                    }
                }else{
                    $data = array('last_modify' => time(), 'status' => 'absent');
                    $res = $staffSignItemModel->update($sign['id'],$data);
                    if( !$res){
                        return false;
                    }
                }
            }
        }
    }
    //获取打卡异常人员
    public function absent($projectInfo,$filter){
        $projectStatisticsModel = new Model_Building_ProjectStatistics();
        $staffSignItemModel = new Model_Building_ProjectStaffSign();
        $signLogModel = new Model_Building_SignLog();

        //获取当前时间项目下属人员
        $info = $projectStatisticsModel->getByWhere($filter,'*');
        $staff_list = json_decode($info['staff_info'],true);
        if(is_array($staff_list) && !empty($staff_list)){
            //过滤掉未签到的
            $sign_staff_list = array();
            foreach ($staff_list as $key=>$value){
                $sign_filter = array(
                    'company_id' => $filter['company_id'],
                    'project_id' => $filter['project_id'],
                    'staff_id' => $value['id'],
                    'record_time' => $filter['record_time'],
                );
                //获取有签到记录的员工
                $sing_info = $signLogModel->getAll('*',$sign_filter);
                if(!empty($sing_info)){
                    $sign_staff_list[]= $value;
                }
            }
//            print_r($sign_staff_list);exit;
            //从有打卡记录的人员中获取签到异常的
            $absent_staff_list = array();
            foreach ($sign_staff_list as $key=>$value){
                $filter['staff_id'] = $value['id'];
                //查询是否签到正常
                $activeInfo = $staffSignItemModel->getByWhere($filter,'*');
                if(empty($activeInfo)){
                    $absent_staff_list[] = $value['id'];
                }
            }
//            print_r($absent_staff_list);exit;
            //时间段打卡时间
            $morning_first_time = 0;
            $morning_end_time = 0;
            $after_first_time = 0;
            $after_end_time = 0;
            $night_first_time = 0;
            $night_end_time = 0;
            //获取签到异常人员打卡时间
            //当前时间
            $time = date("Y-m-d",$filter['record_time']);
            if(is_array($absent_staff_list) && !empty($absent_staff_list)){
                $rs = array();
                //获取项目设置的打卡时间
                $config = json_decode($info['sign_config'],true);
                //获取第一时间段打卡设置
                $morning_startTime  = strtotime($time.$config['day_config']['startWork']);
                $morning_endTime = strtotime($time.$config['day_config']['endWork']);
                //获取第二时间段打卡设置
                $after_startTime  = strtotime($time.$config['after_config']['startWork']);
                $after_endTime = strtotime($time.$config['after_config']['endWork']);
                //获取第三时间段打卡设置
                $night_startTime  = strtotime($time.$config['night_config']['startWork']);
                $night_endTime = strtotime($time.$config['night_config']['endWork']);

                foreach ($absent_staff_list as $kk=>$vv){
                    //获取员工的签到记录
                    $filter['staff_id'] = $vv;
                    //判断是否满足第一个时间段
                    $morning_filter = $filter;
                    $morning_filter['sign_time >= ?'] = strtotime($time);
                    $morning_filter['sign_time <= ?'] = $morning_startTime;
                    $morning_list = $signLogModel->getAll('*',$morning_filter);
                 //   print_r($morning_list);exit;
                    if(is_array($morning_list) && !empty($morning_list)){
                        //获取上午的首次打卡时间
                        $morning_first_time = $morning_list[0]['sign_time'];
                    }
                    //判断是否满足第二个时间段
                    $after_filter = $filter;
                    $after_filter['sign_time >= ?'] = $morning_endTime;
                    $after_filter['sign_time <= ?'] = $after_startTime;
                    $after_list = $signLogModel->getAll('*',$after_filter);
                 //      print_r($after_list);exit;
                    if(is_array($after_list) && !empty($after_list)){
                        //获取上午下班打卡时间
                        $morning_end_time = $after_list[0]['sign_time'];
                        //判断至少有二次打卡记录
                        $total = count($after_list);
                        if($total >= 2){
                            //获取下午首次的打卡时间
                            $after_time = array_slice($after_list,-1,1);
                            $after_first_time = $after_time[0]['sign_time'];
                        }
                    }
                    //判断是否满足第三个时间段
                    $night_filter = $filter;
                    $night_filter['sign_time >= ?'] = $after_endTime;
                    $night_filter['sign_time <= ?'] = $night_startTime;
                    $night_list = $signLogModel->getAll('*',$night_filter);
                 //   print_r($night_list);exit;
                    if(is_array($night_list) && !empty($night_list)){
                        //获取下午下班的首次打卡时间
                        $after_end_time = $night_list[0]['sign_time'];
                        //判断至少有二次打卡记录
                        $after_total = count($night_list);
                        if($after_total >= 2){
                            //获取加班上班的打卡时间
                            $night_time = array_slice($night_list,-1,1);
                            $night_first_time = $night_time[0]['sign_time'];
                        }
                    }
                    //获取加班下班打卡记录
                    $end_filter = $filter;
                    $end_filter['sign_time >= ?'] = $night_endTime;
                    $end_filter['sign_time < ?'] = strtotime($time)+86400;
                    $end_list = $signLogModel->getAll('*',$end_filter);
                    if(is_array($end_list) && !empty($end_list)){
                        $end_time = array_slice($end_list,-1,1);
                        $night_end_time = $end_time[0]['sign_time'];
                    }

                    $rs[$kk]['id'] = $vv;
                    $rs[$kk]['morning_first_time'] = $morning_first_time;
                    $rs[$kk]['morning_end_time'] = $morning_end_time;
                    $rs[$kk]['after_first_time'] = $after_first_time;
                    $rs[$kk]['after_end_time'] = $after_end_time;
                    $rs[$kk]['night_first_time'] = $night_first_time;
                    $rs[$kk]['night_end_time'] = $night_end_time;
                    $rs[$kk]['sign_config'] = $info['sign_config'];
                    $rs[$kk]['record_time'] = strtotime($time);
                }
//                print_r($rs);exit;
                if(!empty($rs)){
                    //插入到员工打卡明细里
                    foreach ($rs as $k =>$vv){
                        $sign_filter = $filter;
                        $sign_filter['staff_id'] = $vv['id'];
                        $sign = $staffSignItemModel->getByWhere($sign_filter,'*');
                        if(empty($sign)){
                            $data = array(
                                'company_id' => $projectInfo['company_id'],
                                'project_id' => $projectInfo['id'],
                                'staff_id' => $vv['id'],
                                'morning_first_time' => $vv['morning_first_time'],
                                'morning_end_time' => $vv['morning_end_time'],
                                'after_first_time' => $vv['after_first_time'],
                                'after_end_time' => $vv['after_end_time'],
                                'night_first_time' => $vv['night_first_time'],
                                'night_end_time' => $vv['night_end_time'],
                                'sign_config' => $vv['sign_config'],
                                'record_time' => $vv['record_time'],
                                'create_time' => time(),
                                'last_modify' => time(),
                                'cost' => 0,
                                'status' => 'absent',
                            );
                            $res = $staffSignItemModel->insert($data);
                            if( !$res){
                                return false;
                            }
                        } else{
                            $data = array('last_modify' => time(), 'status' => 'absent');
                            $res = $staffSignItemModel->update($sign['id'],$data);
                            if( !$res){
                                return false;
                            }
                        }
                    }
                }

            }
        }
    }

    //未签到人员
    public function never($filter){
        $rs = array();
        $projectStatisticsModel = new Model_Building_ProjectStatistics();
        $staffSignItemModel = new Model_Building_ProjectStaffSign();
        $signLogModel = new Model_Building_SignLog();
        //获取当前时间项目下的人员
        $info = $projectStatisticsModel->getByWhere($filter,'*');
        $staff_list = json_decode($info['staff_info'],true);
        if(is_array($staff_list) && !empty($staff_list)){
            foreach ($staff_list as $key=>$value){
                $filter['staff_id'] = $value['id'];
                //获取当前时间打卡记录
                $sign_list = $signLogModel->getAll('*', $filter);
                if(empty($sign_list)){
                    $sign = $staffSignItemModel->getByWhere($filter,'*');
                    if(empty($sign)){
                        $data = array(
                            'company_id' => $filter['company_id'],
                            'project_id' => $filter['project_id'],
                            'staff_id' => $filter['staff_id'],
                            'sign_config' => $info['sign_config'],
                            'record_time' => $filter['record_time'],
                            'create_time' => time(),
                            'last_modify' => time(),
                            'cost' => '0',
                            'status' => 'never',
                        );
                        $res = $staffSignItemModel->insert($data);
                        if( !$res){
                            return false;
                        }
                    }else{
                        $data = array('last_modify' => time(), 'cost' => '0', 'status' => 'never');
                        $res = $staffSignItemModel->update($sign['id'],$data);
                        if( !$res){
                            return false;
                        }
                    }
                }
            }
        }
    }

    //更新项目当前日期人工信息
    public function manualDay($filter){
        $rs = true;
        $filter['record_time'] = strtotime($filter['record_time']);
        $projectModel = new Model_Building_Project();
        $projectInfo = $projectModel->get($filter['project_id']);
        //正常
        $active = $this->active($projectInfo, $filter);
        if( !$active){
            return false;
        }
        //异常
        $absent = $this->absent($projectInfo, $filter);
        if( !$absent){
            return false;
        }
        //未签到
        $never = $this->never($filter);
        if( !$never){
            return false;
        }
        return $rs;
    }
}









