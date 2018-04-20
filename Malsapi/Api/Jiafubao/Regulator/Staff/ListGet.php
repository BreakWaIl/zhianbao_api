<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Regulator_Staff_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'require' => false, 'desc' => '公司ID'),
                     'name' => array('name' => 'name','type'=>'string','require'=> false,'desc'=> '家政员姓名'),
                     'staffMobile' => array('name' => 'staff_mobile','type'=>'string','require'=> false,'desc'=> '手机号'),
                     'cardID' => array('name' => 'cardID','type'=>'string','require'=> false,'desc'=> '身份证号码'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
   * 获取家政员列表
   * #desc 用于获取家政员列表
   * #return int code 操作码，0表示成功
 */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //检测监管者是否存在
        $regulatorDomain = new Domain_Zhianbao_Regulator();
        $regulatorInfo = $regulatorDomain->getBaseInfo($this->regulatorId);
        if(! $regulatorInfo){
            $rs['code'] = 118;
            $rs['msg'] = T('Regulator not exists');
            return $rs;
        }

        $companyIds = $regulatorDomain->getCompanyIds($this->regulatorId);
        $filter = array('company_id' => $companyIds);
        if(!empty($this->companyId)){
            $filter['company_id'] = $this->companyId;
        }
        if(!empty($this->name)){
            $filter['name LIKE ?'] = '%'.$this->name.'%' ;
        }
        if(!empty($this->staffMobile)){
            $filter['mobile LIKE ?'] = '%'.$this->staffMobile.'%' ;
        }
        if(!empty($this->cardID)){
            $filter['cardID LIKE ?'] = $this->cardID.'%' ;
        }
        $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
        $list = $houseStaffDomain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $total = $houseStaffDomain->getCount($filter);

        $rs['count'] = $total;
        $rs['list'] = $list;
        return $rs;
    }
	
}
