<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_GoldStaff_Apply_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'years' => array('name' => 'years','type'=>'int','require'=> false,'desc'=> '申请年份'),
                     'status' => array('name' => 'status','type'=>'enum', 'range'=>array('wait','success','refuse','all'), 'default'=>'all', 'require'=> true,'desc'=> '状态:wait 等待 success 成功 refuse 拒绝'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
     * 获取金牌家政员申请列表
     * #desc 用于获取金牌家政员申请列表
     * #return int code 操作码，0表示成功
     * #return int id 金牌ID
     * #return string name 员工姓名
     * #return string trades 从事工种
     * #return string experience 从事家政服务时间
     * #return string skill_level 职业技能等级
     * #return string years 申请年份
     * #return string status 状态:wait 等待 success 成功 refuse 拒绝
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

        $filter = array();
        $filter['company_id'] = $this->companyId;
        if(!empty($this->years)){
            $filter['years'] = $this->years;
        }
        if($this->status != 'all'){
            $filter['status'] = $this->status;
        }

        $goldStaffDomain = new Domain_Jiafubao_CompanyGoldStaff();
        $list = $goldStaffDomain->applyGetAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $total = $goldStaffDomain->applyGetCount($filter);

        $rs['list'] = $list;
        $rs['total'] = $total;

        return $rs;
    }
	
}
