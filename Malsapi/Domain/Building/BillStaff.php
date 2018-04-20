<?php
class Domain_Building_BillStaff {
    var $model;

    public function __construct() {
        $this->model = new Model_Building_BillStaff();

    }

    public function getBaseInfo($billId, $cols = '*') {
        $rs = array ();
        $id = intval ( $billId );
        if ($id <= 0) {
            return $rs;
        }

        $rs = $this->model->get ( $id);

        if( !$rs){
            return false;
        }else{
            $rs['create_time'] = date("Y-m-d H:i:s", $rs['create_time']);
            $rs['last_modify'] = date("Y-m-d H:i:s", $rs['last_modify']);
            //获取员工信息
            $staffModel = new Model_Building_Staff();
            $staffInfo = $staffModel->get($rs['staff_id']);
            $rs['staff_name'] = $staffInfo['name'];
            //获取项目信息
            $projectModel = new Model_Building_Project();
            $projectInfo = $projectModel->get($rs['project_id']);
            $rs['project_name'] = $projectInfo['name'];
        }

        return $rs;
    }
    //添加员工出入账
    public function add($data,$projectInfo){
        $rs = $this->model->insert($data);
        if( !$rs){
            throw new LogicException ( T ( 'Add failed' ), 102 );
        }else{
            //记录到项目出入帐
            $billProjectDomain = new Domain_Building_BillProject();
            $project_data = array(
                'company_id' => $data['company_id'],
                'project_id' => $data['project_id'],
                'type' => 'expenditure',
                'title' => '【员工新增】'.$data['title'],
                'amount' => $data['amount'],
                'remark' => $data['remark'],
                'create_time' => time(),
                'last_modify' => time(),
                'operate_id' => $data['operate_id'],
            );
            $billProjectDomain->add($project_data,$projectInfo);
        }
        return $rs;
    }
    //更新员工出入账
    public function update($data){
        $id = intval($data['bill_id']);
        unset($data['bill_id']);
        $rs = $this->model->update($id,$data);
        return $rs;
    }
    //获取列表
    public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
        $projectModel = new Model_Building_Project();
        $staffModel = new Model_Building_Staff();
        $rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key=>$value){
            //获取项目信息
            $projectInfo = $projectModel->get($value['project_id']);
            $rs[$key]['project_name'] = $projectInfo['name'];
            //获取员工信息
            $staffInfo = $staffModel->get($value['staff_id']);
            $rs[$key]['staff_name'] = $staffInfo['name'];
        }
        return $rs;
    }
    //获取数量
    public function getCount($filter) {
        return $this->model->getCount ( $filter );
    }
    //检测项目和员工关系
    public function check($filter){
        $projectToStaffModel = new Model_Building_ProjectToStaff();
        $rs = $projectToStaffModel->getByWhere($filter,'*');
        if( $rs){
            return true;
        }else{
            return false;
        }
    }

}
