<?php
class Domain_Jiafubao_HouseStaffCheck {
	var $model;

	public function __construct() {
		$this->model = new Model_Jiafubao_HouseStaffCheck();
	}

	//获取详情
    public function getBaseInfo($id, $cols = '*'){
        $rs = $this->model->get ( $id , $cols);

        return $rs;
    }

    //获取职业技能列表
	public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
		$rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
		return $rs;
	}
	//获取数量
	public function getCount($filter) {
		return $this->model->getCount ( $filter );
	}
	//通过审核
    public function agreeCheckStaff($checkInfo){
        $data = array(
            'status' => 1,
            'last_modify' => time(),
        );
        $rs = $this->model->update($checkInfo['id'],$data);
        if(! $rs){
            throw new LogicException (T('Create failed'), 144);
        }
        $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
        $rs = $houseStaffDomain->checkStaff($checkInfo['staff_id']);
        return $rs;
    }

    //不通过审核
    public function disagreeCheckStaff($checkInfo){
        $data = array(
            'status' => 2,
            'last_modify' => time(),
        );
        $rs = $this->model->update($checkInfo['id'],$data);
        if(! $rs){
            throw new LogicException (T('Create failed'), 144);
        }
        return $rs;
    }
    //提交审核
    public function refer($data){
        $rs = $this->model->insert($data);
        return $rs;
    }

}
