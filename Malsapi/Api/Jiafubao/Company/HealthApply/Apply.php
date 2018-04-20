<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_HealthApply_Apply extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'applyId' => array('name' => 'apply_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '申请ID'),
            ),
        );
    }


    /**
     * 提交健康体检申请
     * #desc 用于提交健康体检申请
     * #return int code 操作码，0表示成功
     * #return int status 0 成功 1 失败
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        //判断申请记录是否已存在
        $healthApplyDomain = new Domain_Jiafubao_StaffHealthApply();
        $applyInfo = $healthApplyDomain->getBaseInfo($this->applyId);
        if( !$applyInfo){
            DI()->logger->debug('Apply not found', $this->applyId);

            $rs['code'] = 117;
            $rs['msg'] = T('Apply not exists');
            return $rs;
        }
        if($applyInfo['status'] != 'wait'){
            $rs['code'] = 119;
            $rs['msg'] = T('Please wait for review');
            return $rs;
        }
        //判断家政人员是否存在
        $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
        $staffInfo = $houseStaffDomain->getBaseInfo($applyInfo['staff_id']);
        if( !$staffInfo) {
            DI()->logger->debug('Staff not exists', $this->staffId);

            $rs['code'] = 126;
            $rs['msg'] = T('Staff not exists');
            return $rs;
        }//判断家政人员是否审核
        if($staffInfo['is_check'] == 'n'){
            $rs['code'] = 179;
            $rs['msg'] = T('Staff not review');
            return $rs;
        }

        $data = array(
            'apply_id' => $this->applyId,
            'status' => 'active',
            'last_modify' => time(),
        );
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $res = $healthApplyDomain->submitApply($data,$applyInfo);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }
        if($res){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info']['status'] = $status;

        return $rs;
    }

}
