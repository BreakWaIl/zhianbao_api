<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_TrainApply_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家政员ID'),
                'age' => array('name' => 'age', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '年龄'),
//                'nation' => array('name' => 'nation', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '民族'),
//                'marriage' => array('name' => 'marriage', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '婚姻情况'),
//                'education' => array('name' => 'education', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '学历'),
//                'industry' => array('name' => 'industry', 'type'=>'string', 'min' => 1,  'require'=> true,'desc'=> '专业'),
//                'address' => array('name' => 'address', 'type' => 'string', 'min'=> 1, 'require' => true, 'desc' => '联系地址'),
//                'telephone' => array('name' => 'telephone', 'type'=>'string','min' => 1, 'require'=> true, 'desc' => '联系电话'),
                'objective' => array('name' => 'objective',  'type' => 'array', 'format'=>'json', 'require'=> true, 'desc' => '求职意向'),
                'workService' => array('name' => 'work_service', 'type' => 'array','format'=>'json', 'require'=> true, 'desc' => '所选培训服务和费用'),
                'totalCost' => array('name' => 'total_cost',  'type' => 'float', 'min' => 1, 'require'=> true, 'desc' => '总费用'),
            ),
        );
    }


    /**
     * 添加服务培训申请
     * #desc 用于添加服务培训申请
     * #return int code 操作码，0表示成功
     * #return int apply_id  申请ID
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
        if(empty($staffInfo['nation']) || empty($staffInfo['marriage']) || empty($staffInfo['education'])){
            $rs['code'] = 191;
            $rs['msg'] = T('Please improve staff information');
            return $rs;
        }
        $trainApplyDomain = new Domain_Jiafubao_StaffTrainApply();
        $data = array(
            'company_id' => $this->companyId,
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
            'create_time' => time(),
            'last_modify' => time(),
        );
        $applyId = 0;
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $applyId = $trainApplyDomain->addApply($data);
            if(!$applyId){
                $rs['code'] = 102;
                $rs['msg'] = T('Add failed');
                return $rs;
            }
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        $rs['info']['apply_id'] = $applyId;

        return $rs;
    }

}
