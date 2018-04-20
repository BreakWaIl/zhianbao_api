<?php
class Domain_Jiafubao_Company {
	var $model;

	public function __construct() {
		$this->model = new Model_Jiafubao_Company();
	}
	//获取详情
    public function getBaseInfo($companyId, $cols = '*'){
        $rs = array();

        $filter = array('company_id' => $companyId);
        $rs = $this->model->getByWhere( $filter, $cols);

        if (! $rs){
            return null;
        }else{
            //获取地址
            $rs['country_name'] = '';
            $rs['province_name'] = '';
            $rs['city_name'] = '';
            $rs['district_name'] = '';
            $domainArea = new Domain_Area();
            if($rs['country'] > 0){
                $rs['country_name'] = $domainArea->getAreaNameById($rs['country']);
                $rs['province_name'] = $domainArea->getAreaNameById($rs['province']);
                $rs['city_name'] = $domainArea->getAreaNameById($rs['city']);
                $rs['district_name'] = $domainArea->getAreaNameById($rs['district']);
            }
            $rs['register_time'] = date("Y-m-d", $rs['register_time']);
            $rs['create_time'] = date("Y-m-d H:i:s", $rs['create_time']);
            $rs['last_modify'] = date("Y-m-d H:i:s", $rs['last_modify']);
            $rs['reg_address'] = $rs['province_name'].$rs['city_name'].$rs['district_name'].$rs['address'];
            $companyModel = new Model_Zhianbao_Company();
            $companyInfo = $companyModel->get($rs['company_id']);
            $rs['company_name'] = $companyInfo['name'];
        }

        return $rs;
    }
    //通过ID获取详情
    public function getBaseInfoById($jfbCompanyId,$cols='*'){
        $rs = $this->model->get($jfbCompanyId,$cols);
        if (! $rs){
            return null;
        }else{
            //获取地址
            $rs['country_name'] = '';
            $rs['province_name'] = '';
            $rs['city_name'] = '';
            $rs['district_name'] = '';
            $domainArea = new Domain_Area();
            if($rs['country'] > 0){
                $rs['country_name'] = $domainArea->getAreaNameById($rs['country']);
                $rs['province_name'] = $domainArea->getAreaNameById($rs['province']);
                $rs['city_name'] = $domainArea->getAreaNameById($rs['city']);
                $rs['district_name'] = $domainArea->getAreaNameById($rs['district']);
            }
            $rs['register_time'] = date("Y-m-d", $rs['register_time']);
            $rs['create_time'] = date("Y-m-d H:i:s", $rs['create_time']);
            $rs['last_modify'] = date("Y-m-d H:i:s", $rs['last_modify']);
            $rs['reg_address'] = $rs['province_name'].$rs['city_name'].$rs['district_name'].$rs['address'];
            $companyModel = new Model_Zhianbao_Company();
            $companyInfo = $companyModel->get($rs['company_id']);
            $rs['company_name'] = $companyInfo['name'];
        }

        return $rs;
    }
    //更新信息
    public function update($data,$companyInfo){
        $companyName = $data['company_name'];
        unset($data['company_name']);
        $filter = array('company_id' => $data['company_id']);
        $info = $this->model->getByWhere($filter,'*');
        if(empty($info)){
            $data['create_time'] = time();
            $data['last_modify'] = time();
            $rs = $this->model->insert($data);
            if( !$rs){
                throw new LogicException ( T ( 'Add failed' ), 102 );
            }
        }else{
            $id = intval($info['id']);
            unset($data['company_id']);
           // unset($data['name']);
            $data['last_modify'] = time();
            $rs = $this->model->update($id,$data);
            if( !$rs){
                throw new LogicException ( T ( 'Add failed' ), 102 );
            }
        }
        //更新公司名称
        $companyModel = new Model_Zhianbao_Company();
        $company_data = array('name' => $companyName);
        $res = $companyModel->update($companyInfo['id'],$company_data);
        if( !$res){
            throw new LogicException ( T ( 'Add failed' ), 102 );
        }
        return $rs;
    }
    //获取列表
    public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
        $rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key=>$value){
            $rs[$key]['register_time'] = date("Y-m-d", $value['register_time']);
        }
        return $rs;
    }
    //获取数量
    public function getCount($filter) {
        return $this->model->getCount ( $filter );
    }

}
