<?php
class Domain_Zhianbao_Staff {
	var $model;

	public function __construct() {
		$this->model = new Model_Zhianbao_Staff ();
	}
    public function getCount($filter) {
        return $this->model->getCount ( $filter );
    }
    //获取详情
    public function getBaseInfo($id, $cols = '*') {
        $rs = $this->model->get($id,$cols);
        if( !$rs){
            return false;
        }else{
            $rs['birthday'] = $rs['birthday'] == 0 ? '-': date('Y-m-d H:i:s',$rs['birthday']);
            //获取员工角色
            $partModel = new Model_Zhianbao_Part();
            $partInfo = $partModel->get($rs['part_id']);
            $rs['part_name'] = $partInfo['name'];
        }
        return $rs;
    }
    //添加
    public function addStaff($data){
        $StaffId = $this->model->insert($data);
        return $StaffId;
    }
    //删除
    public function delStaff($id){
        $rs = $this->model->delete($id);
        if($rs){
            //删除该员工的证书、体检记录
            $filter = array('staff_id' => $id);
            $certModel = new Model_Zhianbao_Cert();
            $checkModel = new Model_Zhianbao_StaffCheck();
            $cert = $certModel->deleteByWhere($filter);
            if(!$cert){
                throw new LogicException ( T ( 'Delete failed' ) , 105 );
            }
            $check = $checkModel->deleteByWhere($filter);
            if(!$check){
                throw new LogicException ( T ( 'Delete failed' ) , 105 );
            }
        }
        return $rs;
    }
    //更新
    public function updateStaff($id,$data){
        $rs = $this->model->update($id,$data);
        return $rs;
    }
    public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = '') {
        $rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key=>$value){
            $rs[$key]['cert_last_modify'] = $value['cert_last_modify'] == 0 ? '-': date('Y-m-d H:i:s',$value['cert_last_modify']);
            $rs[$key]['check_last_modify'] = $value['check_last_modify'] == 0 ? '-': date('Y-m-d H:i:s',$value['check_last_modify']);
            $rs[$key]['birthday'] = $value['birthday'] == 0 ? '-': date('Y-m-d H:i:s',$value['birthday']);
        }
        return $rs;
    }

}
