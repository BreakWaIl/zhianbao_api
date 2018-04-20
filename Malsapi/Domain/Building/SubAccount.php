<?php
class Domain_Building_SubAccount {
    var $model;
    var $loginTime = 86400;

    public function __construct() {
        $this->model = new Model_Zhianbao_User ();

    }

    public function getBaseInfo($subId, $cols = '*'){
        $rs = array ();
        $id = intval ( $subId );
        if ($id <= 0) {
            return $rs;
        }

        $rs = $this->model->get ( $id);

        if( !$rs){
            return false;
        }

        return $rs;
    }

    //注册子账户
    public function userRegisterSub($data){
        $loginName = $data['login_name'];
        $loginPwd = $data['login_password'];
        $mobile = $data['login_name'];
        $salt = PhalApi_Tool::createRandStr ( 8 );
        $loginPwd = $this->user_hash ( $loginPwd, $salt );
        $userData = array(
            'login_name' => $loginName,
            'login_password' => $loginPwd,
            'group_id' => $data['group_id'],
            'parent_id' => $data['parent_id'],
            'mobile' => $mobile,
            'salt' => $salt,
            'name' => $data['name'],
            'type' => $data['type'],
            'begin_time' => $data['begin_time'],
            'end_time'=> $data['end_time'],
            'create_time' => time(),
            'last_modify' => time(),
        );
        $userId =  $this->model->insert($userData);
        if(! $userId){
            throw new LogicException ( T ( 'Create failed' ), 144 );
        }

        return $userId;
    }

    public function user_hash($passwordinput, $salt) {
        $passwordinput = "{$passwordinput}-{$salt}-" . DI ()->config->get ( 'sys.user.pwd_salt' );

        return sha1 ( $passwordinput );
    }

    //获取子账号列表
    public function getSubUserList($filter, $page = 1, $page_size = 20, $orderby = '') {
        $groupModel = new Model_Building_UserGroup();
        $rs = $this->model->getAll('*',$filter,$page,$page_size,$orderby);
        foreach ($rs as $key => $value){
            $groupInfo = $groupModel->get($value['group_id']);
            $rs[$key]['group_info'] = $groupInfo;
        }
        return $rs ;
    }
    //获取数量
    public function getSubUserCount($filter) {
        $count = $this->model->getCount($filter);
        return $count;
    }



}
