<?php
class Domain_Building_Attendance {
    var $model;

    public function __construct() {
        $this->model = new Model_Building_SignLog();

    }

    //扫码获取信息并打卡
    public function getQrCodeInfo($staffUrl,$companyId,$operateId) {
        $rs = array();
        $staffDomain = new Domain_Building_Staff();
        $projectToStaffModel = new Model_Building_ProjectToStaff();
        $projectDomain = new Domain_Building_Project();
        //获取参数
        parse_str(strstr(rawurldecode($staffUrl),'projectId'),$array);
        $projectId = intval($array['projectId']);
        $staffId = intval($array['staffId']);
        if($projectId > 0 && $staffId > 0){
            //获取员工信息
            $rs = $staffDomain->getBaseInfo($staffId);
            //判断是否在此公司下
            if($rs['company_id'] != $companyId){
                throw new LogicException (T('Company not exists'), 100);
            }
            //获取项目信息
            $projectInfo = $projectDomain->getBaseInfo($projectId);
       //     print_r($projectInfo);exit;
            //判断项目是否已完成
            if($projectInfo['status'] == 'finish') {
                throw new LogicException (T('Project finish'), 211);
            }
            //判断该员工是否加入项目
            $filter = array('company_id'=> $companyId, 'project_id'=> $projectId, 'staff_id'=>$staffId,);
            $projectToStaff = $projectToStaffModel->getByWhere($filter,'*');
            if(empty($projectToStaff)){
                throw new LogicException (T('No staff in this project'), 207);
            }
            //判断员工状态
            if($projectToStaff['status'] == 'n'){
                throw new LogicException (T('Staff not join this project'), 208);
            }
            //获取员工所在项目下的班组
            $staffToCatModel = new Model_Building_StaffToCat();
            $catModel = new Model_Building_Cat();
            $cat = array();
            $list = $staffToCatModel->getAll('*',$filter);
            foreach ($list as $kk=>$vv){
                $catInfo = $catModel->get($vv['cat_id']);
                $cat[$kk]['cat_id'] = $vv['cat_id'];
                $cat[$kk]['cat_name'] = $catInfo['name'];
            }
            $projectInfo['catInfo'] = $cat;
            $rs['projectInfo'] = $projectInfo;
            $recordTime = strtotime(date("Ymd"));
            //插入员工打卡记录
            $sign_data = array(
                'company_id' => $companyId,
//                'cat_id' => $catInfo,
                'cat_id' => json_encode($rs['cat_name']),
                'project_id' => $projectId,
                'staff_id' => $staffId,
                'sign_time' => time(),
                'last_modify' => time(),
                'sign_config' => json_encode($projectInfo['sign_config']),
                'record_time' => $recordTime,
                'operate_id'=> $operateId,
            );
            //print_r($sign_data);exit;
            $res = $this->model->insert($sign_data);
            if( !$res){
                throw new LogicException (T('Add failed'), 102);
            }
        }else{
            throw new LogicException (T('Project not exists'), 192);
        }
     //   print_r($rs);exit;
        return $rs;
    }

    //获取当前日期考勤记录
    public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
        $companyModel = new Model_Zhianbao_Company();
        $staffModel = new Model_Building_Staff();
        $projectModel = new Model_Building_Project();
        $staffSignItemModel = new Model_Building_ProjectStaffSign();
        $rs = $this->model->getAll ( 'id,company_id,project_id,staff_id,sign_time,record_time', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key=>$value){
            $rs[$key]['sign_time'] = date("Y-m-d H:i:s",$value['sign_time']);
            $companyInfo = $companyModel->get($value['company_id']);
            $rs[$key]['company_name'] = $companyInfo['name'];
            $staffInfo = $staffModel->get($value['staff_id']);
            $rs[$key]['staff_name'] = $staffInfo['name'];
            $projectInfo = $projectModel->get($value['project_id']);
            $rs[$key]['project_name'] = $projectInfo['name'];
            //获取来源
            $to_filter = array( 'company_id' => $filter['company_id'], 'project_id' => $filter['project_id'], 'staff_id' => $value['staff_id'], 'record_time' => $value['record_time'] );
            $signItemInfo = $staffSignItemModel->getByWhere($to_filter,'type');
            if(!empty($signItemInfo)){
                $rs[$key]['type'] = $signItemInfo['type'];
            }else{
                $rs[$key]['type'] = 'h5';
            }
        }
        return $rs;
    }
    //获取数量
    public function getCount($filter) {
        return $this->model->getCount ( $filter );
    }

    //获取项目下的班组
    public function getAllProjectCat($filter){
        $rs = array();
        $projectToCatModel = new Model_Building_ProjectToCat();
        $catModel = new Model_Building_Cat();
        //获取项目下的班组
        $list = $projectToCatModel->getAll('*',$filter);
        foreach ($list as $kk=>$vv){
            $rs[$kk]['cat_id'] = $vv['cat_id'];
            $catInfo = $catModel->get($vv['cat_id']);
            $rs[$kk]['cat_name'] = $catInfo['name'];
        }
        return $rs;
    }
    //获取近期项目班组的考勤概况
    public function getAllProjectCatSign($projectInfo,$filter, $page = 1, $page_size = 20){
        $beginTime = $filter['beginTime'];
        $endTime = $filter['endTime'];
        $catId = $filter['cat_id'];
        unset($filter['beginTime']);unset($filter['endTime']);unset($filter['cat_id']);
        $filter['sign_time > ?'] = $beginTime;
        $filter['sign_time < ?'] = $endTime;
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
        //数组排序
        $rs = array_values($lastTime);
        //获取每天项目的统计信息
        $projectToStaffModel = new Model_Building_ProjectToStaff();
        $projectStatisticsModel = new Model_Building_ProjectStatistics();
        foreach ($rs as $key=>$value){
            $filter['sign_time > ?'] = $value['start_time'];
            $filter['sign_time < ?'] = $value['stop_time'];
            //获取项目人数统计
            $log_filter = array('company_id' =>  $projectInfo['company_id'], 'project_id' =>  $projectInfo['id'], 'record_time' => $value['start_time']);
            $staffTotal = $projectStatisticsModel->getByWhere($log_filter,'staff_total,create_time,last_modify');
//               print_r($staffTotal);exit;
            if(!empty($staffTotal)){
                $projectStaffTotal = $staffTotal['staff_total'];
            }else{
                $projectStaffTotal = 0;
                //获取项目人数统计
                $todayTime = strtotime(date("Ymd"));
                if($value['start_time'] == $todayTime){
                    $today_filter = array('company_id' =>  $projectInfo['company_id'], 'project_id' => $projectInfo['id'], 'status' => 'y');
                    $projectStaffTotal = $projectToStaffModel->getCount($today_filter);
                }
            }
            //获取该班组员工签到情况
            $signInfoTotal = $this->catSignStaffTotal($filter['company_id'],$filter['project_id'],$catId,$value['start_time']);
//            print_r($signInfoTotal);exit;

            $rs[$key]['project_id'] = $projectInfo['id'];
            $rs[$key]['project_name'] = $projectInfo['name'];
            $rs[$key]['start_time'] = date("Y-m-d",$value['start_time']);
            $rs[$key]['stop_time'] = date("Y-m-d",$value['stop_time']);
            $rs[$key]['staffTotal'] = $projectStaffTotal; //项目总人数
            $rs[$key]['signTotal'] = $signInfoTotal['activeTotal']; //正常签到人数
            $rs[$key]['deviantTotal'] = $signInfoTotal['absentTotal']; //异常人数
            $rs[$key]['create_time'] = $staffTotal['create_time'] == 0 ? '-' : date("Y-m-d H:i:s",$staffTotal['create_time']);
            $rs[$key]['last_modify'] = $staffTotal['last_modify'] == 0 ? '-' : date("Y-m-d H:i:s",$staffTotal['last_modify']);
        }

        $start = ($page-1) * $page_size;
        $list = array_slice($rs, $start, $page_size);
        $list['day'] = $day;
        return $list;
    }
    //获取该班组员工签到情况
    public function catSignStaffTotal($companyId,$projectId,$catId,$startTime){
        $result = array();
        $filter = array('company_id' => $companyId, 'project_id' => $projectId, 'cat_id' => $catId);
        $staffToCatModel = new Model_Building_StaffToCat();
        //获取该分类的下的员工
        $rs = array();
        $list = $staffToCatModel->getAll('*',$filter);
        foreach ($list as $key=>$value){
            $rs[] = $value['staff_id'];
        }
        $activeTotal = 0;
        $absentTotal = 0;
        //查询员工在项目当前时期的考勤状态
        $item_filter = array( 'company_id' => $companyId, 'project_id' => $projectId, 'record_time' =>$startTime );
        $staffSignItemModel = new Model_Building_ProjectStaffSign();
        foreach ($rs as $kk=>$vv){
            $item_filter['staff_id'] = $vv;
            //获取该人员的签到信息
            $info = $staffSignItemModel->getByWhere($item_filter,'*');
            //正常人数
            if($info['status'] == 'active'){
                ++$activeTotal;
            }
            //异常人数
            if($info['status'] == 'absent'){
                ++$absentTotal;
            }
        }

        $result['activeTotal'] = $activeTotal;
        $result['absentTotal'] = $absentTotal;
        return $result;
    }

    //获取人员签到状态列表（正常、异常、未签到）
    public function getAllSignStaff($companyInfo,$projectInfo,$filter,$page = 1, $page_size = 20, $orderby = '', $staffIds){
        $staffSignItemModel = new Model_Building_ProjectStaffSign();
        $staffModel = new Model_Building_Staff();
        $recordTime = $filter['time'];unset($filter['time']);
        $filter['record_time'] = strtotime($recordTime);
        $rs = $staffSignItemModel->getAll('*', $filter, $page, $page_size, $orderby);
        $list = array();
        foreach ($rs as $key=>$value){
            if(in_array($value['staff_id'],$staffIds)){
                $list[] = $value;
            }
        }
        foreach ($list as $kk=>$vv){
            $list[$kk]['morning']['first_time'] = $value['morning_first_time'] == 0 ? '无':date("Y-m-d H:i:s",$value['morning_first_time']);
            $list[$kk]['morning']['end_time'] = $value['morning_end_time'] == 0 ? '无': date("Y-m-d H:i:s",$value['morning_end_time']);
            $list[$kk]['after']['first_time'] = $value['after_first_time'] == 0 ? '无': date("Y-m-d H:i:s",$value['after_first_time']);
            $list[$kk]['after']['end_time'] = $value['after_end_time'] == 0 ? '无': date("Y-m-d H:i:s",$value['after_end_time']);
            $list[$kk]['night']['first_time'] = $value['night_first_time'] == 0 ? '无': date("Y-m-d H:i:s",$value['night_first_time']);
            $list[$kk]['night']['end_time'] = $value['night_end_time'] == 0 ? '无': date("Y-m-d H:i:s",$value['night_end_time']);
            //获取员工信息
            $staffInfo = $staffModel->get($vv['staff_id']);
            $list[$kk]['name'] = $staffInfo['name'];
            $list[$kk]['mobile'] = $staffInfo['mobile'];
            $list[$kk]['company_id'] = $staffInfo['company_id'];
            $list[$kk]['company_name'] = $companyInfo['name'];
            $list[$kk]['project_id'] = $projectInfo['id'];
            $list[$kk]['project_name'] = $projectInfo['name'];
        }
        return $list;
    }
    //获取数量
    public function getSignStaffCount($filter,$staffIds) {
        $staffSignItemModel = new Model_Building_ProjectStaffSign();
        $recordTime = $filter['time'];
        unset($filter['time']);
        $filter['record_time'] = strtotime($recordTime);
        $rs = $staffSignItemModel->getAll('*', $filter);
        $list = array();
        foreach ($rs as $key=>$value){
            if(in_array($value['staff_id'],$staffIds)){
                $list[] = $value;
            }
        }
        $total = COUNT($list);

//        $rs = $staffSignItemModel->getCount ( $filter );
        return $total;
    }
    //获取项目班组下的人员
    public function catToStaff($filter,$catId){
        $rs = array();
        $staffToCatModel = new Model_Building_StaffToCat();
        $filter['cat_id'] = $catId;
        $list = $staffToCatModel->getAll('*', $filter);
        foreach ($list as $kk=>$vv){
            $rs[] = $vv['staff_id'];
        }
        return $rs;
    }
    //处理异常
    public function processDeviant($data,$operateId){
        $rs = array();
        $staffSignItemModel = new Model_Building_ProjectStaffSign();
        $sign_filter = array(
            'company_id' => $data['company_id'],
            'project_id' => $data['project_id'],
            'staff_id' => $data['staff_id'],
            'record_time' => strtotime($data['record_time']),
            'status' => 'absent',
        );
        //判断异常是否存在
        $checkProcess = $staffSignItemModel->getByWhere($sign_filter,'*');
        if(empty($checkProcess)){
            return false;
        }else{
            if($checkProcess['is_deviant'] == 'y'){
                //判断异常是否处理
                throw new LogicException (T('Deviant have been processed'), 196);
            }
            $update_data = array(
                'cost' => $data['cost'],
                'remark' => $data['remark'],
                'is_deviant' => 'y',
                'last_modify' => time(),
                'operate_id' => $operateId,
            );
            $rs = $staffSignItemModel->update($checkProcess['id'],$update_data);
            if( !$rs){
                return false;
            }
        }
        return $rs;
    }

    //获取异常处理结果
    public function deviantInfo($filter){
        $staffSignItemModel = new Model_Building_ProjectStaffSign();
        $companyModel = new Model_Zhianbao_Company();
        $staffModel = new Model_Building_Staff();
        $projectModel = new Model_Building_Project();
        $info = $staffSignItemModel->getByWhere($filter,'*');
        if(!empty($info)){
            $info['create_time'] = date("Y-m-d H:i:s",$info['create_time']);
            $info['last_modify'] = date("Y-m-d H:i:s",$info['last_modify']);
            $info['record_time'] = date("Y-m-d",$info['record_time']);
            $companyInfo = $companyModel->get($info['company_id']);
            $info['company_name'] = $companyInfo['name'];
            $staffInfo = $staffModel->get($info['staff_id']);
            $info['staff_name'] = $staffInfo['name'];
            $projectInfo = $projectModel->get($info['project_id']);
            $info['project_name'] = $projectInfo['name'];
        }
        return $info;
    }
    //系统补签
    public function signReplenish($data){
        $staffSignItemModel = new Model_Building_ProjectStaffSign();
        $projectStatisticsModel = new Model_Building_ProjectStatistics();
        $staffDomain = new Domain_Building_Staff();
        $filter = array(
            'company_id' => $data['company_id'],
            'project_id' => $data['project_id'],
            'staff_id' => $data['staff_id'],
            'record_time' => strtotime($data['record_time']),
        );
        //获取未签到的人员记录
        $info = $staffSignItemModel->getByWhere($filter,'*');
        //print_r($info);exit;
        if(!empty($info)){
            //判断员工签到状态
            if($info['status'] == 'never'){
                //获取项目信息设置
                $statistics_filter = array('company_id' => $data['company_id'], 'project_id' => $data['project_id'], 'record_time' => strtotime($data['record_time']));
                $staffSignInfo = $projectStatisticsModel->getByWhere($statistics_filter,'sign_config');
                //获取项目设置的打卡时间
                $config = json_decode($staffSignInfo['sign_config'],true);
                $time = $data['record_time'];
                //获取第一时间段打卡设置
                $morning_startTime  = strtotime($time.$config['day_config']['startWork']);
                $morning_endTime = strtotime($time.$config['day_config']['endWork']);
                //获取第二时间段打卡设置
                $after_startTime  = strtotime($time.$config['after_config']['startWork']);
                $after_endTime = strtotime($time.$config['after_config']['endWork']);
                //获取第三时间段打卡设置
                $night_startTime  = strtotime($time.$config['night_config']['startWork']);
                $night_endTime = strtotime($time.$config['night_config']['endWork']);
                $sign_time = array($morning_startTime,$morning_endTime,$after_startTime,$after_endTime,$night_startTime,$night_endTime);
                //获取员工信息
                $cat_info = $staffDomain->getBaseInfo($data['staff_id']);
                //插入到签到日志中记录
             //   print_r($data);exit;
                foreach ($sign_time as $value){
                    $log_data = array(
                        'company_id' => $data['company_id'],
                        'cat_id' => json_encode($cat_info['cat_name']),
                        'project_id' => $data['project_id'],
                        'staff_id' => $data['staff_id'],
                        'sign_time' => $value,
                        'sign_config' => $staffSignInfo['sign_config'],
                        'last_modify' => time(),
                        'record_time' => strtotime($data['record_time']),
                        'operate_id' => $data['operate_id'],
                    );
                    $log = $this->model->insert($log_data);
                    if( !$log){
                        return false;
                    }
                }
                //更新员工签到明细表
                $update_data = array();
                $update_data['morning_first_time'] = $morning_startTime;
                $update_data['morning_end_time'] = $morning_endTime;
                $update_data['after_first_time'] = $after_startTime;
                $update_data['after_end_time'] = $after_endTime;
                $update_data['night_first_time'] = $night_startTime;
                $update_data['night_end_time'] = $night_endTime;
                $update_data['cost'] = '1';
                $update_data['type'] = 'system';
                $update_data['remark'] = $data['remark'];
                $update_data['status'] = 'active';
                $update_data['last_modify'] = time();
                $res = $staffSignItemModel->update($info['id'],$update_data);
                if( !$res){
                    throw new LogicException (T('Add failed'), 102);
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

}
