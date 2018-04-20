<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_HealthApply_Get extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家政员ID'),
            ),
        );
    }
  
  /**
     * 获取是否存在未归档的体检申请详情
     * #desc 用于获取是否存在未归档的体检申请详情
     * #return int code 操作码，0表示成功
   */

    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断家政人员是否存在
        $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
        $staffInfo = $houseStaffDomain->getBaseInfo($this->staffId);
        if( !$staffInfo) {
            DI()->logger->debug('Staff not exists', $this->staffId);

            $rs['code'] = 126;
            $rs['msg'] = T('Staff not exists');
            return $rs;
        }

        //判断申请记录是否已存在
        $healthApplyDomain = new Domain_Jiafubao_StaffHealthApply();
        $filter = array('company_id' => $this->companyId, 'staff_id' => $this->staffId);
        $applyInfo = $healthApplyDomain->detect($filter);
        if(!empty($applyInfo)){
            $rs['code'] = 183;
            $rs['msg'] = T('Apply have been not process');
        }
        $rs['info'] = $applyInfo;

        return $rs;
    }
    
}
