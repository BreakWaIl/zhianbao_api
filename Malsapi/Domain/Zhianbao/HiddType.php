<?php
class Domain_Zhianbao_HiddType {
	var $model;

	public function __construct() {
		$this->model = new Model_Zhianbao_HiddType ();
	}
	//获取数量
	public function getCount($filter) {
		return $this->model->getCount ( $filter );
	}
	//获取分类详情
	public function getBaseInfo($id, $cols = '*') {
		$rs = $this->model->get($id);
		return $rs;
	}
	//添加隐患类型
    public function addHiddType($data){
        $hiddTypeId = $this->model->insert($data);
        return $hiddTypeId;
    }
    //删除隐患类型
    public function delHiddType($typeId){
        $rs = $this->model->delete($typeId);
        return $rs;
    }
    //更新隐患类型
    public function updateHiddType($typeId,$data){
        $rs = $this->model->update($typeId,$data);
        return $rs;
    }
    public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = '') {
        $hiddProjectModel = new Model_Zhianbao_HiddProject();
        $rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key => $value){
            $hidd_filter = array(
                'regulator_id' => $filter['regulator_id'],
                'type_id' => $value['id']
            );
            $hiddProjectList = $hiddProjectModel->getAll('*',$hidd_filter);
            $rs[$key]['hidd_project_list'] = $hiddProjectList;
        }
        return $rs;
    }

}
