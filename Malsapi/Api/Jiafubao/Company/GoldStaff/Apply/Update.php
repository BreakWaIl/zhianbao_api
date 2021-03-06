<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */

class Api_Jiafubao_Company_GoldStaff_Apply_Update extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'applyId' => array('name' => 'apply_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '申请ID'),
                'trades' => array('name' => 'trades', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '从事工种'),
                'experience' => array('name' => 'experience', 'type' => 'int', 'require' => true, 'desc' => '从事家政服务时间'),
                'skillLevel' => array('name' => 'skill_level', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '职业技能等级'),
                'remark' => array('name' => 'remark', 'type' => 'string', 'require' => false, 'desc' => '备注'),
            ),
        );
    }


    /**
     * 更新金牌家政员申请
     * #desc 用于更新金牌家政员申请
     * #return int code 操作码，0表示成功
     * #return int status 0 成功 1 失败
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
        $goldStaffDomain = new Domain_Jiafubao_CompanyGoldStaff();
        //判断是否开启申请
        $isOpen = $goldStaffDomain->regConfig($this->companyId);
        if( !$isOpen){
            $rs['code'] = 221;
            $rs['msg'] = T('Apply close');
            return $rs;
        }
        //判断申请记录是否已存在
        $goldStaffDomain = new Domain_Jiafubao_CompanyGoldStaff();
        $applyInfo = $goldStaffDomain->applyInfo($this->applyId);
        if( !$applyInfo){
            DI()->logger->debug('Apply not found', $this->applyId);

            $rs['code'] = 117;
            $rs['msg'] = T('Apply not exists');
            return $rs;
        }
        //判断申请是否审核
        if($applyInfo['status'] == 'wait'){
            $rs['code'] = 119;
            $rs['msg'] = T('Please wait for review');
            return $rs;
        }
        //判断申请是否成功
        if($applyInfo['status'] == 'success'){
            $rs['code'] = 128;
            $rs['msg'] = T('Apply is already finish');
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
        //获取当前年份
        $years = date('Y');
        //获取人员出生年份
        $birthday = substr($staffInfo['birthday'],0,4);
        $data = array(
            'apply_id' => $this->applyId,
            'name' => $staffInfo['name'],
            'trades' => $this->trades,
            'experience' => $this->experience,
            'skill_level' => $this->skillLevel,
            'birthday' => $birthday,
            'mobile' => $staffInfo['mobile'],
            'house_keep_card' => '-',
            'bank_card' => '-',
            'cardID' => $staffInfo['cardID'],
            'education' => $staffInfo['education'],
            'remark' => $this->remark,
            'years' => $years,
            'status' => 'wait',
            'last_modify' => time(),
        );
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $res = $goldStaffDomain->updateApply($data,$applyInfo);
            if($res){
                $status = 0;
            }else{
                $status = 1;
            }
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
