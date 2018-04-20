<?php
class Domain_Jiafubao_StaffDemand {
	var $model;

	public function __construct() {
		$this->model = new Model_Jiafubao_CompanyStaffDemand();
	}

	//获取详情
    public function getBaseInfo($companyId,$staffId, $cols = '*'){
        $filter = array('company_id' => $companyId,'staff_id'=> $staffId);
        $rs = $this->model->getByWhere($filter,'*');

        if (! $rs){
            return null;
        }else{
            $rs['demand'] = json_decode($rs['demand'],true);
            $rs['good_cuisine'] = json_decode($rs['good_cuisine'],true);
            $rs['work_time'] = json_decode($rs['work_time'],true);
            $rs['create_time'] = date("Y-m-d H:i:s", $rs['create_time']);
            $rs['last_modify'] = date("Y-m-d H:i:s", $rs['last_modify']);
            $rs['work_picture'] = json_decode($rs['work_picture'],true);
        }

        return $rs;
    }

    //更新业务需求
    public function updateDemand($data){
        $filter = array('company_id' => $data['company_id'],'staff_id'=> $data['staff_id']);
        $info = $this->model->getByWhere($filter,'*');
        if(empty($info)){
            //添加
            $rs = $this->model->insert($data);
            if($rs){
                $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
                $houseStaffDomain->checkStaff($data['staff_id']);
            }else{
                throw new LogicException (T('Add failed'), 102);
            }
        }else{
            //更新
            $update = array(
                'demand' => $data['demand'],
                'expected_salary' => $data['expected_salary'],
                'good_cuisine' => $data['good_cuisine'],
                'cook_taste' => $data['cook_taste'],
                'is_home' => $data['is_home'],
                'work_time' => $data['work_time'],
                'is_check' => 'n',
                'last_modify' => $data['last_modify'],
                'work_picture' => $data['work_picture'],
            );
            $rs = $this->model->update($info['id'],$update);
            if(!$rs){
                throw new LogicException (T('Update failed'), 104);
            }else{
                $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
                $houseStaffDomain->checkStaff($data['staff_id']);
            }
        }

        return $rs;
    }

}
