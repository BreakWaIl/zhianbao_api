<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class  Api_Jiafubao_Company_HouseKeepCard_Update extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家政员ID'),
                     'cardId' => array('name' => 'card_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家政卡ID'),
                     'name' => array('name' => 'name', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '发卡银行'),
                     'cardBn' => array('name' => 'card_bn', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '卡号'),
                     'endTime' => array('name' => 'end_time', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '截至过期时间'),
            ),
		);
 	}
	
  
  /**
     * 更新家政卡信息
     * #desc 用于更新家政卡信息
     * #return int code 操作码，0表示成功
     * #return int status 状态 0 成功, 1 失败
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

        $data = array(
            'card_id' => $this->cardId,
            'name' => $this->name,
            'card_bn' => $this->cardBn,
            'is_check' => 'n',
            'end_time' => strtotime($this->endTime),
            'last_modify' => time(),
        );
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $res = $houseKeepCardDomain->updateCard($data,$cardInfo);
            if( $res){
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
