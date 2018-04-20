<?php
class Domain_Jiafubao_StaffInsurance {
	var $model;

	public function __construct() {
		$this->model = new Model_Jiafubao_CompanyStaffInsurance();
	}

	//获取详情
    public function getBaseInfo($staffId, $cols = '*'){
        $rs = array ();
        $id = intval ( $staffId );
        if ($id <= 0) {
            return $rs;
        }

        $rs = $this->model->get ( $id);

        if (! $rs){
            return false;
        }else{
            $rs['end_time'] = date("Y-m-d", $rs['end_time']);
            $rs['create_time'] = date("Y-m-d H:i:s", $rs['create_time']);
            $rs['last_modify'] = date("Y-m-d H:i:s", $rs['last_modify']);
        }

        return $rs;
    }
    //添加保险记录
    public function addRecord($data){
        $filter = array('company_id' =>$data['company_id'],'staff_id' =>$data['staff_id'], 'is_delete' => 'n');
        $info = $this->model->getByWhere($filter,'*');
        if(!empty($info)) {
            throw new LogicException (T('Insurance record exist'), 188);
        }else{
            $rs = $this->model->insert($data);
            if($rs){
//                $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
//                $houseStaffDomain->checkStaff($data['staff_id']);
                $houseStaffModel = new Model_Jiafubao_CompanyHouseStaff();
                $res = $houseStaffModel->update($data['staff_id'], array('last_modify' => time()));
                if(!$res){
                    throw new LogicException (T('Add failed'), 102);
                }
            }else{
                throw new LogicException (T('Add failed'), 102);
            }
        }

        return $rs;
    }
    //更新保险记录
    public function updateRecord($data,$recordInfo){
        $id = intval($data['record_id']);
        unset($data['record_id']);
        $rs = $this->model->update($id,$data);
        if(!$rs){
            return false;
        } else{
//            $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
//            $houseStaffDomain->checkStaff($recordInfo['staff_id']);
            $houseStaffModel = new Model_Jiafubao_CompanyHouseStaff();
            $res = $houseStaffModel->update($recordInfo['staff_id'], array('last_modify' => time()));
            if(!$res){
                throw new LogicException (T('Update failed'), 104);
            }
        }
        return $rs;
    }
    //删除保险记录
    public function deleteRecord($recordId){
        $data = array('is_delete' => 'y','last_modify' => time());
        $rs = $this->model->update($recordId,$data);
        return $rs;
    }
    //获取列表
	public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
		$rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        $StaffModel = new Model_Jiafubao_CompanyHouseStaff();
        foreach ($rs as $key=>$value){
            $staffInfo = $StaffModel->get($value['staff_id']);
            $rs[$key]['staff_name'] = $staffInfo['name'];
            $rs[$key]['end_time'] = date("Y-m-d H:i:s",strtotime($value['end_time']));
        }
		return $rs;
	}
	//获取数量
	public function getCount($filter) {
		return $this->model->getCount ( $filter );
	}
    //获取家政员的保险记录
    public function hashInsurance($companyId,$staffId){
        $filter = array('company_id' => $companyId,'staff_id' => $staffId);
        $rs = $this->model->getAll('*',$filter);
        return $rs;
    }

}
