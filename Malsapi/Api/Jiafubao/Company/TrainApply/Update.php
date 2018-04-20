<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_TrainApply_Update extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '员工ID'),
                'applyId' => array('name' => 'apply_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '申请ID'),
                'age' => array('name' => 'age', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '年龄'),
//                'nation' => array('name' => 'nation', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '民族'),
//                'marriage' => array('name' => 'marriage', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '婚姻情况'),
//                'education' => array('name' => 'education', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '学历'),
//                'industry' => array('name' => 'industry', 'type'=>'string', 'min' => 1,  'require'=> true,'desc'=> '专业'),
//                'address' => array('name' => 'address', 'type' => 'string', 'min'=> 1, 'require' => true, 'desc' => '联系地址'),
//                'telephone' => array('name' => 'telephone', 'type'=>'string','min' => 1, 'require'=> true, 'desc' => '联系电话'),
                'objective' => array('name' => 'objective',  'type' => 'array', 'format'=>'json', 'require'=> true, 'desc' => '求职意向'),
                'workService' => array('name' => 'work_service', 'type' => 'array','format'=>'json', 'require'=> true, 'desc' => '所选培训服务和费用'),
                'totalCost' => array('name' => 'total_cost',  'type' => 'float', 'require'=> true, 'desc' => '总费用'),
            ),
        );
    }


    /**
     * 更新健康体检申请
     * #desc 用于更新健康体检申请
     * #return int code 操作码，0表示成功
     * #return int status 0 成功 1 失败
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        //判断申请记录是否已存在
        $trainApplyDomain = new Domain_Jiafubao_StaffTrainApply();
        $applyInfo = $trainApplyDomain->getBaseInfo($this->applyId);
        if( !$applyInfo) {
            DI()->logger->debug('Apply not found', $this->applyId);

            $rs['code'] = 117;
            $rs['msg'] = T('Apply not exists');
            return $rs;
        }
        //wait 等待, active 正常, accept 已接受, process 已处理 reject 已拒绝
        if($applyInfo['status'] != 'wait' && $applyInfo['status'] != 'reject'){
            $rs['code'] = 120;
            $rs['msg'] = T('Ban apply update');
            return $rs;
        }
        //判断家政人员是否存在
        $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
        $staffInfo = $houseStaffDomain->getBaseInfo($applyInfo['staff_id']);
        if( !$staffInfo) {
            $rs['code'] = 126;
            $rs['msg'] = T('Staff not exists');
            return $rs;
        }
        $data = array(
            'apply_id' => $this->applyId,
            'staff_id' => $this->staffId,
            'age' => $this->age,
            'nation' => $staffInfo['nation'],
            'marriage' => $staffInfo['marriage'],
            'education' => $staffInfo['education'],
            'industry' => $staffInfo['industry'],
            'address' => $staffInfo['now_district_name'].$staffInfo['address'],
            'company_telephone' => $staffInfo['company_telephone'],
            'work_objective' => json_encode($this->objective),
            'work_service' => json_encode($this->workService),
            'total_cost' => $this->totalCost,
            'status' => 'wait',
            'last_modify' => time(),
        );
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $res = $trainApplyDomain->updateApply($data,$applyInfo);
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
