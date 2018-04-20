<?php
class Domain_Zhianbao_Organization {
	var $model;

	public function __construct() {
		$this->model = new Model_Zhianbao_Organization ();
	}

	//获取详情
    public function getBaseInfo($layoutId, $cols = '*'){
        $rs = array ();
        $id = intval ( $layoutId );
        if ($id <= 0) {
            return $rs;
        }

        $rs = $this->model->get ( $id);

        if (! $rs){
            return false;
        }

        return $rs;
    }
    //添加安全组织结构
    public function addOrganization($data){
        $rs = $this->model->insert($data);
        return $rs;
    }
    //更新安全组织结构
    public function updateOrganization($data){
        $id = intval($data['layout_id']);
        unset($data['layout_id']);
        $rs = $this->model->update($id,$data);
        return $rs;
    }
    //删除安全组织结构
    public function deleteOrganization($layoutId){
        $rs = $this->model->delete($layoutId);
        return $rs;
    }
    //获取列表
	public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
		$rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        $companyModel = new Model_Zhianbao_Company();
        foreach ($rs as$key=>$value){
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
