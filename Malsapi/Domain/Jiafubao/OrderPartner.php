<?php
class Domain_Jiafubao_OrderPartner {
	var $model;

	public function __construct() {
		$this->model = new Model_Jiafubao_OrderPartner();
	}

	//获取详情
    public function getBaseInfo($staffId, $cols = '*'){}

    //获取列表
	public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
		$rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
		return $rs;
	}
	//获取数量
	public function getCount($filter) {
        $count = $this->model->getCount ( $filter );
		return $count;
	}
}
