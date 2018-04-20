<?php
class Domain_Jiafubao_StaffHealthCard {
	var $model;

	public function __construct() {
		$this->model = new Model_Jiafubao_CompanyStaffHealthCard();
	}

	//获取详情
    public function getBaseInfo($healthId, $cols = '*'){
        $rs = array ();
        $id = intval ( $healthId );
        if ($id <= 0) {
            return $rs;
        }

        $rs = $this->model->get ( $id);

        if (! $rs){
            return false;
        }else{
            $staffModel = new Model_Jiafubao_CompanyHouseStaff();
            $staffInfo = $staffModel->get($rs['staff_id']);
            $rs['staff_name'] = $staffInfo['name'];
            $rs['sex'] = $staffInfo['sex'];
            $rs['mobile'] = $staffInfo['mobile'];
            $rs['birthday'] = date("Y-m-d",$staffInfo['birthday']);
            $rs['send_time'] = date("Y-m-d", $rs['send_time']);
            $rs['end_time'] = date("Y-m-d", $rs['end_time']);
            $rs['img_url'] = json_decode($rs['img_url'],true);
            $rs['create_time'] = date("Y-m-d H:i:s", $rs['create_time']);
            $rs['last_modify'] = date("Y-m-d H:i:s", $rs['last_modify']);
        }

        return $rs;
    }
    //添加健康卡
    public function addHealthCard($data){
        $filter = array('company_id' =>$data['company_id'],'staff_id' =>$data['staff_id'], 'status' => 'y');
        $info = $this->model->getByWhere($filter,'*');
        if(!empty($info)){
            throw new LogicException (T('Health card exist'), 186);
        }else{
            $data['card_bn'] = date("Ymd",time()).mt_rand(10000,99999);
            $rs = $this->model->insert($data);
            if($rs){
                $houseStaffModel = new Model_Jiafubao_CompanyHouseStaff();
                $res = $houseStaffModel->update($data['staff_id'], array('last_modify' => time()));
                if(!$res){
                    throw new LogicException (T('Add failed'), 102);
                }
            }else{
                throw new LogicException (T('Add failed'), 102);
            }
//            if($rs){
//                $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
//                $houseStaffDomain->checkStaff($data['staff_id']);
//            }else{
//                throw new LogicException (T('Add failed'), 102);
//            }
        }
        return $rs;
    }
    //更新健康卡
    public function updateHealthCard($data,$healthCardInfo){
        $id = intval($data['health_id']);
        unset($data['health_id']);
        $rs = $this->model->update($id,$data);
        if(!$rs){
            return false;
        }else{
//            $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
//            $houseStaffDomain->checkStaff($healthCardInfo['staff_id']);
            $houseStaffModel = new Model_Jiafubao_CompanyHouseStaff();
            $res = $houseStaffModel->update($healthCardInfo['staff_id'], array('last_modify' => time()));
            if(!$res){
                throw new LogicException (T('Update failed'), 104);
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
            $rs[$key]['send_time'] = date("Y-m-d",$value['send_time']);
            $rs[$key]['end_time'] = date("Y-m-d",strtotime($value['end_time']));
            $rs[$key]['img_url'] = json_decode($value['img_url'],true);
            $companyInfo = $companyModel->get($value['company_id']);
            $rs[$key]['company_name'] = $companyInfo['name'];
            $staffInfo = $staffModel->get($value['staff_id']);
            $rs[$key]['staff_info']['name'] = $staffInfo['name'];
        }
        return $rs;
    }
    //获取数量
    public function getCount($filter) {
        return $this->model->getCount ( $filter );
    }
    //作废健康证
    public function banHealthCard($healthId){
        $data = array('status' => 'n', 'last_modify' => time());
        $rs = $this->model->update($healthId,$data);
        return $rs;
    }
    //获取家政员的健康卡
    public function hashHealthCard($companyId,$staffId){
        $filter = array('company_id' => $companyId,'staff_id' => $staffId,'status' => 'y');
        $rs = $this->model->getByWhere($filter,'*');
        return $rs;
    }

}
