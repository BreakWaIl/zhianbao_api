<?php
class Domain_Zhianbao_Company {
	var $model;

	public function __construct() {
		$this->model = new Model_Zhianbao_Company ();
	}
	//获取分类列表
	public function getCatList($filter){
		$rs = $this->model->getAll ( '*', $filter);
		return $rs;
	}
	//获取数量
	public function getCount($filter) {
		return $this->model->getCount ( $filter );
	}
	//获取分类详情
	public function getBaseInfo($id, $cols = '*') {
		$rs = $this->model->get($id,$cols);
		return $rs;
	}
	//获取公司信息
    public function getBaseByUserId($userId){
        $filter = array('user_id'=>$userId);
        $info = $this->model->getByWhere($filter);
        $companyModel = new Model_Zhianbao_Company();
        $companyInfo = $companyModel->getByWhere($filter);
        $info['company_info'] = $companyInfo;
        return $info;
    }
    //创建公司
    public function register($data){
        $province = $data['province'];
        $city = $data['city'];
        $district = $data['district'];
        unset($data['province']);unset($data['city']);unset($data['district']);
        $rs = $this->model->insert($data);
        if( !$rs){
            throw new LogicException ( T ( 'Add failed' ) , 102 );
        }else{
            //添加公司地址
            $companyInfoModel = new Model_Jiafubao_Company();
            $company_data = array(
                'company_id' => $rs,
                'country' => 1,
                'province' => $province,
                'city' => $city,
                'district' => $district,
                'create_time' => time(),
                'last_modify' => time(),
            );
            $res = $companyInfoModel->insert($company_data);
            if( !$res){
                throw new LogicException ( T ( 'Add failed' ) , 102 );
            }
        }
        return $rs;
    }

}
