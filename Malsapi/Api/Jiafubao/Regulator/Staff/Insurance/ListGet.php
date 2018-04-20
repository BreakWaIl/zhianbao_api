<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Regulator_Staff_Insurance_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'require' => false, 'desc' => '公司ID'),
                     'staffId' => array('name' => 'staff_id', 'type' => 'int', 'require' => false, 'desc' => '家政员ID'),
                     'title' => array('name' => 'title', 'type'=>'string', 'require'=> false,'desc'=> '标题'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
     * 获取保险记录列表
     * #desc 用于获取保险记录列表
     * #return int code 操作码，0表示成功
     * #return int id 保险记录ID
     * #return int company_id 公司ID
     * #return int staff_id 家政员ID
     * #return string title 保险类别
     * #return string insured_name 投保人
     * #return string policy_bn 保单号
     * #return int end_time 截至有效期
     * #return int create_time 创建时间
     * #return int last_modify  最后更新时间
     * #return int staff_name 家政员名称
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
        $filter['is_delete'] = 'n';
        if(!empty($this->companyId)){
            $filter['company_id'] = $this->companyId;
        }
        if(!empty($this->staffId)){
            $filter['staff_id'] = $this->staffId;
        }
        if(!empty($this->title)){
            $filter['title LIKE ?'] = '%'.$this->title.'%' ;
        }
        $staffInsuranceDomain = new Domain_Jiafubao_StaffInsurance();
        $list = $staffInsuranceDomain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $total = $staffInsuranceDomain->getCount($filter);

        $rs['list'] = $list;
        $rs['total'] = $total;

        return $rs;
    }
	
}
