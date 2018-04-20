<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_HealthCard_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家政员ID'),
                     'bn' => array('name' => 'bn', 'type' => 'string', 'require' => false, 'desc' => '编号'),
                     //'status' => array('name' => 'status', 'type' => 'enum','range'=>array('y','n'), 'default'=>'y','require'=> false,'desc'=> '状态'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
     * 获取健康卡列表
     * #desc 用于获取健康卡列表
     * #return int code 操作码，0表示成功
     * #return int id 证书ID
     * #return int company_id 公司ID
     * #return int staff_id 家政员ID
     * #return string health_level 健康情况
     * #return string card_bn 健康卡号
     * #return string train_course 培训内容
     * #return int train_time 培训时间
     * #return string train_periods 培训课时
     * #return string train_score 培训成绩
     * #return string train_organization 培训机构
     * #return string is_default 是否默认使用 y 是 n 否
     * #return int create_time 创建时间
     * #return int last_modify  最后更新时间
     * #return array staff_info 家政员信息
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
        $info = $houseStaffDomain->getBaseInfo($this->staffId);
        if( !$info) {
            DI()->logger->debug('Staff not exists', $this->staffId);

            $rs['code'] = 126;
            $rs['msg'] = T('Staff not exists');
            return $rs;
        }

        $filter = array();
        $filter['company_id'] = $this->companyId;
        $filter['staff_id'] = $this->staffId;
        if(!empty($this->bn)){
            $filter['card_bn LIKE ?'] = '%'.$this->bn.'%' ;
        }
//        if(!empty($this->status)){
//            $filter['status'] = $this->status;
//        }

        $healthCardDomain = new Domain_Jiafubao_StaffHealthCard();
        $list = $healthCardDomain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $total = $healthCardDomain->getCount($filter);

        $rs['list'] = $list;
        $rs['total'] = $total;

        return $rs;
    }

}
