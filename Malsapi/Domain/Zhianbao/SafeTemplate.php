<?php
class Domain_Zhianbao_SafeTemplate {
    var $model;

    public function __construct() {
        $this->model = new Model_Zhianbao_SafeTemplate ();
    }

    //获取通知详情
    public function getBaseInfo($templateId, $cols = '*'){
        $rs = array ();
        $id = intval ( $templateId );
        if ($id <= 0) {
            return $rs;
        }

        $rs = $this->model->get ( $id);

        if (! $rs){
            return false;
        }

        return $rs;
    }
    //添加模板
    public function addTemplate($data){
        $rs = $this->model->insert($data);
        return $rs;
    }
    //更新模板
    public function updateTemplate($data){
        $id = intval($data['template_id']);
        unset($data['template_id']);
        $rs = $this->model->update($id,$data);
        return $rs;
    }
    //删除模板
    public function deleteTemplate($templateId){
        $rs = $this->model->delete($templateId);
        return $rs;
    }
    //获取模板列表
    public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
        $rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        return $rs;
    }
    //获取数量
    public function getCount($filter) {
        return $this->model->getCount ( $filter );
    }

    //企业获取模板列表
    public function getTemplate($filter){
        $rs = array();
        $regToCustomerModel = new Model_Zhianbao_RegulatorToCustomer();
        $info = $regToCustomerModel->getByWhere($filter);
        if(!empty($info)){
            $to_filter = array('regulator_id' => $info['regulator_id']);
            $rs = $this->model->getAll('*',$to_filter);
        }
        return $rs;
    }
    public function isUser($regulatorId,$templateId){
        $rs = array();
        $templateModel = new Model_Zhianbao_SafeApply();
        $regulatorToCustomerModel = new Model_Zhianbao_RegulatorToCustomer();
        $filter = array('regulator_id' => $regulatorId);
        $list = $regulatorToCustomerModel->getAll('*',$filter);
        foreach ($list as $key=>$value){
            $to_filter = array('company_id' => $value['company_id'],'template_id' =>$templateId);
            $rs = $templateModel->getAll('*', $to_filter);
            if(!empty($rs)){
                return $rs;
            }
        }
        return $rs;
    }
}
