<?php
class Domain_Zhianbao_CheckPenalty {
	var $model;

	public function __construct() {
		$this->model = new Model_Zhianbao_CheckPenalty ();
	}
    public function getCount($filter) {
        return $this->model->getCount ( $filter );
    }
    //获取详情
    public function getBaseInfo($id, $cols = '*') {
        $rs = $this->model->get($id,$cols);
        return $rs;
    }
    //添加
    public function addCheckPenalty($data){
        $CheckPenaltyId = $this->model->insert($data);
        //更新处罚
        $troubleModel = new Model_Zhianbao_CheckTrouble();
        $upData = array('status' => 1,'last_modify' => time());
        $troubleModel->update($data['trouble_id'],$upData);
        return $CheckPenaltyId;
    }
    //删除
    public function delCheckPenalty($id){
        $rs = $this->model->delete($id);
        return $rs;
    }
    //更新
    public function updateCheckPenalty($id,$data){
        $rs = $this->model->update($id,$data);
        return $rs;
    }
    public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = '') {
        $rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        $checkTroubleModel = new Model_Zhianbao_CheckTrouble();
        foreach ($rs as $key=>$value){
            $info = $checkTroubleModel->get($value['trouble_id']);
            $rs[$key]['trouble_name'] = $info['title'];
        }
        return $rs;
    }
    //作废处罚记录
    public function repealCheckPenalty($id,$data){
        $rs = $this->model->update($id,$data);
        return $rs;
    }
}
