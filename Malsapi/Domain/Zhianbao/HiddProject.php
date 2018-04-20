<?php
class Domain_Zhianbao_HiddProject {
	var $model;

	public function __construct() {
		$this->model = new Model_Zhianbao_HiddProject ();
	}
    public function getCount($filter) {
        return $this->model->getCount ( $filter );
    }
    //获取隐患项目详情
    public function getBaseInfo($id, $cols = '*') {
        $rs = $this->model->get($id,$cols);
        return $rs;
    }
    //添加隐患项目
    public function addHiddProject($data){
        $HiddProjectId = $this->model->insert($data);
        return $HiddProjectId;
    }
    //删除隐患项目
    public function delHiddProject($id){
        $rs = $this->model->delete($id);
        return $rs;
    }
    //更新隐患项目
    public function updateHiddProject($id,$data){
        $rs = $this->model->update($id,$data);
        return $rs;
    }
    public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = '') {
        $rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        return $rs;
    }

}
