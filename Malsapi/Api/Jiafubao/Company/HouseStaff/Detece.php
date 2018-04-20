<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_HouseStaff_Detece extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'cardID' => array('name' => 'card_id', 'type'=>'string', 'min' => 15, 'max' => 18, 'require'=> true,'desc'=> '身份证号码'),
            ),
        );
    }


    /**
     * 检测身份证号是否存在
     * #desc 用于检测身份证号是否存在
     * #return int code 操作码，0表示成功
     * #return int info false 不存在 true 存在
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        //检测身份证号码是否存在
        $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
        $cardInfo = $houseStaffDomain->hashCardID($this->cardID);
        if(!empty($cardInfo)){
            $cardInfo = true;
        }

        $rs['info'] = $cardInfo;

        return $rs;
    }

}
