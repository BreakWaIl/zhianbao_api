<?php
class Domain_Jiafubao_StaffAbilityCert {
	var $model;

	public function __construct() {
		$this->model = new Model_Jiafubao_CompanyStaffAbilityCert();
	}

	//获取详情
    public function getBaseInfo($certId, $cols = '*'){
        $rs = array ();
        $id = intval ( $certId );
        if ($id <= 0) {
            return $rs;
        }

        $rs = $this->model->get ( $id);

        if (! $rs){
            return false;
        }else{
            $rs['img_url'] = json_decode($rs['img_url'],true);
            $rs['train_time'] = date("Y-m-d", $rs['train_time']);
            $rs['create_time'] = date("Y-m-d H:i:s", $rs['create_time']);
            $rs['last_modify'] = date("Y-m-d H:i:s", $rs['last_modify']);
            $staffModel = new Model_Jiafubao_CompanyHouseStaff();
            $staffInfo = $staffModel->get($rs['staff_id']);
            $rs['staff_info']['name'] = $staffInfo['name'];
            $rs['staff_info']['avatar'] = json_decode($staffInfo['avatar'],true);
            $rs['staff_info']['birthday'] = date("Y-m-d",$staffInfo['birthday']);
            $rs['staff_info']['sex'] = $staffInfo['sex'];
            $rs['staff_info']['cardID'] = $staffInfo['cardID'];
            $rs['staff_info']['mobile'] = $staffInfo['mobile'];
        }

        return $rs;
    }
    //添加专项能力证书
    public function addAbilityCert($data){
        $rs = $this->model->insert($data);
        if($rs){
//            $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
//            $houseStaffDomain->checkStaff($data['staff_id']);
            $houseStaffModel = new Model_Jiafubao_CompanyHouseStaff();
            $res = $houseStaffModel->update($data['staff_id'], array('last_modify' => time()));
            if(!$res){
                throw new LogicException (T('Add failed'), 102);
            }
        }else{
            throw new LogicException (T('Add failed'), 102);
        }
        return $rs;
    }
    //更新专项能力证书
    public function updateAbilityCert($data,$certInfo){
        $id = intval($data['cert_id']);
        unset($data['cert_id']);
        $rs = $this->model->update($id,$data);
        if(!$rs){
            return false;
        }else{
//            $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
//            $houseStaffDomain->checkStaff($certInfo['staff_id']);
            $houseStaffModel = new Model_Jiafubao_CompanyHouseStaff();
            $res = $houseStaffModel->update($certInfo['staff_id'], array('last_modify' => time()));
            if(!$res){
                throw new LogicException (T('Update failed'), 104);
            }
        }
        return $rs;
    }
    //作废专项能力证书
    public function banAbilityCert($certId){
        $data = array('status' => 'n', 'last_modify' => time());
        $rs = $this->model->update($certId,$data);
        return $rs;
    }
    //获取专项能力证书列表
	public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
		$rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        $StaffModel = new Model_Jiafubao_CompanyHouseStaff();
        foreach ($rs as $key=>$value){
            $staffInfo = $StaffModel->get($value['staff_id']);
            $rs[$key]['staff_info'] = $staffInfo;
            $rs[$key]['train_time'] = date("Y-m-d", $value['train_time']);
        }
		return $rs;
	}
	//获取数量
	public function getCount($filter) {
		return $this->model->getCount ( $filter );
	}

	//检测证书是否存在
    public function detectAbilityCert($data){
        $filter = array('company_id'=> $data['company_id'],'staff_id'=> $data['staff_id'], 'type' => $data['type'], 'level' =>$data['level']);
        $rs = $this->model->getByWhere($filter,'*');
        return $rs;
    }
    //获取专项能力证书列表
    public function getAllCert($filter){
        $rs = $this->model->getAll ( 'id,staff_id,type,level,cert_bn', $filter);
        return $rs;
    }
}
