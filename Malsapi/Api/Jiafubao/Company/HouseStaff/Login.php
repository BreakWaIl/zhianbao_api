<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_HouseStaff_Login extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'mobile' => array('name' => 'mobile', 'type' => 'string', 'require' => true, 'desc' => '电话号码'),
                'code' => array ('name' => 'code', 'type' => 'string', 'require' => true, 'min' => 6, 'desc' => '验证码'),
            ),
        );
    }


    /**
     * 客户登录
     * #desc 用于获取当前客户账号信息
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $filter = array();
        if(!is_null($this->mobile)){
            $filter['mobile'] = $this->mobile;
        }

        $domain = new Domain_Jiafubao_CompanyHouseStaff();

        try {
            //判断验证码
            DI ()->notorm->beginTransaction ( 'db_api' );
            $domainSms = new Domain_Zhianbao_Sms();
            if(isset($this->code)) {
                $domainSms->checkCode($this->mobile, $this->code);
                $info = $domain -> codeLogin($this->mobile);
            }
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
            return $rs;
        }

        if (empty($info)) {
            $rs['code'] = 133;
            $rs['msg'] = T('Username or password is error');
            return $rs;
        }




        $rs['info'] = $info;

        return $rs;
    }

}

