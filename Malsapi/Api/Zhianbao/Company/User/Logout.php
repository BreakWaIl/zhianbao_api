<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_User_Logout extends PhalApi_Api {

    public function getRules() {
        return array (
				 'Go' => array(
                     'userId' => array('name' => 'user_id', 'type' => 'string', 'require' => true, 'desc' => '用户ID'),
        ),
        );
    }


    /**
     * 商户退出登录
     * #desc 用于商户的退出登录
     * @return string login_name 商户名
     * @return string sessionKey 商户凭证
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $sessionDomain = new Domain_Zhianbao_UserSession();
        $sessionDomain->deleteSession($this->userId);
        return $rs;
    }

}

