<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_SubAccount_Add extends PhalApi_Api {

    public function getRules() {
        return array (
				 'Go' => array(
                     'userId' => array('name' => 'user_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主账户ID'),
                     'operateId' => array('name' => 'operate_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '父级ID'),
                     'groupId' => array('name' => 'group_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '角色ID'),
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
        //检测主账号是否存在
        $info = $userDomain->getBaseInfo($this->userId);
        if(empty($info)){
            $rs['code'] = 112;
            $rs['msg'] = T('User not found');
            return $rs;
        }else{
            //检测主账号服务时间
            if($info['end_time'] < time()){
                $rs['code'] = 199;
                $rs['msg'] = T('Account has expired');
                return $rs;
            }
        }
        //查看角色是否存在
        $userGroupDomain = new Domain_Zhianbao_UserGroup();
        $groupInfo = $userGroupDomain->getBaseInfo($this->groupId);
        if(! $groupInfo){
            $rs['code'] = 150;
            $rs['msg'] = T('Role not exist');
            return $rs;
        }

        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            //判断验证码
            $domainSms = new Domain_Zhianbao_Sms();
            $domainSms->checkCode($this->loginName,$this->code);
            $data = array(
                'group_id' => $this->groupId,
                'parent_id' => $this->userId,
                'login_name'=>$this->loginName,
                'login_password' => $this->loginPwd,
                'name' => $this->name,
                'type' => $info['type'],
                'begin_time' => $info['begin_time'],
                'end_time'=> $info['end_time'],
                'create_time'=>time(),
                'last_modify'=>time(),
            );
            $subDomain = new Domain_Building_SubAccount();
            $userId = $subDomain->userRegisterSub($data);
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

