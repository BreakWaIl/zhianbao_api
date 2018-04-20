<?php
class Domain_Zhianbao_RegulatorConfig {
	var $model;

	public function __construct($regulatorId) {
		$this->model = new Model_Zhianbao_RegulatorConfig ();
        $this->regulatorId = $regulatorId;
	}

    public function set($key,$value){

        $get = $this->get($key);
        $data = array(
            'regulator_id' => $this->regulatorId,
            'config_key' => $key,
            'config_value' => $value
        );
        if(empty($get)){
            $rs = $this->model->insert ( $data );
        }else{
            $rs = $this->model->updateByWhere (array('regulator_id' => $this->regulatorId,'config_key' => $key), $data );
        }
        if (! $rs) {
            return false;
        }
    }
    public function get($key){
        $filter = array(
            'regulator_id' => $this->regulatorId,
            'config_key' => $key
        );
        $rs = $this->model->getByWhere($filter);
        if($rs){
            return $rs['config_value'];
        }else{
            return false;
        }
    }



}
