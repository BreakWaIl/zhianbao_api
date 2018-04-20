<?php
class Domain_Zhianbao_StaffCheck {
	var $model;

	public function __construct() {
		$this->model = new Model_Zhianbao_StaffCheck ();
	}

	//获取通知详情
    public function getBaseInfo($checkId, $cols = '*'){
        $rs = array ();
        $id = intval ( $checkId );
        if ($id <= 0) {
            return $rs;
        }

        $rs = $this->model->get ( $id);

        if (! $rs){
            return false;
        }

        return $rs;
    }
    //添加体检记录
    public function addCheck($data){
        $rs = $this->model->insert($data);
        if($rs){
            //更新员工证件更新时间
            $staffModel = new Model_Zhianbao_Staff();
            $update_data = array('check_last_modify' => time());
            $res = $staffModel->update($data['staff_id'],$update_data);
            if(!$res){
                throw new LogicException ( T ( 'Add failed' ) , 102 );
            }
        }
        return $rs;
    }
    //更新体检记录
    public function updateCheck($data){
        $id = intval($data['check_id']);
        unset($data['check_id']);
        $rs = $this->model->update($id,$data);
        if($rs){
            //更新员工证件更新时间
            $staffModel = new Model_Zhianbao_Staff();
            $update_data = array('check_last_modify' => time());
            $res = $staffModel->update($data['staff_id'],$update_data);
            if(!$res){
                throw new LogicException ( T ( 'Update failed' ) , 104 );
            }
        }
        return $rs;
    }
    //删除体检记录
    public function deleteCheck($checkId){
        $rs = $this->model->delete($checkId);
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
}
