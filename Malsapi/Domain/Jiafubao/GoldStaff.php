<?php
class Domain_Jiafubao_GoldStaff {
	var $model;

	public function __construct() {
		$this->model = new Model_Jiafubao_GoldStaff();
	}

	//获取详情
    public function getBaseInfo($goldId, $cols = '*'){
        $rs = array ();
        $id = intval ( $goldId );
        if ($id <= 0) {
            return $rs;
        }

        $rs = $this->model->get ( $id);

        if (! $rs){
            return false;
        }else{
            if($rs['experience'] == 0){
                $rs['experience'] = '-';
            }
        }

        return $rs;
    }
    //获取金牌家政员列表
	public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
        $rs = $this->model->getAll ( 'id,name,trades,birthday,experience,mobile,skill_level,company_name,years', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key=>$value){
            if($value['experience'] == 0){
                $rs[$key]['experience'] = '-';
            }
        }
        return $rs;
	}
	//获取金牌家政员数量
	public function getCount($filter) {
		return $this->model->getCount ( $filter );
	}

    //获取金牌家政员申请列表
    public function applyGetAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
        $applyGoldStaffModel = new Model_Jiafubao_GoldStaffApply();
        $rs = $applyGoldStaffModel->getAll ( 'id,name,birthday,trades,experience,mobile,skill_level,company_name,years,status', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key=>$value){
            if($value['experience'] == 0){
                $rs[$key]['experience'] = '-';
            }
        }
        return $rs;
    }
    //获取金牌家政员申请数量
    public function applyGetCount($filter) {
        $applyGoldStaffModel = new Model_Jiafubao_GoldStaffApply();
        return $applyGoldStaffModel->getCount ( $filter );
    }
    //拒绝申请
    public function refuseApply($filter,$applyInfo){
        $rs = array();
        $applyId = $applyInfo['id'];
        //判断该申请公司和监管者关系
        $regulatorToCustomerModel = new Model_Zhianbao_RegulatorToCustomer();
        $info = $regulatorToCustomerModel->getAll('*',$filter);
        if(empty($info)){
            return false;
        }else{
            $applyGoldStaffModel = new Model_Jiafubao_GoldStaffApply();
            $data = array('status' => 'refuse', 'last_modify' => time());
            $rs = $applyGoldStaffModel->update($applyId,$data);
            if( !$rs){
                throw new LogicException ( T ( 'Update failed' ), 104 );
            }
            //添加日志
            $log_data = array(
                'company_id' => $applyInfo['company_id'],
                'staff_id' => $applyInfo['staff_id'],
                'apply_id' => $applyId,
                'content' => '拒绝金牌家政员申请',
                'create_time' => time(),
            );
            $logModel = new Model_Jiafubao_GoldStaffApplyLog();
            $res = $logModel->insert($log_data);
            if( !$res){
                throw new LogicException ( T ( 'Update failed' ) , 104 );
            }
        }
        return $rs;
    }
    //同意申请
    public function agreeApply($regulatorId,$applyId,$applyInfo){
        $applyGoldStaffModel = new Model_Jiafubao_GoldStaffApply();
        $data = array('status' => 'success', 'last_modify' => time());
        $rs = $applyGoldStaffModel->update($applyId,$data);
        if( !$rs){
            throw new LogicException ( T ( 'Update failed' ), 104 );
        }
        //添加日志
        $log_data = array(
            'company_id' => $applyInfo['company_id'],
            'staff_id' => $applyInfo['staff_id'],
            'apply_id' => $applyId,
            'content' => '同意金牌家政员申请',
            'create_time' => time(),
        );
        $logModel = new Model_Jiafubao_GoldStaffApplyLog();
        $res = $logModel->insert($log_data);
        if( !$res){
            throw new LogicException ( T ( 'Update failed' ) , 104 );
        }
        //添加到金牌家政员
        $gold_data = array(
            'regulator_id' => $regulatorId,
            'name' => $applyInfo['name'],
            'trades' => $applyInfo['trades'],
            'experience' => $applyInfo['experience'],
            'birthday' => $applyInfo['birthday'],
            'mobile' => $applyInfo['mobile'],
            'house_keep_card' => $applyInfo['house_keep_card'],
            'bank_card' => $applyInfo['bank_card'],
            'cardID' => $applyInfo['cardID'],
            'skill_level' => $applyInfo['skill_level'],
            'education' => $applyInfo['education'],
            'company_name' => $applyInfo['company_name'],
            'remark' => $applyInfo['remark'],
            'years' => $applyInfo['years'],
        );
        $id = $this->model->insert($gold_data);
        if( !$id){
            throw new LogicException ( T ( 'Update failed' ) , 104 );
        }
        return $rs;
    }
}
