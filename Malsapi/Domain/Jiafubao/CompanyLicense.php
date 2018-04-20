<?php
class Domain_Jiafubao_CompanyLicense {
	var $model;

	public function __construct() {
		$this->model = new Model_Jiafubao_CompanyLicense();
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
            $rs['img_url'] = json_decode($rs['img_url'],true);
            $rs['create_time'] = date("Y-m-d H:i:s", $rs['create_time']);
            $rs['last_modify'] = date("Y-m-d H:i:s", $rs['last_modify']);
            $companyModel = new Model_Zhianbao_Company();
            $companyInfo = $companyModel->get($rs['company_id']);
            $rs['company_name'] = $companyInfo['name'];
        }

        return $rs;
    }
    //添加营业执照
    public function addLicense($data){
        $rs = $this->model->insert($data);
        if(!$rs){
            throw new LogicException ( T ( 'Add failed' ) , 102 );
        }
        return $rs;
    }
    //更新营业执照
    public function updateLicense($data){
        $id = intval($data['license_id']);
        unset($data['license_id']);
        $rs = $this->model->update($id,$data);
        return $rs;
    }
    //删除营业执照
    public function deleteLicense($recordId){
        $rs = $this->model->delete($recordId);
        return $rs;
    }
    //获取列表
	public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
	    $companyModel = new Model_Zhianbao_Company();
        $rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key=>$value){
            $rs[$key]['img_url'] = json_decode($value['img_url'], true);
            $companyInfo = $companyModel->get($value['company_id']);
            $rs[$key]['company_name'] = $companyInfo['name'];
        }
        return $rs;
	}
	//获取数量
	public function getCount($filter) {
		return $this->model->getCount ( $filter );
	}

}
