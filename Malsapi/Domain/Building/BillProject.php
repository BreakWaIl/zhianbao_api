<?php
class Domain_Building_BillProject {
    var $model;

    public function __construct() {
        $this->model = new Model_Building_BillProject();

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
            //获取项目信息
            $projectModel = new Model_Building_Project();
            $projectInfo = $projectModel->get($rs['project_id']);
            $rs['project_name'] = $projectInfo['name'];
        }

        return $rs;
    }
    //添加项目出入账
    public function add($data,$projectInfo){
        $rs = $this->model->insert($data);
        if( !$rs){
            throw new LogicException (T('Add failed'), 102);
        }else{
            //更新项目开支
            $projectModel = new Model_Building_Project();
            //收入
            if($data['type'] == 'income'){
                $amount = $projectInfo['amount'] + $data['amount'];;
            }
            //支出
            if($data['type'] == 'expenditure'){
                $amount = $projectInfo['amount'] - $data['amount'];
            }
            //借支
            if($data['type'] == 'borrow'){
                $amount = $projectInfo['amount'] - $data['amount'];
            }

            $project_data = array(
                'amount' => $amount,
                'last_modify' => time(),
            );
            $res = $projectModel->update($projectInfo['id'],$project_data);
            if(! $res){
                throw new LogicException (T('Add failed'), 102);
            }
        }
        return $rs;
    }
    //更新项目出入账
    public function update($data,$billInfo,$projectInfo){
        $id = intval($data['bill_id']);
        unset($data['bill_id']);
        $rs = $this->model->update($id,$data);
        if( !$rs){
            return false;
        }else{
            //更新项目开支
            $projectModel = new Model_Building_Project();
            //比较原始金额和更新后的金额
            if($billInfo['amount'] > $data['amount']){
                $amount = $projectInfo['amount'] + ($billInfo['amount'] - $data['amount']);
            }else{
                $amount = $projectInfo['amount'] + ($data['amount'] - $billInfo['amount']);
            }
            $project_data = array(
                'amount' => $amount,
                'last_modify' => time(),
            );
            $res = $projectModel->update($projectInfo['id'],$project_data);
            if(! $res){
                return false;
            }
        }
        return $rs;
    }
    //获取列表
    public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
        $projectModel = new Model_Building_Project();
        $rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key=>$value){
            $projectInfo = $projectModel->get($value['project_id']);
            $rs[$key]['project_name'] = $projectInfo['name'];
        }
        return $rs;
    }
    //获取数量
    public function getCount($filter) {
        return $this->model->getCount ( $filter );
    }


}
