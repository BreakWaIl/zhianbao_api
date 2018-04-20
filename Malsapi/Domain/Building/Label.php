<?php
class Domain_Building_Label {
    var $model;

    public function __construct() {
        $this->model = new Model_Building_Label();

    }

    public function getBaseInfo($labelId, $cols = '*') {
        $rs = array ();
        $id = intval ( $labelId );
        if ($id <= 0) {
            return $rs;
        }

        $rs = $this->model->get ( $id);

        if( !$rs){
            return false;
        }else{
            $rs['create_time'] = date("Y-m-d H:i:s", $rs['create_time']);
            $rs['last_modify'] = date("Y-m-d H:i:s", $rs['last_modify']);
        }

        return $rs;
    }
    //添加标签
    public function add($data){
        $rs = $this->model->insert($data);
        if(! $rs){
            throw new LogicException ( T ( 'Add failed' ), 102 );
        }
        return $rs;
    }
    public function getBaseInfoByName($companyId,$name){
        $rs = $this->model->getByWhere(array('company_id' => $companyId, 'name'=>$name));
        return $rs;
    }
    //更新标签
    public function update($data){
        $id = intval($data['label_id']);
        unset($data['label_id']);
        $rs = $this->model->update($id,$data);
        return $rs;
    }
    //删除标签
    public function delete($labelId){
        $rs = $this->model->delete($labelId);
        return $rs;
    }
    //检测是否正在使用
    public function isUser($companyId,$labelId){
        $rs = true;
        $constructionModel = new Model_Building_Construction ();
        //获取公司下所有标签
        $filter = array('company_id' => $companyId);
        $list = $constructionModel->getAll('*', $filter);
        if( !empty($list)){
            foreach ($list as $key=>$value){
                $ids = explode(',',$value['label_id']);
                if( in_array($labelId, $ids)){
                    return false;
                }
            }
        }
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
