<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_HouseKeepCard_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家政员ID'),
                'name' => array('name' => 'name', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '发卡银行'),
                'cardBn' => array('name' => 'card_bn', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '卡号'),
                'endTime' => array('name' => 'end_time', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '截至过期时间'),
            ),
        );
    }


    /**
     * 添加家政卡信息
     * #desc 用于添加家政卡信息
     * #return int code 操作码，0表示成功
     * #return int card_id  家政卡ID
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
            'name' => $this->name,
            'card_bn' => $this->cardBn,
            'end_time' => strtotime($this->endTime),
            'create_time' => time(),
            'last_modify' => time(),
        );
        $houseKeepCardDomain = new Domain_Jiafubao_HouseKeepCard();
        $info = $houseKeepCardDomain->hashCard($data['company_id'],$data['staff_id']);
        if(!empty($info)){
            $rs['code'] = 187;
            $rs['msg'] = T('House keep card exist');
            return $rs;
        }
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $cardId = $houseKeepCardDomain->addCard($data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        $rs['info']['card_id'] = $cardId;

        return $rs;
    }

}
