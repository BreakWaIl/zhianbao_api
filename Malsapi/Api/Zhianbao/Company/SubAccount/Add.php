<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_SubAccount_Add extends PhalApi_Api {

    public function getRules() {
        return array (
				 'Go' => array(
                     'userId' => array('name' => 'user_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主账户ID'),
                     'groupId' => array('name' => 'group_Id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '角色ID'),
                     'name' => array('name' => 'name', 'type' => 'string', 'require' => true, 'min' => 1, 'desc' => '名称'),
                     'loginName' => array('name' => 'login_name', 'type' => 'string', 'require' => true, 'desc' => '手机'),
                     'loginPwd' => array('name' => 'login_password', 'type' => 'string', 'min' => 6, 'max' => 20 , 'require' => true, 'desc' => '密码'),
                     'code' => array ('name' => 'code', 'type' => 'string', 'require' => true, 'min' => 6, 'desc' => '验证码'),
        ),
        );
    }


    /**
     * 添加子账户
     * #desc 用于添加子账户
     * #return int user_id 商户ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $userDomain = new Domain_Zhianbao_User();
        //查询账号是否存在
        $userInfo = $userDomain->checkRegister($this->loginName);
        if(!empty($userInfo)){
            $rs['code'] = 193;
            $rs['msg'] = T('Account exists');
            return $rs;
        }
        //检测是否存在
        $domain = new Domain_Zhianbao_User();
        $info = $domain->getBaseInfo($this->userId);
        if(empty($info)){
            $rs['code'] = 112;
            $rs['msg'] = T('Company not exists');
            return $rs;
        };
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            //判断验证码
            $domainSms = new Domain_Zhianbao_Sms();
            $domainSms->checkCode($this->loginName,$this->code);
            $data = array(
                'parent_id' => $this->userId,
                'group_id' => $this->groupId,
                'login_name'=>$this->loginName,
                'login_password' => $this->loginPwd,
                'begin_time' => $info['begin_time'],
                'end_time' => $info['end_time'],
                'name' => $this->name,
                'type' => $info['type'],
                'create_time'=>time(),
                'last_modify'=>time(),
            );
            $userId = $userDomain->subaccountAdd($data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
            return $rs;
        }

        $rs['info'] = $userId;

        return $rs;
    }

}

