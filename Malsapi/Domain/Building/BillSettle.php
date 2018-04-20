<?php
class Domain_Building_BillSettle {
    var $model;

    public function __construct() {
        $this->model = new Model_Building_Settle();

    }

    public function getBaseInfo($settleId, $cols = '*') {
        $rs = array ();
        $id = intval ( $settleId );
        if ($id <= 0) {
            return $rs;
        }

        $rs = $this->model->get ( $id);

        if( !$rs){
            return false;
        }else{
            $rs['create_time'] = date("Y-m-d H:i:s",$rs['create_time']);
            $rs['last_modify'] = date("Y-m-d H:i:s",$rs['last_modify']);
            $rs['start_time'] = date("Y-m-d H:i:s",$rs['start_time']);
            $rs['end_time'] = date("Y-m-d H:i:s",$rs['end_time']);
            $projectModel = new Model_Building_Project();
            $staffModel = new Model_Building_Staff();
            $projectInfo = $projectModel->get($rs['project_id']);
            $rs['project_name'] = $projectInfo['name'];
            $staffInfo = $staffModel->get($rs['staff_id']);
            $rs['staff_name'] = $staffInfo['name'];
            $rs['cardID'] = $staffInfo['cardID'];
            $rs['mobile'] = $staffInfo['mobile'];
        }

        return $rs;
    }
    //添加结算单
    public function add($data){
        $filter = array(
            'company_id' => $data['company_id'],
            'project_id' => $data['project_id'],
            'staff_id' => $data['staff_id'],
        );
        $filter['settle_status'] = 'n';
        //判断是否有未处理的结算单
        $info = $this->model->getByWhere($filter,'*');
        if(!empty($info)){
            throw new LogicException ( T ( 'There are untreated Settle' ), 209 );
        }
        //判断是否有异常未处理
        $staffSignItemModel = new Model_Building_ProjectStaffSign();
        $item_filter = array(  'company_id' => $data['company_id'], 'project_id' => $data['project_id'], 'staff_id' => $data['staff_id'], 'status' => 'absent','is_deviant' => 'n');
        $deviantInfo = $staffSignItemModel->getByWhere($item_filter,'*');
        if(!empty($deviantInfo)){
            throw new LogicException ( T ( 'The staff has an unhandled attendance exception' ), 217 );
        }
        //获取员工的人工、借支金额
        $artificial_filter = array('company_id' => $data['company_id'], 'project_id' => $data['project_id'], 'staff_id' => $data['staff_id']);
        $artificialInfo = $this->artificial($artificial_filter);
        if($artificialInfo['total'] == 0){
            throw new LogicException ( T ( 'Add failed' ), 102 );
        }
//print_r($artificialInfo);
        $data['work_day'] = $artificialInfo['total']; //人工
        $total_amount = $artificialInfo['total']  * $data['work_price']; //总计金额
        $borrow_amount = $artificialInfo['billTotal']; //借支金额
        $data['total_amount'] = $total_amount;
        $data['borrow_amount'] = $borrow_amount;
        //余额
        $data['balance_amount'] = $total_amount - $borrow_amount;
        //计算时间
        $data['start_time'] = $artificialInfo['settleTime']['start_time'];
        $data['end_time'] = $artificialInfo['settleTime']['end_time'];
   //    print_r($data);exit;
        $rs = $this->model->insert($data);
        if(! $rs){
            throw new LogicException ( T ( 'Add failed' ), 102 );
        }
        //插入日志
        $SettleLogModel = new Model_Building_SettleLog();
        $log_data = array(
            'company_id' => $data['company_id'],
            'settle_id' => $rs,
            'content' => '生成结算单成功',
            'create_time' => time(),
            'operate_id' => $data['operate_id'],
        );
        $log = $SettleLogModel->insert($log_data);
        if(! $log){
            throw new LogicException ( T ( 'Add failed' ), 102 );
        }

        return $rs;
    }
    //获取项目下员工的人工
    public function artificial($filter){
        $res = array();
        $staffSignModel = new Model_Building_ProjectStaffSign();
        $billStaffModel = new Model_Building_BillStaff();
        $projectToStaffModel = new Model_Building_ProjectToStaff();
        //人天总数
        $total = 0.0;
        $billTotal = 0.00;
        $settle_time = array();
        $rs = array();
        //获取结算单
        $list = $this->model->getAll('*',$filter);
       // print_r($list);exit;
        if(empty($list)){
            //获取加入项目的时间
            $info = $projectToStaffModel->getByWhere($filter,'*');
            //首次生成结算单、获取所有的人工
            $rs = $staffSignModel->getAll('*', $filter);
            foreach ($rs as $key=>$value){
                $total += $value['cost'];
                $rs[$key]['record_time'] = date("Y-m-d",$value['record_time']);
            }
            //获取借支
            $bill = $billStaffModel->getAll('*', $filter);
            foreach ($bill as $key=>$value){
                $billTotal += $value['amount'];
            }
            //获取时间段
            $settle_time['start_time'] = $info['create_time'];
            $settle_time['end_time'] = time();
        }else{
            foreach ($list as $key=>$value){
                $createTime[] = strtotime($value['create_time']);
            }
            //获取最近的结算单时间
            $time = max($createTime);
            $filter['record_time > ?'] = $time;
            $rs = $staffSignModel->getAll('*', $filter);
            foreach ($rs as $key=>$value){
                $total += $value['cost'];
                $rs[$key]['record_time'] = date("Y-m-d",$value['record_time']);
            }
            //获取借支
            $bill_filter = array();
            $bill_filter['company_id'] = $filter['company_id'];
            $bill_filter['project_id'] = $filter['project_id'];
            $bill_filter['staff_id'] = $filter['staff_id'];
            $bill_filter['create_time > ?'] = $time;
            $bill = $billStaffModel->getAll('*', $bill_filter);
            foreach ($bill as $key=>$value){
                $billTotal += $value['amount'];
            }
            //获取时间段
            $settle_time['start_time'] = $time;
            $settle_time['end_time'] = time();
        }
        $res['total'] = $total;
        $res['billTotal'] = $billTotal;
        $res['settleTime'] = $settle_time;
        $res['sign_list'] = $rs;

        return $res;
    }
    public function process($data,$settleInfo){
        $id = intval($data['settle_id']);
        unset($data['settle_id']);
        $rs = $this->model->update ( $id ,$data );
        if( !$rs){
            return false;
        }else{
            //插入日志
            $SettleLogModel = new Model_Building_SettleLog();
            $log_data = array(
                'company_id' => $settleInfo['company_id'],
                'settle_id' => $id,
                'content' => '结算单完成',
                'create_time' => time(),
                'operate_id' => $data['operate_id'],
            );
            $log = $SettleLogModel->insert($log_data);
            if(! $log){
                return false;
            }
            //记录到项目出入帐
            //获取结算余额
            $balance_amount = $settleInfo['balance_amount'];
            //如果余额为负、取绝对值
            if($settleInfo['balance_amount'] < 0.00){
                $balance_amount = abs($settleInfo['balance_amount']);
            }
            $billProjectDomain = new Domain_Building_BillProject();
            $projectModel = new Model_Building_Project();
            $projectInfo = $projectModel->get($settleInfo['project_id']);
            $project_data = array(
                'company_id' => $settleInfo['company_id'],
                'project_id' => $settleInfo['project_id'],
                'type' => 'expenditure',
                'title' => '【新增员工结算单】',
                'amount' => $balance_amount,
                'remark' => $settleInfo['remark'],
                'create_time' => time(),
                'last_modify' => time(),
                'operate_id' => $data['operate_id'],
            );
            $billProjectDomain->add($project_data,$projectInfo);
//            //更新项目开支
//            $projectModel = new Model_Building_Project();
//            $projectInfo = $projectModel->get($settleInfo['project_id']);
//            $project_data = array(
//                'amount' => $projectInfo['amount'] - $settleInfo['balance_amount'],
//                'last_modify' => time(),
//            );
//            $res = $projectModel->update($projectInfo['id'],$project_data);
//            if(! $res){
//                return false;
//            }
        }

        return $rs;
    }

    //获取列表
    public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
        $projectModel = new Model_Building_Project();
        $staffModel = new Model_Building_Staff();
        $rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key=>$value){
            $projectInfo = $projectModel->get($value['project_id']);
            $rs[$key]['project_name'] = $projectInfo['name'];
            $staffInfo = $staffModel->get($value['staff_id']);
            $rs[$key]['staff_name'] = $staffInfo['name'];
            $rs[$key]['cardID'] = $staffInfo['cardID'];
            $rs[$key]['mobile'] = $staffInfo['mobile'];
        }
        return $rs;
    }
    //获取数量
    public function getCount($filter) {
        return $this->model->getCount ( $filter );
    }


//    //员工结算单考勤列表
//    public function getAllStaffSign($filter, $page = 1, $page_size = 20, $orderby = ''){
//        $companyModel = new Model_Zhianbao_Company();
//        $staffModel = new Model_Building_Staff();
//        $projectModel = new Model_Building_Project();
//        $signItemModel = new Model_Building_ProjectStaffSign();
//        $rs = $signItemModel->getAll('id,company_id,project_id,staff_id,morning_first_time,morning_end_time,after_first_time,after_end_time,night_first_time,night_end_time,record_time,cost,type,remark,status,is_deviant', $filter, $page, $page_size, $orderby);
//        foreach ($rs as $key=>$value){
//            $rs[$key]['morning_first_time'] = $value['morning_first_time'] == 0 ? '无':date("Y-m-d H:i:s",$value['morning_first_time']);
//            $rs[$key]['morning_end_time'] = $value['morning_end_time'] == 0 ? '无':date("Y-m-d H:i:s",$value['morning_end_time']);
//            $rs[$key]['after_first_time'] = $value['after_first_time'] == 0 ? '无':date("Y-m-d H:i:s",$value['after_first_time']);
//            $rs[$key]['after_end_time'] = $value['after_end_time'] == 0 ? '无':date("Y-m-d H:i:s",$value['after_end_time']);
//            $rs[$key]['night_first_time'] = $value['night_first_time'] == 0 ? '无':date("Y-m-d H:i:s",$value['night_first_time']);
//            $rs[$key]['night_end_time'] = $value['night_end_time'] == 0 ? '无':date("Y-m-d H:i:s",$value['night_end_time']);
//            $rs[$key]['record_time'] = date("Y-m-d", $value['record_time']);
//            $companyInfo = $companyModel->get($value['company_id']);
//            $rs[$key]['company_name'] = $companyInfo['name'];
//            $staffInfo = $staffModel->get($value['staff_id']);
//            $rs[$key]['staff_name'] = $staffInfo['name'];
//            $projectInfo = $projectModel->get($value['project_id']);
//            $rs[$key]['project_name'] = $projectInfo['name'];
//        }
//        return $rs;
//    }
//    public function getCountStaffSign($filter) {
//        $signItemModel = new Model_Building_ProjectStaffSign();
//        return $signItemModel->getCount ( $filter );
//    }
}
