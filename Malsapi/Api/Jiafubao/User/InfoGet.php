<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_User_InfoGet extends PhalApi_Api {

    public function getRules() {
        return array (
				 'Go' => array(
                     'userId' => array('name' => 'user_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                    ),
        );
    }


    /**
     * 家服宝商户资料获取
     * #desc 用于家服宝商户资料获取
     * #return int code 操作码，0表示成功
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //获取用户
        $domain = new Domain_Jiafubao_User();
        $info = $domain->getBaseByUserId($this->userId);
        if( empty($info)){
            $rs['code'] = 112;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }
        $rs['info'] = $info;

        return $rs;
    }

}

