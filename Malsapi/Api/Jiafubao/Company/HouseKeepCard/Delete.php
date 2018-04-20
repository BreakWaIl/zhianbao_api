<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_HouseKeepCard_Delete extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'cardId' => array('name' => 'card_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家政卡ID'),
            ),
		);
 	}
	
  
  /**
     * 删除家政卡
     * #desc 用于删除当前家政卡
     * #return int code 操作码，0表示成功
     * #return int status 状态 0 成功, 1 失败
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断家政卡是否存在
        $houseKeepCardDomain = new Domain_Jiafubao_HouseKeepCard();
        $cardInfo = $houseKeepCardDomain->getBaseInfo($this->cardId);
        if( !$cardInfo) {
            $rs['code'] = 151;
            $rs['msg'] = T('House keep card not exist');
            return $rs;
        }

//        $res = $houseKeepCardDomain->deleteCard($this->cardId);
        $res = 0;
        if( $res){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info'] = $status;

        return $rs;
    }
	
}
