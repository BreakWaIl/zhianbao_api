<?php
class Domain_Jiafubao_CompanySetting {

    public function __construct() {
        $this->model = new Model_Jiafubao_CompanySetting();
    }

    public function set($companyId,$key,$value){
        $filter = array(
            'company_id' => $companyId,
            'c_key' => $key
        );
        $had = $this->model->getByWhere($filter);
        if($had){
            $data = array(
                'c_value' => $value
            );
            $rs = $this->model->updateByWhere($filter,$data);
        }else{
            $data = array(
                'company_id' => $companyId,
                'c_key' => $key,
                'c_value' => $value,
                'create_time' => time(),
            );
            $rs = $this->model->insert($data);
        }
        return $rs;
    }
    public function get($companyId,$key){
        $filter = array(
            'company_id' => $companyId,
            'c_key' => $key
        );
        $return = $this->model->getByWhere($filter);
        $rs = $return['c_value'];
        return $rs;
    }

}
