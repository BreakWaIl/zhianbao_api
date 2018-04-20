<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Attendance_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'staffUrl' => array('name' => 'staff_url', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '员工URL'),
                     'operateId' => array('name' => 'operate_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '操作人ID'),
            ),
        );
    }
  
  /**
     * 获取员工信息，并打卡签到
     * #desc 用于获取当前员工信息，并打卡签到
     * #return int code 操作码，0表示成功
  */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断公司是否存在
        $domainCompany = new Domain_Zhianbao_Company();
        $companyInfo = $domainCompany->getBaseInfo($this->companyId);
        if (empty($companyInfo)) {
            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }
        $info = array();
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $AttendanceDomain = new Domain_Building_Attendance();
            $info = $AttendanceDomain->getQrCodeInfo($this->staffUrl,$this->companyId,$this->operateId);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        $rs['info'] = $info;

        return $rs;
    }
    
}
