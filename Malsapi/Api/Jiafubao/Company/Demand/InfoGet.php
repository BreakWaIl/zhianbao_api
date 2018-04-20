<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_Demand_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家政员ID'),
            ),
        );
    }
  
  /**
     * 获取家政员业务需求信息
     * #desc 用于家政员业务需求信息
     * #return int code 操作码，0表示成功
     * #return int id 需求ID
      * #return int company_id 公司ID
     * #return int staff_id 家政员ID
     * #return array demand 工作范围
     * #return string expected_salary 期望薪酬
     * #return string good_cuisine 擅长菜系
     * #return string cook_taste 做饭口味
     * #return string is_home 是否住家:y 住家 n 不住家
     * #return array work_time 工作时间
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

        //获取家政员业务需求;
        $demandDomain = new Domain_Jiafubao_StaffDemand();
        $demandInfo = $demandDomain->getBaseInfo($this->companyId,$this->staffId);

        $rs['info'] = $demandInfo;

        return $rs;
    }
    
}
