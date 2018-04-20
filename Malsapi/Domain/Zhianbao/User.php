<?php
class Domain_Zhianbao_User {
    var $model;
    var $loginTime = 86400;
    var $roleList;
    var $syoleList;
    var $buildRoleList;

    public function __construct() {
        $this->model = new Model_Zhianbao_User ();
        $this->roleList = array(
        1=>array('name'=>'超级管理员','disablePermission'=>array(),'disableView'=>array('')),
        2=>array('name'=>'普通管理员','disablePermission'=>array(),'disableView'=>array(''))
        );
        $this->syoleList = array(
            1=>array('name'=>'超级管理员','disablePermission'=>array(),'disableView'=>array('')),
            2=>array('name'=>'普通管理员','disablePermission'=>array(),'disableView'=>array('')),
            3=>array('name'=>'农事操作员','disablePermission'=>array(),'disableView'=>array(''))
        );
        $this->buildRoleList = array(
            1=>array('name'=>'项目经理','disablePermission'=>array(),'disableView'=>array('')),
            2=>array('name'=>'生产经理','disablePermission'=>array(),'disableView'=>array('')),
            3=>array('name'=>'技术总工','disablePermission'=>array(),'disableView'=>array('')),
            4=>array('name'=>'技术员','disablePermission'=>array(),'disableView'=>array('')),
            5=>array('name'=>'施工员','disablePermission'=>array(),'disableView'=>array('')),
            6=>array('name'=>'资料员','disablePermission'=>array(),'disableView'=>array('')),
            7=>array('name'=>'质检员','disablePermission'=>array(),'disableView'=>array('')),
            8=>array('name'=>'预算员','disablePermission'=>array(),'disableView'=>array('')),
            9=>array('name'=>'安全员','disablePermission'=>array(),'disableView'=>array('')),
            10=>array('name'=>'材料员','disablePermission'=>array(),'disableView'=>array('')),
            11=>array('name'=>'机械员','disablePermission'=>array(),'disableView'=>array('')),
            12=>array('name'=>'劳务员','disablePermission'=>array(),'disableView'=>array('')),
            13=>array('name'=>'测量员','disablePermission'=>array(),'disableView'=>array('')),
            14=>array('name'=>'仓库管理员','disablePermission'=>array(),'disableView'=>array(''))
        );

    }

    public function getBaseInfo($userId, $cols = '*') {
        $rs = $this->model->get($userId,$cols);
        return $rs;
    }

    public function getSubUserInfo($parnetUserId,$subUserId, $cols = '*') {
        $rs = array ();

        $rs = $this->model->get($subUserId,$cols);
        if (! $rs)
        return false;
        if($rs['parent_id'] != $parnetUserId){
            return false;
        }
        return $rs;
    }

    /**
     *
     * 手机号是否注册
     *
     * @param string $mobile
     */
    public function checkRegister($loginName) {
        $rs = array ();

        $filter = array('login_name'=>$loginName);
        $rs = $this->model->getByWhere($filter);

        if (! $rs)
        return false;

        return $rs;
    }





    public function getBaseInfoByName($login_name){
        $rs = $this->model->getByWhere(array('login_name'=>$login_name));
        return $rs;
    }
    public function userRegister($data){

        $loginName = $data['login_name'];
        $loginPwd = $data['login_password'];
        $mobile = $data['mobile'];
        $beginTime = time();
        $endTime = $data['serviceTime'] * 86400 * 365 + time();
//        if(isset($data['regulator_id'])){
        $regulatorId = $data['regulator_id'];
//        }else{
//            $regulatorId = false;
//        }
        $salt = PhalApi_Tool::createRandStr ( 8 );
        $loginPwd = $this->user_hash ( $loginPwd, $salt );
        $userData = array(
          //  'regulator_id' => $data['regulator_id'],
			'login_name' => $loginName,
			'login_password' => $loginPwd,
			'mobile' => $mobile,
			'salt' => $salt,
            'name' => $data['name'],
            'begin_time' => $beginTime,
            'end_time' => $endTime,
            'type' => $data['sysType'],
			'create_time' => time(),
            'last_modify' => time(),
        );

        $userId =  $this->model->insert($userData);
        if(! $userId){
            throw new LogicException ( T ( 'Create failed' ), 144 );
        }
        //注册好自动创建公司
        $companyModel = new Model_Zhianbao_Company();
        $companyData = array(
            'user_id' => $userId,
            'name' => $data['name'],
            'mobile' => $mobile,
            'type' => $data['companyType'],
            'create_time' => time(),
            'last_modify' => time(),
        );
        $companyId = $companyModel->insert($companyData);
        if(! $companyId){
            throw new LogicException ( T ( 'Create failed' ), 144 );
        }
        if($data['sysType'] == 'jfb') {
            //如果是家服保-创建家服保公司信息
            $jfbCompanyModel = new Model_Jiafubao_Company();
            $jfbData = array(
                'company_id' => $companyId,
                'create_time' => time(),
                'last_modify' => time(),
                'country' => '1',
                'province' => $data['province'],
                'city' => $data['city'],
                'district' => $data['district'],
                'address' => $data['address'],
            );
            $jfbCompanyId = $jfbCompanyModel->insert($jfbData);
            if (!$jfbCompanyId) {
                throw new LogicException (T('Create failed'), 144);
            }
        }
        //添加监管者关系
        if($regulatorId > 0){
            $regulatorToCompanyModel = new Model_Zhianbao_RegulatorToCustomer();
            $toCompanyData = array(
                'regulator_id' => $regulatorId,
                'company_id' => $companyId,
                'create_time' => time(),
                'last_modify' => time(),
            );
            $companyId = $regulatorToCompanyModel->insert($toCompanyData);
            if(! $companyId){
                throw new LogicException ( T ( 'Create failed' ), 144 );
            }
        }
        return $userId;
    }





    /**
     *
     * 只允许一个账号同时一个人登陆
     * @param array $user
     */
    public function login($user,$sysType) {
        $rs = $this->model->getByWhere ( array('login_name' => $user ['login_name']), 'id,salt,login_password,type,end_time' );
        if (empty ( $rs )) {
            throw new LogicException ( T ( 'User not exists' ), 104 );
        }
        $checkRs = $this->checkLoginError($rs['id']);
        if(! $checkRs){
            throw new LogicException ( T ( 'Password error more than five times, please try again in five minutes' ), 134 );
        }
        $checkLoginPwd = $this->user_hash ( $user ['login_password'], $rs ['salt'] );
        if ($checkLoginPwd != $rs ['login_password']) {
            $loginRs = $this->loginError($rs['id'],$user ['login_name']);
            if(! $loginRs){
                throw new LogicException ( T ( 'Password error more than five times, please try again in five minutes' ), 134 );
            }
            throw new LogicException ( T ( 'Username or password error' ), 133 );
        }
        $user = $this->getLoginUserInfo($rs['id']);

        //判断是否禁用
        if($user['is_enable'] == 'n'){
            throw new LogicException ( T ( 'User is already ban' ), 132 );
        }
        //判断是否有系统登录权限
        if($rs['type'] != $sysType){
            throw new LogicException ( T ( 'User can not  login the system' ), 198 );
        }
        //判断是否过期
        if($rs['end_time'] < time()){
            throw new LogicException ( T ( 'Account expired' ), 199 );
        }
        $domainUserSession = new Domain_Zhianbao_UserSession();


        //更新登录IP
        $time = time();
        $ip = PhalApi_Tool::getClientIp();
        $data = array(
            'last_visit_time' => $time,
            'last_visit_ip' => $ip,
            'type' => $sysType,
        );
        $this->updateLogin($user['id'],$data);

        $user = array_merge($user,$data);
        //登录成功后插入登录成功日志

        $logModel = new Model_Zhianbao_UserLoginLog();
        $log_data = array(
            'user_id' => $user['id'],
            'login_name' => $user['login_name'],
            'name' => $user['name'],
            'last_visit_time' => $user['last_visit_time'],
            'last_visit_ip' => $user['last_visit_ip']
        );
        $log = $logModel->insert($log_data);
        if($log){
            //更新登录统计数据
            $this->userLoginData($log_data);
        }

        //登陆成功后，种入SESSION COOKIE
        $session =  $domainUserSession->makeSession($user ['id']);
        $user['session'] = $session;


        //登录成功后清除登录失败日志
        $errorModel = new Model_Zhianbao_UserLoginError();
        $filter = array('user_id' => $user['id']);
        $errorModel->deleteByWhere($filter);


        //获取权限
        if($user['group_id'] != 0 ) {
            //判断是系统类型
            if($sysType == 'zgb'){
                $firstAuth = array();
                $groupModel = new Model_Building_UserGroup();
                $userAuthModel = new Model_Building_UserAuthRole();
                $groupInfo = $groupModel->get($user['group_id']);
                if($groupInfo) {
                    //$authArray = explode(',', $groupInfo['role']);
                    $authArray = json_decode($groupInfo['role'],true);
                    $authFilter = array('id' => $authArray,'parent_id' => 0);
                    $authList = $userAuthModel->getAll('*',$authFilter,1,-1,'o');
                    foreach ($authList as $key => $value){
                        //       $firstAuth['info'] = $value;
                        $childAuthFilter = array('id' => $authArray,'parent_id' => $value['id']);
                        $childAuthList = $userAuthModel->getAll('*',$childAuthFilter);
                        foreach ($childAuthList as $k => $v){
                            $actionAuthFilter = array('id' => $authArray,'parent_id' => $v['id']);
                            $actionList = $userAuthModel->getAll('*',$actionAuthFilter);
                            $v['child'] = $actionList;
                            $value['child'][] = $v;
                        }
                        $firstAuth[] = $value;
                    }
                }
                $user['auth_role'] = $firstAuth;
            }else{
                $firstAuth = array();
                $groupModel = new Model_Zhianbao_UserGroup();
                $userAuthModel = new Model_Jiafubao_UserAuthRole();
                $groupInfo = $groupModel->get($user['group_id']);
                if($groupInfo) {
                    //$authArray = explode(',', $groupInfo['role']);
                    $authArray = json_decode($groupInfo['role'],true);
                    $authFilter = array('id' => $authArray,'parent_id' => 0);
                    $authList = $userAuthModel->getAll('*',$authFilter,1,-1,'o');
                    foreach ($authList as $key => $value){
                        //       $firstAuth['info'] = $value;
                        $childAuthFilter = array('id' => $authArray,'parent_id' => $value['id']);
                        $childAuthList = $userAuthModel->getAll('*',$childAuthFilter);
                        foreach ($childAuthList as $k => $v){
                            $actionAuthFilter = array('id' => $authArray,'parent_id' => $v['id']);
                            $actionList = $userAuthModel->getAll('*',$actionAuthFilter);
                            $v['child'] = $actionList;
                            $value['child'][] = $v;
                        }
                        $firstAuth[] = $value;
                    }
                }
                $user['auth_role'] = $firstAuth;
            }
        }
        return $user;
    }

    public function logout($userId){
        $domainUserSession = new Domain_Zhianbao_UserSession();
        $domainUserSession->deleteSession($userId);
       // DI ()->cookie->delete('sp_asid');
    }

    public function checkLogin($session){
        $domainUserSession = new Domain_Zhianbao_UserSession();
        $sessionData = $domainUserSession->checkSession($session);
        if($sessionData){
            return $sessionData;
        }else{
            DI ()->cookie->delete('zab_asid');
            return false;
        }
    }

    public function user_hash($passwordinput, $salt) {
        $passwordinput = "{$passwordinput}-{$salt}-" . DI ()->config->get ( 'sys.user.pwd_salt' );

        return sha1 ( $passwordinput );
    }

    public function updateLogin($userId,$data){
        $rs = $this->model->update($userId,$data);
        return $rs;
    }
    public function getLoginUserInfo($userId){
        $rs = $this->model->get($userId);
        return $rs;
    }

    public function banUser($userId){
        $data = array('is_enable' => 'n');
        $rs = $this->model->update($userId,$data);
        return $rs;
    }

    public function unBanUser($userId){
        $data = array('is_enable' => 'y');
        $rs = $this->model->update($userId,$data);
        return $rs;
    }
    public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = '') {
        $rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        return $rs;
    }  
    //获取数量
    public function getCount($filter) {
        $count = $this->model->getCount($filter);
        return $count;
    }

    //登录失败，记录失败次数
    public function loginError($userId,$loginName){
        $errorModel = new Model_Zhianbao_UserLoginError();
        $filter = array(
            'user_id' => $userId,
            'last_modify > ?' => time() - 300,
        );
        $rs = $errorModel->getByWhere($filter);
        if($rs){
            //存在登录错误记录
            if($rs['error_times'] >= 5){
                return false;
            }
            $updateData = array(
                'last_modify' => time(),
                'error_times' => new NotORM_Literal("error_times + 1"),
            );
            $errorModel->update($rs['id'],$updateData);
            return true;
        }else{
            //不存在登录错误记录
            $insertData = array(
                'user_id' => $userId,
                'login_name' => $loginName,
                'error_times' => 1,
                'create_time' => time(),
                'last_modify' => time()
            );
            $errorModel->insert($insertData);
            return true;
        }
    }
    //检测登录失败次数
    public function checkLoginError($userId){
        $errorModel = new Model_Zhianbao_UserLoginError();
        $filter = array(
            'user_id' => $userId,
            'last_modify > ?' => time() - 300,
        );
        $rs = $errorModel->getByWhere($filter);
        if($rs){
            //存在登录错误记录
            if($rs['error_times'] >= 5){
                return false;
            }
            return true;
        }else{
            return true;
        }
    }
    //商户找回密码
    public function findPwd($userId,$newPwd){
        $salt = PhalApi_Tool::createRandStr ( 8 );
        $loginPwd = $this->user_hash ( $newPwd, $salt );
        $data = array('login_password' => $loginPwd,'salt' => $salt);
        $rs = $this->model->update($userId,$data);
        return $rs;
    }
    //商户修改密码
    public function changePwd($user,$oldPwd,$newPwd){
        $userId = $user['id'];
        $salt = $user['salt'];
        $oldSaltPwd = $this->user_hash ( $oldPwd, $salt );
        if($oldSaltPwd != $user['login_password']){
            //旧密码错误
            throw new LogicException ( T ( 'Create failed' ), 144 );
        }
        //更新新的密码
        $salt = PhalApi_Tool::createRandStr ( 8 );
        $loginPwd = $this->user_hash ( $newPwd, $salt );
        $data = array('login_password' => $loginPwd,'salt' => $salt);
        $rs = $this->model->update($userId,$data);
        return $rs;
    }
    //更新商户单位信息
    public function updateUser($data){
        $rs = true;
        $userId = $data['user_id'];
        unset($data['user_id']);

        $userInfo = $this->model->get($userId);
        if($userInfo['name'] != $data['name']){
            $update = array(
                'name' => $data['name']
            );
            $rs = $this->model->update($userId,$update);
            if(!$rs){
                return false;
            }
        }

        return $rs;
    }
    //获取当前用户登录日志
    public function getAllUserLog( $filter, $page = 1, $page_size = 20, $orderby = ''){
        $logModel = new Model_Zhianbao_UserLoginLog();
        $rs = $logModel->getAll ( '*', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key=>$value){
            $rs[$key]['last_visit_time'] = date('Y-m-d H:i:s',$value['last_visit_time']);
        }
        return $rs;
    }
    public function getUserLoginCount($filter) {
        $logModel = new Model_Zhianbao_UserLoginLog();
        return $logModel->getCount ( $filter );
    }
    //获取当前用户登录统计
    public function getUserLoginData( $filter, $page = 1, $page_size = 20, $orderby = ''){
        $logDataModel = new Model_Zhianbao_UserLoginData();
        $rs = $logDataModel->getAll ( '*', $filter, $page, $page_size, $orderby );
        $key_arrays = array();
        foreach ($rs as $key=>$value){
            $rs[$key]['last_visit_time'] = date('Y-m-d H:i:s',$value['last_visit_time']);
            $key_arrays[]=$value['times'];
        }
        array_multisort($key_arrays,SORT_DESC,SORT_NUMERIC,$rs);
        return $rs;
    }
    public function getUserLoginDataCount($filter) {
        $logDataModel = new Model_Zhianbao_UserLoginData();
        return $logDataModel->getCount ( $filter );
    }
    private function userLoginData($data){
        $logDataModel = new Model_Zhianbao_UserLoginData();
        $filter = array(
            'user_id' => $data['user_id'],
            'login_name' => $data['login_name']
        );
        $info = $logDataModel->getByWhere($filter,'*');
        if(empty($info)){
            $log_data = array(
                'user_id' => $data['user_id'],
                'login_name' => $data['login_name'],
                'times' => '1',
                'last_visit_time' => $data['last_visit_time']
            );
            $logDataModel->insert($log_data);
        }else{
            $log_data = array(
                'times' => ++$info['times']
            );
            $logDataModel->update($info['id'],$log_data);
        }
    }
    //更新用户信息和公司信息
    public function updateCompany($userId,$data){
        $companyModel = new Model_Zhianbao_Company();
        $userData = array(
            'name' => $data['name'],
            'last_modify' => time()
        );
        if(isset($data['logoImg'])){
            $userData['logo_img'] = $data['logoImg'];
        }
        $rs = $this->model->update($userId,$userData);
        if(! $rs){
            throw new LogicException ( T ( 'Update failed' ), 104 );
        }
        $filter = array('user_id' => $userId);
        $companyInfo = $companyModel->getByWhere($filter);
        if(! $companyInfo){
            throw new LogicException ( T ( 'Update failed' ), 104 );
        }
//        $companyData = array(
//            'name' => $data['name'],
//        );
//        if(isset($data['logoImg'])){
//            $companyData['logo_img'] = $data['logoImg'];
//        }
//        $rs = $companyModel->update($companyInfo['id'],$companyData);
//        if(! $rs){
//            throw new LogicException ( T ( 'Update failed' ), 104 );
//        }
        //处理监管关系
        $regulatorToCompanyModel = new Model_Zhianbao_RegulatorToCustomer();
        if(isset($data['regulator_id'])){
            //需要处理监管关系
            $filter = array(
                'company_id' => $companyInfo['id'],
                'regulator_id' => $data['regulator_id']
            );
            $toCompanyInfo = $regulatorToCompanyModel->getByWhere($filter);
            if(! $toCompanyInfo){
                //如果不存在说明监管部门更换-删除之前的监管关系-添加新的监管
                $delFilter = array('company_id' => $companyInfo['id']);
                $regulatorToCompanyModel->deleteByWhere($delFilter);
                //添加新的监管
                $addData = array(
                    'company_id' => $companyInfo['id'],
                    'regulator_id' => $data['regulator_id'],
                    'create_time' => time(),
                    'last_modify' => time(),
                );
                $regulatorToCompanyModel->insert($addData);
            }
        }
        return true;
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
        //插入关系
        $role_data = array(
            'user_id' => $userId,
            'login_name' => $loginName,
            'parent_id' => $data['parent_id'],
            'role_id' => $data['role_id'],
            'create_time' => time(),
        );
        $userRoleModel = new Model_Building_UserRole();
        $res = $userRoleModel->insert($role_data);
        if(! $res){
            throw new LogicException ( T ( 'Create failed' ), 144 );
        }

        return $userId;
    }

    //添加子账号
    public function subaccountAdd($data){
        $loginName = $data['login_name'];
        $loginPwd = $data['login_password'];
        $mobile = $data['login_name'];
        $salt = PhalApi_Tool::createRandStr ( 8 );
        $loginPwd = $this->user_hash ( $loginPwd, $salt );
        $userData = array(
            'login_name' => $loginName,
            'login_password' => $loginPwd,
            'parent_id' => $data['parent_id'],
            'group_id' => $data['group_id'],
            'mobile' => $mobile,
            'salt' => $salt,
            'name' => $data['name'],
            'type' => $data['type'],
            'begin_time' => $data['begin_time'],
            'end_time' => $data['end_time'],
            'create_time' => time(),
            'last_modify' => time(),
        );
        $userId =  $this->model->insert($userData);
        if(! $userId){
            throw new LogicException ( T ( 'Create failed' ), 144 );
        }
        return $userId;
    }
    //获取子账号列表
    //获取子账号列表
    public function getSubAccountList($filter, $page = 1, $page_size = 20, $orderby = '') {
        $groupModel = new Model_Zhianbao_UserGroup();
        $rs = $this->model->getAll('*',$filter,$page,$page_size,$orderby);
        foreach ($rs as $key => $value){
            $groupInfo = $groupModel->get($value['group_id']);
            $rs[$key]['group_info'] = $groupInfo;
        }
        return $rs ;
    }
    //获取子账号列表
    public function getSubUserList($filter, $page = 1, $page_size = 20, $orderby = '') {
        $userRoleModel = new Model_Building_UserRole();
        $rs = $userRoleModel->getAll('*',$filter,$page,$page_size,$orderby);
        $userIds = array();
        foreach ($rs as $key => $value){
            $userIds[] = $value['user_id'];
            $role = $this->buildRoleList[$value['role_id']];
            $value['role_name'] = $role['name'];
            $value['mobile'] = $value['login_name'];
            $rs[$key] = $value;
        }

        $filter = array('id'=>$userIds);
        $users = $this->model->getAllPairs('id', '',$filter,'name,last_visit_time');
        foreach ($rs as $key => $value){
            $user = $users[$value['user_id']];
            $value['name'] = $user['name'];
            $rs[$key] = $value;
        }

        return $rs ;
    }
    //获取数量
    public function getSubUserCount($filter) {
        $count = $this->model->getCount($filter);
        return $count;
    }
    //获取系统使用情况
    public function getSysUseDetails($data){
        $loginLogModel = new Model_Zhianbao_UserLoginLog();
        $beginTime = $data['begin_time'];
        $endTime = $beginTime + 86400;
        $searchEndTime = $data['end_time'];
        $result = array();
        while ($endTime <= $searchEndTime){
            $filter = array(
                'last_visit_time > ?' => $beginTime,
                'last_visit_time < ?' => $endTime,
                'type' => $data['sys_type']
            );
            $list = $loginLogModel->getAll('*',$filter);
            foreach ($list as $key => $value){
                $list[$key]['last_visit_time'] = date('Y-m-d H:i:s',$value['last_visit_time']);
            }
            $count = $loginLogModel->getCount($filter);
            $result[date('Y-m-d',$beginTime)]['list'] = $list;
            $result[date('Y-m-d',$beginTime)]['count'] = $count;
            $beginTime = $endTime;
            $endTime = $endTime + 86400;
        }
        return $result;
    }
}
