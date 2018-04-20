<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_StaffInsurance_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家政员ID'),
                     'title' => array('name' => 'title', 'type'=>'string', 'min' => 0, 'require'=> false,'desc'=> '标题'),
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
     * #return int staff_name 家政员名称
     * #return string content 内容
     * #return int end_time 截至有效期
     * #return int create_time 创建时间
     * #return int last_modify  最后更新时间
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断公司是否存在
        $domainCompany = new Domain_Zhianbao_Company();
        $companyInfo = $domainCompany->getBaseInfo($this->companyId);
        if (empty($companyInfo)) {
            DI()->logger->debug('Company not exists', $this->companyId);

            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }

        //判断家政人员是否存在
        $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
        $staffInfo = $houseStaffDomain->getBaseInfo($this->staffId);
        if( !$staffInfo) {
            DI()->logger->debug('Staff not exists', $this->staffId);

            $rs['code'] = 126;
            $rs['msg'] = T('Staff not exists');
            return $rs;
        }

        $filter = array();
        $filter['company_id'] = $this->companyId;
        $filter['staff_id'] = $this->staffId;
        $filter['is_delete'] = 'n';
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
