<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_HealthCard_Ban extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家政员ID'),
                     'healthId' => array('name' => 'health_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '健康卡ID'),
            ),
		);
 	}
	
  
  /**
     * 作废健康卡
     * #desc 用于作废健康卡
     * #return int code 操作码，0表示成功
     * #return int status 状态 0 成功, 1 失败
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
        //检测健康卡是否已存在
        $healthCardDomain = new Domain_Jiafubao_StaffHealthCard();
        $healthInfo = $healthCardDomain->getBaseInfo($this->healthId);
        if( !$healthInfo){
            $rs['code'] = 152;
            $rs['msg'] = T('health card not exists');
            return $rs;
        }

        $res = $healthCardDomain->banHealthCard($this->healthId);
        if( $res){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info']['status'] = $status;

        return $rs;
    }
	
}
