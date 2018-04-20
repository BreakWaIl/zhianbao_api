<?php
class Domain_Zhianbao_CheckReport {
	var $model;

	public function __construct() {
		$this->model = new Model_Zhianbao_CheckReport ();
	}
    public function getCount($filter) {
        return $this->model->getCount ( $filter );
    }
    //获取详情
    public function getBaseInfo($id, $cols = '*') {
	    $planModel = new Model_Zhianbao_CheckPlan();
        $hiddProjectModel = new Model_Zhianbao_HiddProject();
        $hiddTypeModel  = new Model_Zhianbao_HiddType();
        $rs = $this->model->get($id,$cols);
        $planInfo = $planModel->get($rs['plan_id']);
        $planInfo['check_time'] = date('Y-m-d H:i:s',$planInfo['check_time']);
        $content = json_decode($rs['content'],true);
        $return = array();
        foreach ($content as $key => $value){
            $projectInfo = $hiddProjectModel->get($value['project_id']);
            $value['hidd_project_title'] = $projectInfo['title'];
            $value['hidd_project_content'] = $projectInfo['content'];
            $typeId = $projectInfo['type_id'];
            $typeInfo = $hiddTypeModel->get($typeId);
            $value['hidd_type_id'] = $typeId;
            $value['hidd_type_name'] = $typeInfo['name'];
            $return[$typeId]['project'][] = $value;
            $return[$typeId]['hidd_type_id'] = $typeId;
            $return[$typeId]['hidd_type_name'] = $typeInfo['name'];
        }
        sort($return);
        $planInfo['report_content'] = $return;
        $planInfo['report_time'] = date('Y-m-d H:i:s',$rs['create_time']);
        return $planInfo;
    }
    //添加
    public function addCheckReport($data){
        $CheckReportId = $this->model->insert($data);
        return $CheckReportId;
    }
    //删除
    public function delCheckReport($id){
        $rs = $this->model->delete($id);
        return $rs;
    }
    //更新
    public function updateCheckReport($id,$data){
        $rs = $this->model->update($id,$data);
        return $rs;
    }
    public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = '') {
        $planModel = new Model_Zhianbao_CheckPlan();
        $rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        foreach ( $rs as $key => $value){
            $planInfo = $planModel->get($value['plan_id']);
            $rs[$key]['plan_title'] = $planInfo['title'];
        }
        return $rs;
    }

}
