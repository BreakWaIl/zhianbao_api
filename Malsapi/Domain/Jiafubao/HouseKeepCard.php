<?php
class Domain_Jiafubao_HouseKeepCard {
	var $model;

	public function __construct() {
		$this->model = new Model_Jiafubao_CompanyHouseStaffCard();
	}

	//获取详情
    public function getBaseInfo($cardId, $cols = '*'){
        $rs = array ();
        $id = intval ( $cardId );
        if ($id <= 0) {
            return $rs;
        }

        $rs = $this->model->get ( $id);

        if (! $rs){
            return false;
        }else{
            $rs['end_time'] = date("Y-m-d",$rs['end_time']);
        }

        return $rs;
    }
    //添加家政卡
    public function addCard($data){
        $rs = $this->model->insert($data);
        if($rs){
            $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
            $result = $houseStaffDomain->checkStaff($data['staff_id']);
            if(! $result){
                throw new LogicException (T('Add failed'), 102);
            }
        }else{
            throw new LogicException (T('Add failed'), 102);
        }
        return $rs;
    }
    //更新家政卡
    public function updateCard($data,$cardInfo){
        $id = intval($data['card_id']);
        unset($data['card_id']);
        $rs = $this->model->update($id,$data);
        if(!$rs){
            return false;
        }else{
            $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
            $houseStaffDomain->checkStaff($cardInfo['staff_id']);
        }
        return $rs;
    }
    //删除家政卡
    public function deleteCard($cardId){
        $rs = $this->model->delete($cardId);
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
	//获取家政员的家政卡
    public function hashCard($companyId,$staffId){
        $filter = array('company_id' => $companyId,'staff_id' => $staffId);
        $rs = $this->model->getByWhere($filter,'*');
        return $rs;
    }
    //搜索
    public function srearch($filter){
        $rs = array();
        $staffModel = new Model_Jiafubao_CompanyHouseStaff();
        $list = $staffModel->getAll('id',$filter);
        if(!empty($list)){
            foreach ($list as $kk=>$vv){
                $rs[] = $vv['id'];
            }
        }
        return $rs;
    }

}
