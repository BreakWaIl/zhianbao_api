<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_User_Logout extends PhalApi_Api {

    public function getRules() {
        return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id', 'type' => 'string', 'require' => true, 'desc' => '监管ID'),
        ),
        );
    }


    /**
     * 监管者退出登录
     * #desc 用于监管者的退出登录
     * @return string login_name 商户名
     * @return string sessionKey 商户凭证
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $sessionDomain = new Domain_Zhianbao_RegulatorSession();
        $sessionDomain->deleteSession($this->regulatorId);
        return $rs;
    }

}

