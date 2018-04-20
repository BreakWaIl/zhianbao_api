<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_HealthApply_Update extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'applyId' => array('name' => 'apply_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '申请ID'),
                'education' => array('name' => 'education', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '文化程度'),
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
     * 更新健康体检申请
     * #desc 用于更新健康体检申请
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
            'education' => $staffInfo['education'],
            'native_place' => $staffInfo['native_place_name'],
            'native_place_address' => $staffInfo['native_place_district_name'].$staffInfo['native_place_address'],
            'now_address' => $staffInfo['now_district_name'].$staffInfo['address'],
            'telephone' =>  $staffInfo['company_telephone'],
            'service_content' => json_encode($this->serviceContent),
            'service_style' => $this->serviceStyle,
            'service_year' => $this->serviceYear,
            'reservation_time' => strtotime($this->reservationTime),
            'reservation_address' => $this->reservationAddress,
            'contractors' => $this->contractors,
            'report_address' => $this->reportAddress,
            'status' => 'wait',
            'last_modify' => time(),
        );
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $res = $healthApplyDomain->updateApply($data,$applyInfo);
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
