<?php
class Domain_Jiafubao_StaffTrainApply {
	var $model;

	public function __construct() {
		$this->model = new Model_Jiafubao_StaffTrainApply();
	}

	//获取详情
    public function getBaseInfo($applyId, $cols = '*'){
        $rs = array ();
        $id = intval ( $applyId );
        if ($id <= 0) {
            return $rs;
        }

        $rs = $this->model->get ( $id);

        if (! $rs){
            return false;
        }else{
            $rs['work_objective'] = json_decode($rs['work_objective'],true);
            $rs['work_service'] = json_decode($rs['work_service'],true);
            $rs['create_time'] = date("Y-m-d H:i:s", $rs['create_time']);
            $rs['last_modify'] = date("Y-m-d H:i:s", $rs['last_modify']);
            //获取公司信息
            $companyModel = new Model_Zhianbao_Company();
            $companyInfo = $companyModel->get($rs['company_id']);
            $rs['company_name'] = $companyInfo['name'];
            //获取家政员信息
            $staffModel = new Model_Jiafubao_CompanyHouseStaff();
            $staffInfo = $staffModel->get($rs['staff_id']);
            $rs['staff_name'] = $staffInfo['name'];
            $rs['avatar'] = json_decode($staffInfo['avatar'],true);
            $rs['birthday'] = date("Y-m-d", $staffInfo['birthday']);
            $rs['sex'] = $staffInfo['sex'];
            $rs['mobile'] = $staffInfo['mobile'];
            $rs['cardID'] = $staffInfo['cardID'];
            //获取申请日志
            $logModel = new Model_Jiafubao_StaffApplyLog();
            $filter = array('apply_id' => $rs['id'], 'company_id' => $rs['company_id'], 'staff_id' => $rs['staff_id']);
            $rs['apply_log'] = $logModel->getAll('*',$filter);
        }

        return $rs;
    }
    //添加服务培训申请
    public function addApply($data){
        $rs = $this->model->insert($data);
        if($rs){
            //添加日志
            $log_data = array(
                'company_id' => $data['company_id'],
                'staff_id' => $data['staff_id'],
                'apply_id' => $rs,
                'content' => '服务培训报名添加成功',
                'create_time' => time(),
                'last_modify' => time(),
            );
            $logModel = new Model_Jiafubao_StaffApplyLog();
            $res = $logModel->insert($log_data);
            if( !$res){
                throw new LogicException ( T ( 'Add failed' ) , 1020 );
            }
        }
        return $rs;
    }
    //更新服务培训申请
    public function updateApply($data,$applyInfo){
        $id = intval($data['apply_id']);
        unset($data['apply_id']);
        $rs = $this->model->update($id,$data);
        if($rs){
            //更新日志
            $log_data = array(
                'company_id' => $applyInfo['company_id'],
                'staff_id' => $applyInfo['staff_id'],
                'apply_id' => $id,
                'content' => '服务培训报名申请更新成功',
                'create_time' => time(),
                'last_modify' => time(),
            );
            $logModel = new Model_Jiafubao_StaffApplyLog();
            $res = $logModel->insert($log_data);
            if( !$res){
                throw new LogicException ( T ( 'Update failed' ) , 104 );
            }
        }
        return $rs;
    }
    //获取列表
    public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
        $companyModel = new Model_Zhianbao_Company();
        $staffModel = new Model_Jiafubao_CompanyHouseStaff();
        $rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key=>$value){
            $staffInfo = $staffModel->get($value['staff_id']);
            $rs[$key]['staff_name'] = $staffInfo['name'];
            $companyInfo = $companyModel->get($value['company_id']);
            $rs[$key]['company_name'] = $companyInfo['name'];
        }
        return $rs;
    }
    //获取数量
    public function getCount($filter) {
        return $this->model->getCount ( $filter );
    }
    //是否存在未归档的申请详情
    public function detect($filter){
        $filter['status'] = array('wait','active','accept','reject');
        $rs = $this->model->getByWhere($filter,'*');
        return $rs;
    }
    //提交服务培训申请
    public function submitApply($data,$applyInfo){
        $id = intval($data['apply_id']);
        unset($data['apply_id']);
        $rs = $this->model->update($id,$data);
        if($rs){
            //更新日志
            $log_data = array(
                'company_id' => $applyInfo['company_id'],
                'staff_id' => $applyInfo['staff_id'],
                'apply_id' => $id,
                'content' => '服务培训报名提交成功',
                'create_time' => time(),
                'last_modify' => time(),
            );
            $logModel = new Model_Jiafubao_StaffApplyLog();
            $res = $logModel->insert($log_data);
            if( !$res){
                throw new LogicException ( T ( 'Apply failed' ) , 116 );
            }
        }
        return $rs;
    }
    //接收服务培训报名
    public function acceptApply($applyId,$applyInfo){
        $data = array('status' => 'accept', 'last_modify' => time() );
        $rs = $this->model->update($applyId,$data);
        if($rs){
            //更新日志
            $log_data = array(
                'company_id' => $applyInfo['company_id'],
                'staff_id' => $applyInfo['staff_id'],
                'apply_id' => $applyId,
                'content' => '服务培训报名申请已接收，请等待审核处理',
                'create_time' => time(),
                'last_modify' => time(),
            );
            $logModel = new Model_Jiafubao_StaffApplyLog();
            $res = $logModel->insert($log_data);
            if( !$res){
                throw new LogicException ( T ( 'Update failed' ) , 104 );
            }
        }
        return $rs;
    }
    //处理服务培训报名
    public function processApply($applyId,$applyInfo){
        $data = array('status' => 'process', 'last_modify' => time() );
        $rs = $this->model->update($applyId,$data);
        if($rs){
            //更新日志
            $log_data = array(
                'company_id' => $applyInfo['company_id'],
                'staff_id' => $applyInfo['staff_id'],
                'apply_id' => $applyId,
                'content' => '服务培训报名申请已审核完成',
                'create_time' => time(),
                'last_modify' => time(),
            );
            $logModel = new Model_Jiafubao_StaffApplyLog();
            $res = $logModel->insert($log_data);
            if( !$res){
                throw new LogicException ( T ( 'Update failed' ) , 104 );
            }
        }
        return $rs;
    }
    //拒绝服务培训报名
    public function rejectApply($data,$applyInfo){
        $id = $data['apply_id'];
        $rejectRemark = $data['rejectRemark'];
        unset($data['apply_id']);unset($data['rejectRemark']);
        $rs = $this->model->update($id,$data);
        if($rs){
            //更新日志
            $log_data = array(
                'company_id' => $applyInfo['company_id'],
                'staff_id' => $applyInfo['staff_id'],
                'apply_id' => $id,
                'content' => '服务培训报名申请已拒绝，理由：'.$rejectRemark,
                'create_time' => time(),
                'last_modify' => time(),
            );
            $logModel = new Model_Jiafubao_StaffApplyLog();
            $res = $logModel->insert($log_data);
            if( !$res){
                throw new LogicException ( T ( 'Update failed' ) , 104 );
            }
        }
        return $rs;
    }

}
