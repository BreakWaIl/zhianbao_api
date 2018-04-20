<?php
class Domain_Jiafubao_CompanyInformation {
	var $model;

	public function __construct() {
		$this->model = new Model_Jiafubao_CompanyInformation();
	}

	//获取详情
    public function getBaseInfo($formId, $cols = '*'){
        $rs = array();
        $rs = $this->model->get( $formId);
        if (! $rs){
            return false;
        }else{
            $rs['create_time'] = date("Y-m-d H:i:s", $rs['create_time']);
            $rs['last_modify'] = date("Y-m-d H:i:s", $rs['last_modify']);
            $rs['is_intermediary_fee'] = json_decode($rs['is_intermediary_fee'], true);
            $rs['intermediary_fee'] = json_decode($rs['intermediary_fee'], true);
            $rs['business'] = json_decode($rs['business'], true);
            $business_info = '';
            foreach ($rs['business'] as $_v){
                $business_info .= $_v.'|';
            }
            $rs['business_info'] = $business_info;

            //获取公司信息
            $companyModel = new Model_Zhianbao_Company();
            $companyInfo = $companyModel->get($rs['company_id']);
            $rs['company_name'] = $companyInfo['name'];
            $domainArea = new Domain_Area();
            $companyMessageModel = new Model_Jiafubao_Company();
            $filter = array('company_id' => $rs['company_id']);
            $companyMessageInfo = $companyMessageModel->getByWhere($filter,'*');
            //拼接地区
            $province = $domainArea->getAreaNameById($companyMessageInfo['province']);
            $city = $domainArea->getAreaNameById($companyMessageInfo['city']);
            $district = $domainArea->getAreaNameById($companyMessageInfo['district']);
            $rs['address'] = $province.$city.$district.$companyMessageInfo['address'];
            $rs['zip_code'] = $companyMessageInfo['zip_code'];
        }

        return $rs;
    }
    public function getInfo($companyId){
        $rs = array();
        $filter = array('company_id' => $companyId);
        $companyModel = new Model_Jiafubao_Company();
        $info = $companyModel->getByWhere( $filter, 'id,company_id,address,legal_person,telephone,zip_code,country,province,city,district');
        if( $info){
            //获取地址
            $rs['country'] = $info['country'];
            $rs['province'] = $info['province'];
            $rs['city'] = $info['city'];
            $rs['district'] = $info['district'];
            $rs['address'] = $info['address'];
            $rs['zip_code'] = $info['zip_code'];
            $rs['country_name'] = '';
            $rs['province_name'] = '';
            $rs['city_name'] = '';
            $rs['district_name'] = '';
            $domainArea = new Domain_Area();
            if($info['country'] > 0){
                $rs['country_name'] = $domainArea->getAreaNameById($info['country']);
                $rs['province_name'] = $domainArea->getAreaNameById($info['province']);
                $rs['city_name'] = $domainArea->getAreaNameById($info['city']);
                $rs['district_name'] = $domainArea->getAreaNameById($info['district']);
            }
            $rs['information'] = false;
            $information = $this->model->getByWhere( $filter, '*');
            if(!empty($information)){
                $information['intermediary_fee'] = json_decode($information['intermediary_fee'],true);
                $information['business'] = json_decode($information['business'],true);
                $information['is_intermediary_fee'] = json_decode($information['is_intermediary_fee'],true);
                $rs['information'] = $information;
            }
            //获取公司名称
            $companyModel = new Model_Zhianbao_Company();
            $company = $companyModel->get($companyId);
            $rs['company_name'] = $company;
        }

        return $rs;
    }
    public function check($companyId){
        $filter = array('company_id' => $companyId);
        $rs = $this->model->getByWhere( $filter, '*');
        if (! $rs){
            return false;
        }else{
            return $this->getBaseInfo($rs['id']);
        }
    }
    //添加登记表
    public function add($data,$companyData,$companyInfo){
        $filter = array('company_id' => $companyInfo['id']);
        $info = $this->model->getByWhere($filter,'*');
        if(!empty($info)){
            throw new LogicException ( T ( 'Add failed' ), 102 );
        }
        $rs = $this->model->insert($data);
        if( !$rs){
            throw new LogicException ( T ( 'Add failed' ), 102 );
        }else{
            if($companyInfo['name'] != $companyData['company_name']){
                //更新公司名称
                $companyModel = new Model_Zhianbao_Company();
                $company_data = array('name' => $companyData['company_name'],'last_modify' => time());
                $res = $companyModel->update($companyInfo['id'],$company_data);
                if( !$res){
                    throw new LogicException ( T ( 'Add failed' ), 102 );
                }
            }
            //更新地址、邮编
            $update_data = array(
                'address' => $companyData['address'],
                'zip_code' => $companyData['zip_code'],
                'legal_person' => $data['legal_person'],
                'telephone' => $data['telephone'],
                'last_modify' => time(),
            );
            $jfbCompanyModel = new Model_Jiafubao_Company();
            $address = $jfbCompanyModel->updateByWhere($filter,$update_data);
            if( !$address){
                throw new LogicException ( T ( 'Add failed' ), 102 );
            }
        }
        return $rs;
    }

    //更新登记表
    public function update($data,$companyData,$companyInfo){
        $id = $data['form_id'];
        unset($data['form_id']);
        //更新登记表信息
        $rs = $this->model->update($id,$data);
        if( !$rs){
            return false;
        }else{
            if($companyInfo['name'] != $companyData['company_name']){
                //更新公司名称
                $companyModel = new Model_Zhianbao_Company();
                $company_data = array('name' => $companyData['company_name']);
                $res = $companyModel->update($companyInfo['id'],$company_data);
                if( !$res){
                    return false;
                }
            }
            //更新地址、邮编
            $filter = array('company_id' => $companyInfo['id']);
            $update_data = array(
                'address' => $companyData['address'],
                'zip_code' => $companyData['zip_code'],
                'legal_person' => $data['legal_person'],
                'telephone' => $data['telephone'],
            );
            $jfbCompanyModel = new Model_Jiafubao_Company();
            $address = $jfbCompanyModel->updateByWhere($filter,$update_data);
            if( !$address){
                return false;
            }
        }
        return $rs;
    }

    //获取列表
    public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
        $companyModel = new Model_Zhianbao_Company();
        $domainArea = new Domain_Area();
        $companyMessageModel = new Model_Jiafubao_Company();
        $rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key=>$value){
            $companyInfo = $companyModel->get($value['company_id']);
            $rs[$key]['company_name'] = $companyInfo['name'];
            $filter = array('company_id' => $value['company_id']);
            $companyMessageInfo = $companyMessageModel->getByWhere($filter,'*');
            //拼接地区
            $province = $domainArea->getAreaNameById($companyMessageInfo['province']);
            $city = $domainArea->getAreaNameById($companyMessageInfo['city']);
            $district = $domainArea->getAreaNameById($companyMessageInfo['district']);
            $rs[$key]['address'] = $province.$city.$district.$companyMessageInfo['address'];
            $rs[$key]['intermediary_fee'] = json_decode($value['intermediary_fee'],true);
            $rs[$key]['business'] = json_decode($value['business'], true);
        }
//        print_r($rs);exit;
        return $rs;
    }
    //获取数量
    public function getCount($filter) {
        return $this->model->getCount ( $filter );
    }

}
