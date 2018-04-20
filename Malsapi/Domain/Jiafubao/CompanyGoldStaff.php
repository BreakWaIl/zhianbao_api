<?php
class Domain_Jiafubao_CompanyGoldStaff {
	var $model;
    var $applyModel;

	public function __construct() {
		$this->model = new Model_Jiafubao_GoldStaff();
        $this->applyModel = new Model_Jiafubao_GoldStaffApply();
	}
    //
    public function getConfig($companyId){
        $rs = array();
        $regToCustomerModel = new Model_Zhianbao_RegulatorToCustomer();
        //获取监管者
        $filter = array('company_id' => $companyId);
        $info = $regToCustomerModel->getByWhere($filter, '*');
        if(!empty($info)){
            $domainShopKV = new Domain_Zhianbao_RegulatorConfig($info['regulator_id']);
            $rs = $domainShopKV->get('GoldStaff');
        }else{
            return false;
        }
        return $rs;
    }
    //获取监管者
    public function getRegulator($companyId){
        $regToCustomerModel = new Model_Zhianbao_RegulatorToCustomer();
        $filter = array('company_id' => $companyId);
        $rs = $regToCustomerModel->getByWhere($filter,'*');
        if( !$rs){
            return false;
        }
        return $rs;
    }
    //获取列表
	public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
        $rs = $this->model->getAll ( 'id,name,trades,experience,skill_level,years', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key=>$value){
            if($value['experience'] == 0){
                $rs[$key]['experience'] = '-';
            }
        }
        return $rs;
	}
	//获取数量
	public function getCount($filter) {
		return $this->model->getCount ( $filter );
	}

	//获取申请详情
    public function applyInfo($applyId, $cols = '*'){
        $rs = array ();
        $id = intval ( $applyId );
        if ($id <= 0) {
            return $rs;
        }
        $rs = $this->applyModel->get ( $id);

        if (! $rs){
            return false;
        }else{
            $rs['create_time'] = date("Y-m-d H:i:s", $rs['create_time']);
            $rs['last_modify'] = date("Y-m-d H:i:s", $rs['last_modify']);
            //获取申请日志
            $logModel = new Model_Jiafubao_GoldStaffApplyLog();
            $filter = array('company_id' => $rs['company_id'], 'staff_id' => $rs['staff_id']);
            $logList = $logModel->getAll('*',$filter);
            $rs['log_list'] = $logList;
        }

        return $rs;
    }
   //获取申请列表
    public function applyGetAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
        $rs = $this->applyModel->getAll ( 'id,name,trades,experience,skill_level,years,status', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key=>$value){
            if($value['experience'] == 0){
                $rs[$key]['experience'] = '-';
            }
        }
        return $rs;
    }
    //获取数量
    public function applyGetCount($filter) {
        return $this->applyModel->getCount ( $filter );
    }
    //查询申请是否存在
    public function checkApply($filter){
        return  $this->applyModel->getByWhere($filter,'*');
    }
    //查询申请是否开启
    public function regConfig($companyId){
        $regToCustomerModel = new Model_Zhianbao_RegulatorToCustomer();
        //获取监管者
        $filter = array('company_id' => $companyId);
        $info = $regToCustomerModel->getByWhere($filter, '*');
        if(!empty($info)){
            $domainShopKV = new Domain_Zhianbao_RegulatorConfig($info['regulator_id']);
            $configInfo = $domainShopKV->get('GoldStaff');
            if(!empty($configInfo)){
                if($configInfo == 'open'){
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    //提交金牌家政员申请
    public function addApply($data){
        $rs = $this->applyModel->insert($data);
        if($rs){
            //添加日志
            $log_data = array(
                'company_id' => $data['company_id'],
                'staff_id' => $data['staff_id'],
                'apply_id' => $rs,
                'content' => '提交金牌家政员申请成功',
                'create_time' => time(),
            );
            $logModel = new Model_Jiafubao_GoldStaffApplyLog();
            $res = $logModel->insert($log_data);
            if( !$res){
                throw new LogicException ( T ( 'Add failed' ) , 102 );
            }
        }
        return $rs;
    }
    //更新金牌家政员申请
    public function updateApply($data,$applyInfo){
        $id = $data['apply_id'];
        unset($data['apply_id']);
        $rs = $this->applyModel->update($id,$data);
        if($rs){
            //插入日志
            $log_data = array(
                'company_id' => $applyInfo['company_id'],
                'staff_id' => $applyInfo['staff_id'],
                'apply_id' => $id,
                'content' => '更新金牌家政员申请',
                'create_time' => time(),
            );
            $logModel = new Model_Jiafubao_GoldStaffApplyLog();
            $res = $logModel->insert($log_data);
            if( !$res){
                throw new LogicException ( T ( 'Add failed' ) , 102 );
            }
        }
        return $rs;
    }

}
