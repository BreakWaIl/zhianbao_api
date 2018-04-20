<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_HealthCard_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家政员ID'),
//                'condition' => array('name' => 'condition', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '健康情况'),
                'sendTime' => array('name' => 'send_time', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '发放日期'),
                'endTime' => array('name' => 'end_time', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '截至有效期'),
                'imgUrl' => array('name' => 'img_url', 'type' => 'array', 'format'=>'json', 'require' => true, 'desc' => '图片路径'),
            ),
        );
    }


    /**
     * 添加健康卡
     * #desc 用于添加健康卡
     * #return int code 操作码，0表示成功
     * #return int health_id  健康卡ID
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

        $data = array(
            'company_id' => $this->companyId,
            'staff_id' => $this->staffId,
//            'health_level' => $this->condition,
            'send_time' => strtotime($this->sendTime),
            'end_time' => strtotime($this->endTime),
            'img_url' => json_encode($this->imgUrl),
            'create_time' => time(),
            'last_modify' => time(),
        );
        $healthCardDomain = new Domain_Jiafubao_StaffHealthCard();
        $healthId = 0;
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $healthId = $healthCardDomain->addHealthCard($data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }
        $rs['info']['health_id'] = $healthId;

        return $rs;
    }

}
