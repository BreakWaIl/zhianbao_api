<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_SubAccount_Add extends PhalApi_Api {

    public function getRules() {
        return array (
				 'Go' => array(
                     'userId' => array('name' => 'user_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主账户ID'),
                     'groupId' => array('name' => 'group_Id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '角色ID'),
                     'loginName' => array('name' => 'login_name', 'type' => 'string', 'require' => true, 'desc' => '手机'),
                     'loginPwd' => array('name' => 'login_password', 'type' => 'string', 'min' => 6, 'max' => 20 , 'require' => true, 'desc' => '密码'),
                     'code' => array ('name' => 'code', 'type' => 'string', 'require' => true, 'min' => 6, 'desc' => '验证码'),
        ),
        );
    }


    /**
     * 添加家服宝子账户
     * #desc 用于家服宝添加子账户
     * #return int user_id 商户ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $userDomain = new Domain_Jiafubao_User();
//        //查询账号是否存在
//        $userInfo = $userDomain->checkRegister($this->loginName);
//        if(!empty($userInfo)){
//            $rs['code'] = 193;
//            $rs['msg'] = T('Account exists');
//            return $rs;
//        }

        try {
            $data = array(
                'user_id' => $this->userId,
                'group_id' => $this->groupId,
                'login_name'=>$this->loginName,
                'login_password' => $this->loginPwd,
                'code' => $this->code,
            );
            $info = $userDomain->subAccountAdd($data);
            if( !$info){
                $rs['code'] = 226;
                $rs['msg'] = T('Please improve the shop information first');
                return $rs;
            }
            if($info['code'] == 0){
                $rs['info'] = $info['user_id'];
            }else{
                $rs = $info;
            }

        } catch ( Exception $e ) {

            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
            return $rs;
        }
        return $rs;
    }

}

