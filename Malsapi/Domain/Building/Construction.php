<?php
class Domain_Building_Construction {
    var $model;

    public function __construct() {
        $this->model = new Model_Building_Construction();

    }

    public function getBaseInfo($logId, $cols = '*') {
        $rs = array ();
        $id = intval ( $logId );
        if ($id <= 0) {
            return $rs;
        }

        $rs = $this->model->get ($id);
        if( !$rs){
            return false;
        }else{
            if($rs['day'] == 'null'){
                $rs['day'] = '';
            }else{
                $rs['day'] = json_decode($rs['day'],true);
            }
            if($rs['night'] == 'null'){
                $rs['night'] = '';
            }else{
                $rs['night'] = json_decode($rs['night'],true);
            }
            $rs['dateTime'] = date("Y-m-d H:i:s", $rs['dateTime']);
            $rs['create_time'] = date("Y-m-d H:i:s", $rs['create_time']);
            $rs['last_modify'] = date("Y-m-d H:i:s", $rs['last_modify']);
            //获取标签信息
            $labelModel = new Model_Building_Label();
            $ids = explode(',',$rs['label_id']);
            $label = array();
            foreach ($ids as $kk=>$vv){
                $info = $labelModel->get($vv);
                $label[$kk]['id'] = $vv;
                $label[$kk]['name'] = $info['name'];
            }
            $rs['label_info'] = $label;
            //获取项目信息
            $projectModel = new Model_Building_Project();
            $projectInfo = $projectModel->get($rs['project_id']);
            $rs['project_name'] = $projectInfo['name'];
            //获取负责人信息
            $userModel = new Model_Zhianbao_User();
            $userInfo = $userModel->get($rs['recorder']);
            $rs['recorder'] = $userInfo['name'];
        }

        return $rs;
    }
    //添加施工日志
    public function add($data){
        $rs = $this->model->insert($data);
        if(! $rs){
            throw new LogicException ( T ( 'Add failed' ), 102 );
        }
        return $rs;
    }
    //获取列表
    public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
        $labelModel = new Model_Building_Label();
        if(isset($filter['label_id'])){
            $ids = $this->checkLabel($filter);
            unset($filter['label_id']);
            $filter['id'] = $ids;
        }
        $projectModel = new Model_Building_Project();
        $rs = $this->model->getAll ( 'id,project_id,label_id,dateTime,recorder,create_time,last_modify', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key=>$value){
            $rs[$key]['dateTime'] = date("Y-m-d H:i:s", $value['dateTime']);
            //获取标签信息
            $ids = explode(',',$value['label_id']);
            $label = array();
            foreach ($ids as $kk=>$vv){
                $info = $labelModel->get($vv);
                $label[$kk]['id'] = $vv;
                $label[$kk]['name'] = $info['name'];
            }
            $rs[$key]['label_info'] = $label;
            //获取项目信息
            $info = $projectModel->get($value['project_id']);
            $rs[$key]['project_name'] = $info['name'];
            //获取负责人信息
            $userModel = new Model_Zhianbao_User();
            $userInfo = $userModel->get($value['recorder']);
            $rs[$key]['recorder'] = $userInfo['name'];
        }
        return $rs;
    }
    //获取数量
    public function getCount($filter) {
        return $this->model->getCount ( $filter );
    }
    function checkLabel($filter){
        $rs = array();
        $labelId = $filter['label_id'];
        unset($filter['label_id']);
        $list = $this->model->getAll ( 'id,label_id', $filter);
        foreach ($list as $key=>$value){
            $ids = explode(',',$value['label_id']);
            if( in_array($labelId, $ids)){
                $rs[$key][] = $value['id'];
            }
        }
        return $rs;
    }
    public function labelInfo($filter){
        $labelModel = new Model_Building_Label();
        $ids = explode(',',$filter['label_id']);
        unset($filter['label_id']);
        foreach ($ids as $key=>$value){
            $filter['id'] = $value;
            $info = $labelModel->getByWhere($filter,'*');
            if(empty($info)){
                return false;
            }
        }
        return true;
    }

    function subInfo($companyInfo,$recorder){
        $rs = array();
        $userModel = new Model_Zhianbao_User();
        $filter = array('parent_id' => $companyInfo['user_id']);
        $filter[ 'name LIKE ?'] = '%'.$recorder.'%';
        $list = $userModel->getAll('id,group_id,parent_id,name,login_name',$filter);
        foreach ($list as $key=>$value){
            $rs[$key] = $value['id'];
        }
        return $rs;
    }
}
