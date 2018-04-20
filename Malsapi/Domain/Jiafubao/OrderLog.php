<?php
class Domain_Jiafubao_OrderLog {
	var $model;

	public function __construct() {
		$this->model = new Model_Jiafubao_OrderLog();
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
	//获取数量
	public function getCount($filter) {
		return $this->model->getCount ( $filter );
	}
    //添加日志
    public function addLog($orderId,$content){
        $data = array(
            'order_id' => $orderId,
            'content' => $content,
            'create_time' => time()
        );
        $rs = $this->model->insert($data);
        return $rs;
    }
}
