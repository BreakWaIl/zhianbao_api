<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Regulator_HealthApply_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'require' => false, 'desc' => '公司ID'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
   * 获取健康体检申请列表
   * #desc 用于获取健康体检申请列表
   * #return int code 操作码，0表示成功
   * #return int id 申请ID
   * #return string staff_name 家政员名称
   * #return string reservation_time 预约时间
   * #return string reservation_address 预约地点
   * #return string status 申请状态 wait 等待, active 正常, accept 已接收, process 已归档 reject 已驳回
   * #return int create_time 创建时间
   * #return int last_modify  最后更新时间
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
//        $filter['status'] = array('active','accept','process','reject');
        $healthApplyDomain = new Domain_Jiafubao_StaffHealthApply();
        $list = $healthApplyDomain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $total = $healthApplyDomain->getCount($filter);
        $rs['count'] = $total;
        $rs['list'] = $list;
        return $rs;
    }
	
}
