<?php
class Domain_Zhianbao_Part {
	var $model;

	public function __construct() {
		$this->model = new Model_Zhianbao_Part ();
	}

	//获取详情
    public function getBaseInfo($safeId, $cols = '*'){
        $rs = array ();
        $id = intval ( $safeId );
        if ($id <= 0) {
            return $rs;
        }

        $rs = $this->model->get ( $id);

        if (! $rs){
            return false;
        }

        return $rs;
    }
    //添加角色
    public function addPart($data){
        $rs = $this->model->insert($data);
        return $rs;
    }
    //更新角色
    public function updatePart($data){
        $id = intval($data['part_id']);
        unset($data['part_id']);
        $rs = $this->model->update($id,$data);
        return $rs;
    }
    //检测角色名称
    public function isUser($regulatorId,$name){
        $filter = array('regulator_id' => $regulatorId, 'name' => $name);
        $rs = $this->model->getByWhere($filter,'*');
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
    //企业获取角色类型列表
    public function getAllPart($filter){
        $list = $this->model->getAll('*',$filter);
        return $list;
    }
}
