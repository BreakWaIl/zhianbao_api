<?php
class Domain_Zhianbao_SafeGrade {
    var $model;

    public function __construct() {
        $this->model = new Model_Zhianbao_SafeCompanyGrade ();
    }

    //获取详情
    public function getBaseInfo($companyId,$companyInfo){
        $rs = array();
        $filter = array('company_id' => $companyId);
        $applyModel = new Model_Zhianbao_SafeApply();
        $res = $applyModel->getByWhere($filter, '*');
        if(!empty($res)){
            if($res['status'] != 'finish'){
                $rs['is_apply'] = 'n';
            }else{
                $rs = $this->model->getByWhere($filter, '*');
                if($rs['next_apply_time'] < time()){
                    $rs['is_apply'] = 'y';
                }
                $rs['issue_time'] = $rs['issue_time'] == 0 ? '': date('Y-m-d H:i:s',$rs['issue_time']);
                $rs['complete_time'] = $rs['complete_time'] == 0 ? '': date('Y-m-d H:i:s',$rs['complete_time']);
                $rs['create_time'] = $rs['create_time'] == 0 ? '': date('Y-m-d H:i:s',$rs['create_time']);
                $rs['end_time'] = $rs['end_time'] == 0 ? '': date('Y-m-d H:i:s',$rs['end_time']);
                $rs['apply_time'] = $rs['apply_time'] == 0 ? '': date('Y-m-d H:i:s',$rs['apply_time']);
                $rs['next_apply_time'] = $rs['next_apply_time'] == 0 ? '': date('Y-m-d H:i:s',$rs['next_apply_time']);
                $rs['company_name'] = $companyInfo['name'];
            }
        }else{
            $rs['is_apply'] = 'y';
        }
        return $rs;
    }

    //获取等级列表
    public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
        $companyModel = new Model_Zhianbao_Company();
        $rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key=>$value){
            $rs[$key]['issue_time'] = date('Y-m-d H:i:s',$value['issue_time']);
            $rs[$key]['complete_time'] = date('Y-m-d H:i:s',$value['complete_time']);
            $rs[$key]['apply_time'] = date('Y-m-d H:i:s',$value['apply_time']);
            $rs[$key]['next_apply_time'] = date('Y-m-d H:i:s',$value['next_apply_time']);
            $companyInfo = $companyModel->get($value['company_id']);
            $rs[$key]['company_name'] = $companyInfo['name'];
        }
        return $rs;
    }
    //获取数量
    public function getCount($filter){
        return $this->model->getCount ( $filter );
    }
}
