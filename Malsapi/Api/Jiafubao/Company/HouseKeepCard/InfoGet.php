<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_HouseKeepCard_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家政员ID'),
                     'cardId' => array('name' => 'card_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家政卡ID'),
            ),
        );
    }
  
  /**
     * 获取家政卡详情
     * #desc 用于获取家政卡详情
     * #return int code 操作码，0表示成功
   * #return int company_id 公司ID
   * #return string name 发卡银行
   * #return string card_bn 发号
   * #return string end_time 过期时间
   * #return int create_time 创建时间
   * #return int last_modify  最后更新时间
  */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断家政人员是否存在
        $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
        $staffInfo = $houseStaffDomain->getBaseInfo($this->staffId);
        if( !$staffInfo) {
            DI()->logger->debug('Staff not exists', $this->staffId);

            $rs['code'] = 126;
            $rs['msg'] = T('Staff not exists');
            return $rs;
        }

        //判断家政卡是否存在
        $houseKeepCardDomain = new Domain_Jiafubao_HouseKeepCard();
        $cardInfo = $houseKeepCardDomain->getBaseInfo($this->cardId);
        if( !$cardInfo) {
            $rs['code'] = 151;
            $rs['msg'] = T('House keep card not exist');
            return $rs;
        }

        $rs['info'] = $cardInfo;

        return $rs;
    }
    
}
