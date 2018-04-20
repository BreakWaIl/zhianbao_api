<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_User_Login extends PhalApi_Api {

    public function getRules() {
        return array (
				 'Go' => array(
                     'loginName' => array('name' => 'login_name', 'type' => 'string', 'min' => 11,'max'=> 11, 'require' => true, 'desc' => '用户名'),
                     'loginPwd' => array('name' => 'login_password', 'type' => 'string', 'min' => 6,'max'=> 20, 'require' => true, 'desc' => '密码'),
                     'sysType' => array('name' => 'sys_type', 'type' => 'enum', 'range' => array('jfb'), 'require' => true, 'desc' => '系统类型'),
        ),
        );
    }


    /**
     * 家服宝商户登录
     * #desc 用于家服宝商户的登录
     * @return string login_name 商户名
     * @return string sessionKey 商户凭证
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        //查询商户名是否注册
        $domain = new Domain_Jiafubao_User();
        $user = array(
            'login_name' => $this->loginName,
            'login_password' => $this->loginPwd,
            'sys_type' => $this->sysType,
        );

        try {
            $user = $domain->userLogin($user);
            if( !isset($user['code'])){
                $new_user = array(
                    'id' => $user['id'],
                    'group_id' => $user['group_id'],
                    'parent_id' => $user['parent_id'],
                    'login_name' => $user['login_name'],
                    'name' => $user['name'],
                    'mobile' => $user['mobile'],
                    'last_visit_time' => $user['last_visit_time'],
                    'last_visit_ip' => $user['last_visit_ip'],
                    'is_enable' => $user['is_enable'],
                    'minisoft' => $user['minisoft'],
                    'service' => $user['service'],
                    'session' => $user['session'],
                    'create_time' => $user['create_time'],
                    'type' => 'jfb',
                );
                $rs['user'] = $new_user;
                if($user['parent_id'] == 0){
                    $company = $domain->getBaseByUserId($user['id']);
                }else{
                    $company = $domain->getBaseByUserId($user['parent_id']);
                    $groupId = $user['group_info']['role_id'];
                    $auth = $domain->authRole($groupId);
                    $rs['auth_role'] = $auth;
                }
                if(empty($company)){
                    $rs['code'] = 226;
                    $rs['msg'] = T('Please improve the shop information first');
                    return $rs;
                }
                $rs['user']['company'] = $company;
            }else{
                $rs = $user;
            }
        }catch ( Exception $e ) {
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        return $rs;
    }

}

