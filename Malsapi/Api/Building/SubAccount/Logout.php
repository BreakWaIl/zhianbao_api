<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_SubAccount_Logout extends PhalApi_Api {

    public function getRules() {
        return array (
				 'Go' => array(
                     'userId' => array('name' => 'user_id', 'type' => 'string', 'require' => true, 'desc' => '用户ID'),
                     'operateId' => array('name' => 'operate_id', 'type' => 'string', 'require' => true, 'desc' => '操作者ID'),
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
        if($this->userId == $this->operateId){
            $sessionDomain->deleteSession($this->userId);
        }else{
            $sessionDomain->deleteSession($this->operateId);
        }

        return $rs;
    }

}

