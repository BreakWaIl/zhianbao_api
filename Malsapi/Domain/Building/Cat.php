<?php
class Domain_Building_Cat {
    var $model;

    public function __construct() {
        $this->model = new Model_Building_Cat();

    }

    public function getBaseInfo($catId, $cols = '*') {
        $rs = array ();
        $id = intval ( $catId );
        if ($id <= 0) {
            return $rs;
        }

        $rs = $this->model->get ( $id);

        if( !$rs){
            return false;
        }else{
            $domainArea = new Domain_Area();
            //获取户籍省市区
            $rs['province_name'] = $domainArea->getAreaNameById($rs['province']);
            $rs['city_name'] = $domainArea->getAreaNameById($rs['city']);
            $rs['district_name'] = $domainArea->getAreaNameById($rs['district']);
            $rs['create_time'] = date("Y-m-d H:i:s", $rs['create_time']);
            $rs['last_modify'] = date("Y-m-d H:i:s", $rs['last_modify']);
        }

        return $rs;
    }

    public function getBaseInfoByName($companyId,$name){
        $rs = $this->model->getByWhere(array('company_id' => $companyId, 'name'=>$name));
        return $rs;
    }
    //添加类别
    public function add($data){
        $rs = $this->model->insert($data);
        if(! $rs){
            throw new LogicException ( T ( 'Add failed' ), 102 );
        }
        return $rs;
    }
    //更新类别
    public function update($data){
        $id = intval($data['cat_id']);
        unset($data['cat_id']);
        $rs = $this->model->update($id,$data);
        return $rs;
    }
    //检测是否正在使用
    public function isUser($companyId,$catId){
        $rs = true;
//        $staffModel = new Model_Building_Staff ();
        $projectToCatIdModel = new Model_Building_ProjectToCat();
        $filter = array('company_id' => $companyId,'cat_id'=>$catId);
        $list = $projectToCatIdModel->getAll('*', $filter);
        if( is_array($list) && !empty($list)){
            return false;
        }
        return $rs;
    }
    //删除
    public function delete($catId){
        $rs = $this->model->delete($catId);
        return $rs;
    }
    //获取列表
    public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
        $companyModel = new Model_Zhianbao_Company();
        $rs = $this->model->getAll ( 'id,company_id,name,legal_person,create_time,last_modify', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key=>$value){
            $companyInfo = $companyModel->get($value['company_id']);
            $rs[$key]['company_name'] = $companyInfo['name'];
        }
        return $rs;
    }
    //获取数量
    public function getCount($filter) {
        return $this->model->getCount ( $filter );
    }



}
