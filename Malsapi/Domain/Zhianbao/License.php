<?php
class Domain_Zhianbao_License {
	var $model;

	public function __construct() {
		$this->model = new Model_Zhianbao_LicenseType();
	}

	//获取详情
    public function getBaseInfo($typeId, $cols = '*'){
        $rs = array ();
        $id = intval ( $typeId );
        if ($id <= 0) {
            return $rs;
        }

        $rs = $this->model->get ( $id);

        if (! $rs){
            return false;
        }

        return $rs;
    }
    //添加证照类型
    public function addLicenseType($data){
        $rs = $this->model->insert($data);
        return $rs;
    }
    //更新证照类型
    public function updateLicenseType($data){
        $id = intval($data['type_id']);
        unset($data['type_id']);
        $rs = $this->model->update($id,$data);
        return $rs;
    }
    //删除证照类型
    public function deleteLicenseType($typeId){
        $rs = $this->model->delete($typeId);
        return $rs;
    }
    //检测是否则正在使用
    public function isUser($regulatorId,$typeId){
        $rs = array();
        $licenseModel = new Model_Zhianbao_License();
        $regulatorToCustomerModel = new Model_Zhianbao_RegulatorToCustomer();
        $filter = array('regulator_id' => $regulatorId);
        $list = $regulatorToCustomerModel->getAll('*',$filter);
        foreach ($list as $key=>$value){
            $to_filter = array('company_id' => $value['company_id'],'type_id' =>$typeId);
            $rs = $licenseModel->getAll('*', $to_filter);
            if(!empty($rs)){
                return $rs;
            }
        }
        return $rs;
    }
    //获取列表
	public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
		$rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
		return $rs;
	}
	//获取数量
	public function getCount($filter) {
		return $this->model->getCount ( $filter );
	}

	//企业获取证书类型列表
    public function getAllLicenseType($filter){
        $list = $this->model->getAll('*',$filter);
        return $list;
    }
    //获取企业证照详情
    public function getLicenseInfo($licenseId){
        $licenseModel = new Model_Zhianbao_License();
        $rs = array ();
        $id = intval ( $licenseId );
        if ($id <= 0) {
            return $rs;
        }

        $rs = $licenseModel->get ( $id);

        if (! $rs){
            return false;
        }else{
            $rs['img_url'] = json_decode($rs['img_url'],true);
        }

        return $rs;
    }
    //添加企业证照
    public function addLicense($data){
        $licenseModel = new Model_Zhianbao_License();
        $rs = $licenseModel->insert($data);
        return $rs;
    }
    //更新企业证照
    public function updateLicense($data){
        $licenseModel = new Model_Zhianbao_License();
        $id = intval($data['license_id']);
        unset($data['license_id']);
        $rs = $licenseModel->update($id,$data);
        return $rs;
    }
    //删除企业证照
    public function deleteLicense($licenseId){
        $licenseModel = new Model_Zhianbao_License();
        $rs = $licenseModel->delete($licenseId);
        return $rs;
    }
    //获取列表
    public function getAllLicense($filter, $page = 1, $page_size = 20, $orderby = ''){
        $licenseModel = new Model_Zhianbao_License();
        $licenseTypeModel = new Model_Zhianbao_LicenseType();
        $companyModel = new Model_Zhianbao_Company();
        $rs = $licenseModel->getAll ( '*', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key=>$value){
            $companyInfo = $companyModel->get($value['company_id']);
            $rs[$key]['company_name'] = $companyInfo['name'];
            $typeInfo = $licenseTypeModel->get($value['type_id']);
            $rs[$key]['type_name'] = $typeInfo['name'];
            $rs[$key]['img_url'] = json_decode($value['img_url'],true);
        }
        return $rs;
    }
    //获取数量
    public function getCountLicense($filter) {
        $licenseModel = new Model_Zhianbao_License();
        return $licenseModel->getCount ( $filter );
    }
}
