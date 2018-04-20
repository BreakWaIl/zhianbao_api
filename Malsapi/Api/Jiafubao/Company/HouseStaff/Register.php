<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_HouseStaff_Register extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'mobile' => array('name' => 'mobile', 'type' => 'string', 'require' => true, 'desc' => '电话号码'),
                     'code' => array ('name' => 'code', 'type' => 'string', 'require' => true, 'min' => 6, 'desc' => '验证码'),
            ),
		);
 	}

  
  /**
     * 家政员注册
     * #desc 用于获取家政员注册
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $data = array(
            'mobile'=>$this->mobile,
            'create_time'=>time(),
            'last_modify'=>time(),
        );
        $domain = new Domain_Jiafubao_CompanyHouseStaff();

        DI ()->notorm->beginTransaction ( 'db_api' );
        try {
            //判断验证码

            $domainSms = new Domain_Zhianbao_Sms();
            $domainSms->checkCode($this->mobile,$this->code);
            $staffId =  $domain->staffRegister($data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
            return $rs;
        }

        $staffSession = new Domain_Jiafubao_StaffSession();
        $session =  $staffSession->makeSession($staffId);
        $rs['info'] = $data;
        $rs['info']['session'] = $session;
        return $rs;
    }

}

