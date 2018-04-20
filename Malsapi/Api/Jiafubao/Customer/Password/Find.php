<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Customer_Password_Find extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'mobile' => array('name' => 'mobile', 'type' => 'string', 'require' => true, 'desc' => '电话号码'),
                     'code' => array ('name' => 'code', 'type' => 'string', 'require' => true, 'min' => 6, 'desc' => '验证码'),
                     'loginPwd' => array ('name' => 'login_password', 'type' => 'string', 'require' => true, 'min' => 6, 'max' => 20, 'desc' => '客户新密码')
            ),
        );
    }
  
  /**
     * 客户密码找回
     * #desc 用于获取当前客户信息
     * #return int code 操作码，0表示成功
  */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            //判断验证码
            if(!empty($this->code)) {
                $domain = new Domain_Zhianbao_Sms();
                $info = $domain->checkCode($this->mobile,$this->code);
                if ($info){
                    $loginPwd = MD5(MD5($this->loginPwd.$info['salt']));
                    $data = array(
                        'login_pwd'=>$loginPwd,
                        'last_modify'=>time(),
                    );
                    unset($loginPwd);
                    $domainCustomer = new Domain_Jiafubao_Customer();
                    $updateResult = $domainCustomer->updateLogPwd($this->mobile,$data);
                    if(!$updateResult){
                        $rs['code'] = 108;
                        $rs['msg'] = T('Update failed');
                        return $rs;
                    }
                }
            }
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
            return $rs;
        }

        return $rs;
    }
    
}
