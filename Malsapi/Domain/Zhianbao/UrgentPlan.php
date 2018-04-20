<?php
class Domain_Zhianbao_UrgentPlan {
	var $model;

	public function __construct() {
		$this->model = new Model_Zhianbao_UrgentPlan ();
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
            $rs['finish_time'] = $rs['finish_time'] == 0 ? '': date('Y-m-d H:i:s',$rs['finish_time']);
        }

        return $rs;
    }
    //添加应急预案
    public function addPlan($data){
        $data['bn'] = date("Ymd",time()).mt_rand(10000,99999);
        $rs = $this->model->insert($data);
        return $rs;
    }
    //更新应急预案
    public function updatePlan($data){
        $id = intval($data['plan_id']);
        unset($data['plan_id']);
        $rs = $this->model->update($id,$data);
        return $rs;
    }
    //获取列表
	public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
		$rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key=>$value){
            $rs[$key]['finish_time'] = $value['finish_time'] == 0 ? '': date('Y-m-d H:i:s',$value['finish_time']);
            $rs[$key]['repeal_time'] = $value['repeal_time'] == 0 ? '': date('Y-m-d H:i:s',$value['repeal_time']);
        }
		return $rs;
	}
	//获取数量
	public function getCount($filter) {
		return $this->model->getCount ( $filter );
	}

	//同意应急预案审核
    public function Agree($planInfo){
        $id = $planInfo['id'];
        $data = array(
            'status' => 'finish',
            'last_modify' => time(),
            'finish_time' => time(),
        );
        $rs = $this->model->update($id, $data);
        return $rs;
    }
    //拒绝应急预案审核
    public function Refuse($planInfo){
        $id = $planInfo['id'];
        $data = array(
            'status' => 'failure',
            'last_modify' => time(),
        );
        $rs = $this->model->update($id, $data);
        return $rs;
    }
    //作废应急预案
    public function repealUrgentPlan($id,$data){
        $rs = $this->model->update($id,$data);
        return $rs;
    }
}
