<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Staff_Check_Agree extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'checkId' => array('name' => 'check_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '审核记录ID'),
            ),
        );
    }
  
  /**
     * 审核通过该家政员申请
     * #desc 用于审核通过该家政员申请
     * #return int code 操作码，0表示成功
  */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断家政人员是否存在
        $checkDomain  = new Domain_Jiafubao_HouseStaffCheck();
        $checkInfo = $checkDomain->getBaseInfo($this->checkId);
        if( ! $checkInfo) {
            $rs['code'] = 126;
            $rs['msg'] = T('Staff not exists');
            return $rs;
        }

        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $status = $checkDomain->agreeCheckStaff($checkInfo);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }



        $rs['info']['status'] = $status;

        return $rs;
    }
    
}
