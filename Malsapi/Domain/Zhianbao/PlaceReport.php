<?php
class Domain_Zhianbao_PlaceReport {
	var $model;

	public function __construct() {
		$this->model = new Model_Zhianbao_PlaceReport ();
	}

	//获取通知详情
    public function getBaseInfo($reportId, $cols = '*'){
        $rs = array ();
        $id = intval ( $reportId );
        if ($id <= 0) {
            return $rs;
        }

        $rs = $this->model->get ( $id);

        if (! $rs){
            return false;
        }

        return $rs;
    }
    //添加检测报告
    public function addReport($data){
        $rs = $this->model->insert($data);
        return $rs;
    }
    //更新检测报告
    public function updateReport($data){
        $id = intval($data['report_id']);
        unset($data['report_id']);
        $rs = $this->model->update($id,$data);
        return $rs;
    }
    //删除检测报告
    public function deleteReport($reportId){
        $rs = $this->model->delete($reportId);
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
