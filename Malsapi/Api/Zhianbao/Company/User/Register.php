<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_User_Register extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id', 'type' => 'int', 'require' => false, 'desc' => '监管者ID'),
                     'loginName' => array('name' => 'login_name', 'type' => 'string', 'min' => 11, 'max' => 11 , 'require' => true, 'desc' => '用户名'),
                     'name' => array('name' => 'name', 'type' => 'string' , 'require' => true, 'desc' => '公司名称'),
                     'loginPwd' => array('name' => 'login_password', 'type' => 'string', 'min' => 6, 'max' => 20 , 'require' => true, 'desc' => '密码'),
                     'mobile' => array('name' => 'mobile',  'type' => 'string', 'min' => 11,'max'=> 11,   'require' => true, 'desc' => '手机号码'),
                     'companyType' => array('name' => 'company_type', 'type'=>'enum','range' => array('hospital','community','hotel','station','colliery','education','building' ), 'default' => 'y', 'require'=> true,'desc'=> '行业:hospital 医院,community 社区,hotel 酒店,station 加油站,colliery 煤矿,education 教育,building 建筑'),
                     'sysType' => array('name' => 'sys_type', 'type' => 'enum', 'range' => array('zab','jfb','zgb'), 'require' => true, 'desc' => '系统类型'),
                     'serviceTime' => array('name' => 'service_time', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '服务年限'),
                     'province' => array('name' => 'province', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '省份'),
                     'city' => array('name' => 'city', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '城市'),
                     'district' => array('name' => 'district', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '区县'),
                     'address' => array('name' => 'address', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '详细地址'),
                     ),
		);
 	}

  
  /**
     * 商户注册
     * #desc 用于商户的注册
     * #return int user_id 商户ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //查询商户名是否注册
        $domain = new Domain_Zhianbao_User();
        $info = $domain->getBaseInfoByName($this->loginName);

        if (! empty($info)) {
            DI()->logger->debug('User is already exists', $this->loginName);

            $rs['code'] = 156;
            $rs['msg'] = T('User is already exists');
            return $rs;
        }

        //验证激活码
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $data = array(
                'login_name' => $this->loginName,
                'login_password' => $this->loginPwd,
                'mobile' => $this->mobile,
                'name' => $this->name,
                'companyType' => $this->companyType,
                'sysType' => $this->sysType,
                'serviceTime' => $this->serviceTime,
                'country' => '1',
                'province' => $this->province,
                'city' => $this->city,
                'district' => $this->district,
                'address' => $this->address,
            );
            if(isset($this->regulatorId)){
                $data['regulator_id'] = $this->regulatorId;
            }else{
                $data['regulator_id'] = 0;
            }
            $userId = $domain->userRegister($data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
            return $rs;
        }

        $rs['user_id'] = $userId;

        return $rs;
    }

}

