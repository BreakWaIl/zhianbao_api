<?php
class Domain_Jiafubao_CompanyShareHouseStaff {
	var $model;

	public function __construct() {
		$this->model = new Model_Jiafubao_CompanyShareHouseStaff();
	}

	//获取详情
    public function getBaseInfo($staffId, $cols = '*'){}

    //获取列表
	public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
		$rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        $domainArea = new Domain_Area();
		foreach ($rs as $key => $value){
            if($value['native_place'] > 0){
                //籍贯
                $rs[$key]['native_place'] = $domainArea->getAreaNameById($value['native_place']);
            }else{
                $rs[$key]['native_place'] = '';
            }
        }
		return $rs;
	}
	//获取数量
	public function getCount($filter) {
        $count = $this->model->getCount ( $filter );
		return $count;
	}
	//删除共享的家政员
    public function deleteShareStaff($filter){
        //删除分享的家政员
        $staffRs = $this->model->deleteByWhere($filter);
        if(! $staffRs){
            throw new LogicException ( T ( 'Delete failed' ), 105 );
        }
        //删除分享的记录
        $sendModel = new Model_Jiafubao_OrderSend();
        $sendRs = $sendModel->updateByWhere($filter,array('status' => 'delete','last_modify' => time()));
        if(! $sendRs){
            throw new LogicException ( T ( 'Delete failed' ), 105 );
        }
        return true;
    }
    //添加备注
    public function addMark($filter,$mark){
        $data = array(
            'mark' => $mark,
            'last_modify' => time()
        );
        $rs = $this->model->updateByWhere($filter,$data);
        return $rs;
    }
}
