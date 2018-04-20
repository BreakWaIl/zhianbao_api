<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_TrainApply_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'staffName' => array('name' => 'staff_name', 'type' => 'string', 'require' => false, 'desc' => '家政员姓名'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
     * 获取服务培训申请列表
     * #desc 用于获取服务培训申请列表
     * #return int code 操作码，0表示成功
     * #return int id 申请ID
     * #return string staff_name 家政员名称
     * #return string status 申请状态 wait 等待, active 正常, accept 已接受, process 已处理 reject 已拒绝
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

        $filter = array();
        $filter['company_id'] = $this->companyId;
        if(!empty($this->staffName)){
            //判断家政人员是否存在
            $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
            $info = $houseStaffDomain->getBaseName($this->companyId,$this->staffName);
            if( !$info) {
                DI()->logger->debug('Staff not exists', $this->staffName);

                $rs['code'] = 126;
                $rs['msg'] = T('Staff not exists');
                return $rs;
            }
            $filter['staff_id'] = $info;
        }
        $trainApplyDomain = new Domain_Jiafubao_StaffTrainApply();
        $list = $trainApplyDomain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $total = $trainApplyDomain->getCount($filter);

        $rs['list'] = $list;
        $rs['total'] = $total;

        return $rs;
    }
	
}
