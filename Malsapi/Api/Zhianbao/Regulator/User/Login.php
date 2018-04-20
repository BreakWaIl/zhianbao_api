<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_User_Login extends PhalApi_Api {

    public function getRules() {
        return array (
				 'Go' => array(
                     'loginName' => array('name' => 'login_name', 'type' => 'string', 'min' => 11,'max'=> 11, 'require' => true, 'desc' => '用户名'),
                     'loginPwd' => array('name' => 'login_password', 'type' => 'string', 'min' => 6,'max'=> 20, 'require' => true, 'desc' => '密码'),
                     'sysType' => array('name' => 'sys_type', 'type' => 'enum', 'range' => array('zab','jfb','zgb'), 'require' => true, 'desc' => '系统类型'),
        ),
        );
    }


    /**
     * 监管者登录
     * #desc 用于监管者的登录
     * @return string login_name 商户名
     * @return string sessionKey 商户凭证
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //查询商户名是否注册
        $domain = new Domain_Zhianbao_Regulator();
        $user = array(
               'login_name' => $this->loginName,
               'login_password' => $this->loginPwd,
        );
        try {
            $user = $domain->login($user,$this->sysType);

            $user ['create_time'] = date ( 'Y-m-d H:i:s', $user ['create_time'] );
            $user ['last_visit_time'] = ! empty ( $user ['last_visit_time'] ) ? date ( 'Y-m-d H:i:s', $user ['last_visit_time'] ) : $user ['last_visit_time'];
            $rs['user'] = $user;
           
            
        }catch ( Exception $e ) {
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        return $rs;
    }

}

