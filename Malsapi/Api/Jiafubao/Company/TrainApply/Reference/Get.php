<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_TrainApply_Reference_Get extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家政员ID'),
            ),
        );
    }
  
  /**
     * 获取是否存在未归档的培训申请
     * #desc 用于获取是否存在未归档的培训申请
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

        //获取家政员专项技能证书
        $staffCertDomain = new Domain_Jiafubao_StaffAbilityCert();
        $filter = array('company_id' => $this->companyId, 'staff_id' => $this->staffId);
        $list = $staffCertDomain->getAllCert($filter);

        $rs['list'] = $list;

        return $rs;
    }
    
}
