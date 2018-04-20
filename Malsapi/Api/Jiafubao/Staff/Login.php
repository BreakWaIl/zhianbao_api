<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Staff_Login extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'mobile' => array('name' => 'mobile', 'type' => 'string', 'require' => true, 'desc' => '电话号码'),
                'code' => array ('name' => 'code', 'type' => 'string', 'require' => true, 'min' => 6, 'desc' => '验证码'),
            ),
        );
    }


    /**
     * 家政员手机号登录
     * #desc 用于微信家政员手机号登录
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $domain = new Domain_Jiafubao_CompanyHouseStaff();

        try {
            //判断验证码
            DI ()->notorm->beginTransaction ( 'db_api' );
            $domainSms = new Domain_Zhianbao_Sms();
            $domainSms->checkShenpuCode($this->mobile,$this->code);
            $sessionRs =  $domain->codeLogin($this->mobile);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {
            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
            return $rs;
        }
        $rs['info']['session'] = $sessionRs['session'];
        return $rs;
    }

}

