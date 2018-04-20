<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_HealthApply_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家政员ID'),
//                'education' => array('name' => 'education', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '文化程度'),
//                'nativePlace' => array('name' => 'native_place', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '籍贯'),
//                'hometown' => array('name' => 'hometown', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '出生地'),
//                'placeAddress' => array('name' => 'native_place_address', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '户籍所在地'),
//                'nowDistrict' => array('name' => 'now_district', 'type'=>'string', 'min' => 1,  'require'=> true,'desc'=> '现居住地区'),
//                'nowAddress' => array('name' => 'now_address', 'type' => 'string', 'min'=> 1, 'require' => true, 'desc' => '联系地址'),
//                'telephone' => array('name' => 'telephone', 'type'=>'string', 'min' => 0, 'require'=> false,'desc'=> '联系电话'),
                'serviceContent' => array('name' => 'service_content', 'type' => 'array', 'format'=>'json', 'require'=> true, 'desc' => '服务内容'),
                'serviceStyle' => array('name' => 'service_style', 'type'=>'enum','range' => array('time','home','all'), 'default' => 'time', 'require'=> true,'desc'=> '服务形式: time 小时工 home 住家制 all 全日制'),
                'serviceYear' => array('name' => 'service_year', 'type'=>'int', 'min' => 1, 'require'=> true,'desc'=> '从事服务年限'),
                'reservationTime' => array('name' => 'reservation_time', 'type'=>'string','min' => 1, 'require'=> true, 'desc' => '预约时间'),
                'reservationAddress' => array('name' => 'reservation_address', 'type'=>'string', 'min' => 1, 'require'=> true, 'desc' => '预约地点'),
                'contractors' => array('name' => 'contractors', 'type'=>'string', 'min' => 1, 'require'=> true, 'desc' => '承接单位'),
                'reportAddress' => array('name' => 'report_address', 'type'=>'string', 'min' => 1, 'require'=> true, 'desc' => '报告送达地址'),
            ),
        );
    }


    /**
     * 添加健康体检申请
     * #desc 用于添加健康体检申请
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
        $data = array(
            'company_id' => $this->companyId,
            'staff_id' => $this->staffId,
            'education' => $staffInfo['education'],
            'native_place' => $staffInfo['native_place_name'],
            'native_place_address' => $staffInfo['native_place_district_name'].$staffInfo['native_place_address'],
            'now_address' => $staffInfo['now_district_name'].$staffInfo['address'],
            'telephone' => $staffInfo['company_telephone'],
            'service_content' => json_encode($this->serviceContent),
            'service_style' => $this->serviceStyle,
            'service_year' => $this->serviceYear,
            'reservation_time' => strtotime($this->reservationTime),
            'reservation_address' => $this->reservationAddress,
            'contractors' => $this->contractors,
            'report_address' => $this->reportAddress,
            'create_time' => time(),
            'last_modify' => time(),
        );
        $applyId = 0;
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $healthApplyDomain = new Domain_Jiafubao_StaffHealthApply();
            $applyId = $healthApplyDomain->addApply($data);
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
