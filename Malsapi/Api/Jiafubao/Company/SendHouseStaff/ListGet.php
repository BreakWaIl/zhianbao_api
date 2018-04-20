<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_SendHouseStaff_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'jfbCompanyId' => array('name' => 'jfb_company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家服云公司ID'),
                     'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => false, 'desc' => '家政员ID'),
                     'name' => array('name' => 'name', 'type'=>'string', 'min' => 0, 'require'=> false,'desc'=> '家政员姓名'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}

  /**
     * 获取推荐家政员列表
     * #desc 用于获取推荐家政员列表
     * #return int code 操作码，0表示成功
     * #return int id 员工ID
     * #return int company_id 公司ID
     * #return string name 员工姓名
     * #return string sex 性别
     * #return int create_time 创建时间
     * #return int last_modify  最后更新时间
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断公司是否存在
        $domainCompany = new Domain_Jiafubao_Company();
        $companyInfo = $domainCompany->getBaseInfoById($this->jfbCompanyId);
        if (empty($companyInfo)) {
            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }

        $filter = array('company_id' => $companyInfo['id']);
        if(!empty($this->name)){
           $filter['name'] = $this->name;
        }
        if(isset($this->staffId)){
            $filter['staff_id'] = $this->staffId;
        }
        $houseStaffDomain = new Domain_Jiafubao_CompanyShareHouseStaff();
        $list = $houseStaffDomain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $total = $houseStaffDomain->getCount($filter);
        $rs['list'] = $list;
        $rs['total'] = $total;

        return $rs;
    }

}
