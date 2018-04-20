<?php
class Domain_Zhianbao_CheckTrouble {
	var $model;

	public function __construct() {
		$this->model = new Model_Zhianbao_CheckTrouble ();
	}
    public function getCount($filter) {
        return $this->model->getCount ( $filter );
    }
    //获取详情
    public function getBaseInfo($id, $cols = '*') {
        $rs = $this->model->get($id,$cols);
        $rs['create_time'] = date('Y-m-d H:i:s',$rs['create_time']);
        $rs['last_modify'] = date('Y-m-d H:i:s',$rs['last_modify']);
        return $rs;
    }
    //添加
    public function addCheckTrouble($data){
        $CheckTroubleId = $this->model->insert($data);
        return $CheckTroubleId;
    }
    //删除
    public function delCheckTrouble($id){
        $rs = $this->model->delete($id);
        return $rs;
    }
    //更新
    public function updateCheckTrouble($id,$data){
        $rs = $this->model->update($id,$data);
        return $rs;
    }
    public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = '') {
        $rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        return $rs;
    }
    //根据公司ID获取事故ID
    public function getTroubleIdsByCompanyIds($companyIds){
        $filter = array('company_id' => $companyIds);
        $rs = $this->model->getAll('id',$filter);
        $troubleIds = array();
        foreach ($rs as $key => $value){
            $troubleIds[] = $value['id'];
        }
        return $troubleIds;
    }
}
