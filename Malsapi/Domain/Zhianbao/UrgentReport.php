<?php
class Domain_Zhianbao_UrgentReport {
	var $model;

	public function __construct() {
		$this->model = new Model_Zhianbao_UrgentReport ();
	}

	//获取通知详情
    public function getBaseInfo($planId, $cols = '*'){
        $rs = array ();
        $id = intval ( $planId );
        if ($id <= 0) {
            return $rs;
        }

        $rs = $this->model->get ( $id);

        if (! $rs){
            return false;
        }else{
            $rs['create_time'] = $rs['create_time'] == 0 ? '': date('Y-m-d H:i:s',$rs['create_time']);
            $rs['last_modify'] = $rs['last_modify'] == 0 ? '': date('Y-m-d H:i:s',$rs['last_modify']);
            $companyModel = new Model_Zhianbao_Company();
            $companyInfo = $companyModel->get($rs['company_id']);
            $rs['company_name'] = $companyInfo['name'];
        }

        return $rs;
    }
    //添加应急演练
    public function addReport($data){
        $rs = $this->model->insert($data);
        return $rs;
    }
    //更新应急演练
    public function updateReport($data){
        $id = intval($data['report_id']);
        unset($data['report_id']);
        $rs = $this->model->update($id,$data);
        return $rs;
    }
    //获取列表
	public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
		$rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        $companyModel = new Model_Zhianbao_Company();
        foreach ($rs as $key=>$value){
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
