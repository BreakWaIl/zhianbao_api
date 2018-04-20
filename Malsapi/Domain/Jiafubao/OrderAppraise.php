<?php
class Domain_Jiafubao_OrderAppraise {
	var $model;

	public function __construct() {
		$this->model = new Model_Jiafubao_OrderAppraise();
	}

	//获取详情
    public function getBaseInfo($id, $cols = '*'){
        $rs = $this->model->get ( $id , $cols);
        return $rs;
    }
    //获取列表
	public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
		$rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
		return $rs;
	}
	//根据条件获取
    public function getBaseInfoByFilter ($filter){
        $rs = $this->model->getByWhere($filter);
        return $rs;
    }
	//获取数量
	public function getCount($filter) {
		return $this->model->getCount ( $filter );
	}
	//添加订单评价
    public function addAppraise($data){
	    return $this->model->insert($data);
    }

}
