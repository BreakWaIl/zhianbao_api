<?php
class Domain_Zhianbao_Complaint {
	var $model;

	public function __construct() {
		$this->model = new Model_Zhianbao_Complaint ();
	}

	//获取通知详情
    public function getBaseInfo($complaintId, $cols = '*'){
        $rs = $this->model->get ( $complaintId , $cols);
        $companyModel = new Model_Zhianbao_Company();
        $companyInfo = $companyModel->get($rs['company_id']);
        $rs['company_name'] = $companyInfo['name'];
        return $rs;
    }
    //添加证件
    public function addComplaint($data){
        $agentInfo = DI ()->cookie->get('zab_agent');
        $agentInfo = json_decode($agentInfo,true);
        if(isset($agentInfo)){
            $openId = $agentInfo['openid'];
        }else{
            throw new LogicException ( T ( 'Get openid failed' ) , 149 );
        }
        $data['openid'] = $openId;
        $rs = $this->model->insert($data);
        return $rs;
    }
    //更新证件
    public function updateComplaint($data){
        $id = intval($data['safe_id']);
        unset($data['safe_id']);
        $rs = $this->model->update($id,$data);
        return $rs;
    }
    //删除证件
    public function deleteComplaint($safeId){
        $rs = $this->model->delete($safeId);
        return $rs;
    }
    //获取列表
	public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
		$rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        $companyModel = new Model_Zhianbao_Company();
        foreach ($rs as $key => $value){
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
