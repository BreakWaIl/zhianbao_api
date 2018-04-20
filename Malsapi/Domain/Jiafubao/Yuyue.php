<?php
class Domain_Jiafubao_Yuyue {
	public function __construct() {
		$this->model = new Model_Jiafubao_Yuyue();
	}
    public function addYuyue($data){
            $id = $this->model->insert($data);
            return $id;
    }
    public function update($id,$data){
        $rs = $this->model->update($id,$data);
        return $rs;
    }
    public function delete($id){
        $rs = $this->model->delete($id);
        return $rs;
    }
    public function getBaseInfo($id,$col = '*'){
        $rs = $this->model->get($id,$col);
        $rs['birthday'] = date('Y-m-d',$rs['birthday']);
        $rs['begin_time'] = date('Y-m-d',$rs['begin_time']);
        $rs['end_time'] = date('Y-m-d',$rs['end_time']);
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
    //判断是否可以预约
    public function canYuyue($hotelMark){
        $today = strtotime(date('Y-m-d'.'00:00:00',time()));
        $filter = array(
            'hotel' => $hotelMark,
            'create_time > ?' => $today,
            'create_time < ?' => $today + 86400,
        );
        $count = $this->model->getCount($filter);
        if($count < 10){
            return true;
        }else{
            return false;
        }
    }
}
