<?php
class Domain_Building_Visa {
    var $model;

    public function __construct() {
        $this->model = new Model_Building_Visa();

    }

    public function getBaseInfo($visaId, $cols = '*') {
        $rs = array ();
        $id = intval ( $visaId );
        if ($id <= 0) {
            return $rs;
        }

        $rs = $this->model->get ($id);
        if( !$rs){
            return false;
        }else{
            $rs['img_url'] = json_decode($rs['img_url']);
            $rs['create_time'] = date("Y-m-d H:i:s", $rs['create_time']);
            $rs['last_modify'] = date("Y-m-d H:i:s", $rs['last_modify']);
            $projectModel = new Model_Building_Project();
            //获取项目信息
            $info = $projectModel->get($rs['project_id']);
            $rs['project_name'] = $info['name'];
        }

        return $rs;
    }
    //添加合同外签证
    public function add($data){
        $rs = $this->model->insert($data);
        return $rs;
    }
    //更新合同外签证
    public function update($data){
        $id = intval($data['visa_id']);
        unset($data['visa_id']);
        $rs = $this->model->update($id,$data);
        return $rs;
    }
    //获取列表
    public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
        $projectModel = new Model_Building_Project();
        $rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key=>$value){
            $rs[$key]['img_url'] = json_decode($value['img_url']);
            //获取项目信息
            $info = $projectModel->get($value['project_id']);
            $rs[$key]['project_name'] = $info['name'];
        }
        return $rs;
    }
    //获取数量
    public function getCount($filter) {
        return $this->model->getCount ( $filter );
    }

}
