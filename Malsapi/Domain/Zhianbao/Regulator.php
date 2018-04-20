<?php
class Domain_Zhianbao_Regulator {
	var $model;

	public function __construct() {
		$this->model = new Model_Zhianbao_Regulator ();
	}
	//获取分类列表
	public function getCatList($filter){
		$rs = $this->model->getAll ( '*', $filter);
		return $rs;
	}
    public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = '') {
        $rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        return $rs;
    }
    //获取数量
	public function getCount($filter) {
		return $this->model->getCount ( $filter );
	}
	//获取分类详情
	public function getBaseInfo($id, $cols = '*') {
		$rs = $this->model->get($id,$cols);
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
    //商户找回密码
    public function findPwd($userId,$newPwd){
        $salt = PhalApi_Tool::createRandStr ( 8 );
        $loginPwd = $this->user_hash ( $newPwd, $salt );
        $data = array('login_password' => $loginPwd,'salt' => $salt);
        $rs = $this->model->update($userId,$data);
        return $rs;
    }
	//获取监管的公司ID
    public function getCompanyIds($regulatorId){
        $regulatorToCustomerModel = new Model_Zhianbao_RegulatorToCustomer();
        $filter = array('regulator_id' => $regulatorId);
        $customerList = $regulatorToCustomerModel->getAll('*',$filter);
        $customerIds = array();
        foreach ($customerList as $key => $value){
            $customerIds[]  = $value['company_id'];
        }
        return $customerIds;
    }

    //注册监管者
    public function regulatorRegister($data){

        $loginName = $data['login_name'];
        $loginPwd = $data['login_password'];
        $mobile = $data['mobile'];

        $salt = PhalApi_Tool::createRandStr ( 8 );
        $loginPwd = $this->user_hash ( $loginPwd, $salt );
        $userData = array(
            'login_name' => $loginName,
            'login_password' => $loginPwd,
            'mobile' => $mobile,
            'salt' => $salt,
            'name' => $data['name'],
            'type' => $data['type'],
            'create_time' => time(),
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
    //商户修改密码
    public function changePwd($user,$oldPwd,$newPwd){
        $userId = $user['id'];
        $salt = $user['salt'];
        $oldSaltPwd = $this->user_hash ( $oldPwd, $salt );
        if($oldSaltPwd != $user['login_password']){
            //旧密码错误
            throw new LogicException ( T ( 'Old password error' ), 131 );
        }
        //更新新的密码
        $salt = PhalApi_Tool::createRandStr ( 8 );
        $loginPwd = $this->user_hash ( $newPwd, $salt );
        $data = array('login_password' => $loginPwd,'salt' => $salt);
        $rs = $this->model->update($userId,$data);
        return $rs;
    }


    /**
     *
     * 只允许一个账号同时一个人登陆
     * @param array $user
     */
    public function login($user,$sysType) {
        $rs = $this->model->getByWhere ( array('login_name' => $user ['login_name']), 'id,salt,login_password,type' );
        if (empty ( $rs )) {
            throw new LogicException ( T ( 'User not exists' ), 104 );
        }

        $checkLoginPwd = $this->user_hash ( $user ['login_password'], $rs ['salt'] );
        if ($checkLoginPwd != $rs ['login_password']) {
            throw new LogicException ( T ( 'Username or password error' ), 133 );
        }
        $user = $this->getLoginUserInfo($rs['id']);
        //判断是否有系统登录权限
        if($rs['type'] != $sysType){
            throw new LogicException ( T ( 'User can not  login the system' ), 198 );
        }
        //判断是否禁用
        if($user['is_enable'] == 'n'){
            throw new LogicException ( T ( 'User is already ban' ), 132 );
        }

        $domainUserSession = new Domain_Zhianbao_RegulatorSession();
        //更新登录IP
        $time = time();
        $ip = PhalApi_Tool::getClientIp();
        $data = array(
            'last_visit_time' => $time,
            'last_visit_ip' => $ip,
        );
        $this->updateLogin($user['id'],$data);
        $user = array_merge($user,$data);
        //登录成功后插入登录成功日志

        $logModel = new Model_Zhianbao_RegulatorLoginLog();
        $log_data = array(
            'regulator_id' => $user['id'],
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
        return $user;
    }
    public function updateLogin($userId,$data){
        $rs = $this->model->update($userId,$data);
        return $rs;
    }
    private function userLoginData($data){
        $logDataModel = new Model_Zhianbao_RegulatorLoginData();
        $filter = array(
            'regulator_id' => $data['regulator_id'],
            'login_name' => $data['login_name']
        );
        $info = $logDataModel->getByWhere($filter,'*');
        if(empty($info)){
            $log_data = array(
                'regulator_id' => $data['regulator_id'],
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
    public function getLoginUserInfo($userId){
        $rs = $this->model->get($userId);
        return $rs;
    }

    public function getAllCompanyByPage($filter, $page = 1, $page_size = 20, $orderby = '') {
       $regulatorToCompanyModel = new Model_Zhianbao_RegulatorToCustomer();
       $companyModel = new Model_Zhianbao_Company();
       $rs = $regulatorToCompanyModel->getAll ( '*', $filter, $page, $page_size, $orderby );
       foreach ( $rs as $key => $value){
           $rs[$key]['create_time'] = strtotime($value['create_time']) == 0 ? '-': $value['create_time'];
           $rs[$key]['last_modify'] = strtotime($value['last_modify']) == 0 ? '-': $value['last_modify'];
           $companyInfo = $companyModel->get($value['company_id']);
           $rs[$key]['company_info'] = $companyInfo;
       }
        return $rs;
    }
    public function getCompanyCount($filter){
        $regulatorToCompanyModel = new Model_Zhianbao_RegulatorToCustomer();
        $count = $regulatorToCompanyModel->getCount($filter);
        return $count;
    }
    //获取首页详情
    public function getDashBoard($regulatorId){
        $rs = array();
        //发文通知代签收
        $noticeModel = new Model_Zhianbao_Notice();
        //已发送通知但未全部签收
        $filter = array(
            'regulator_id' => $regulatorId,
            'is_sign' => 'n',
            'is_release' => 'y'
        );
        $rs['unSignNoticeCount'] = $noticeModel->getCount($filter);
        //安全生产标准化申报待审核
        $safeModel = new Model_Zhianbao_SafeApply();
        $companyIds = $this->getCompanyIds($regulatorId);
        $filter = array('company_id' => $companyIds,'status' => 'applying');
        $rs['safeSelfApplyingCount'] = $safeModel->getCount($filter);
        //应急演练申报
        $urgentModel = new Model_Zhianbao_UrgentPlan();
        $filter = array('company_id' => $companyIds,'status' => 'wait');
        $rs['urgentWaitCount'] = $urgentModel->getCount($filter);
        //检查计划
        $planModel = new Model_Zhianbao_CheckPlan();
        $filter = array('company_id' => $companyIds,'status' => 0);
        $rs['unCheckCount'] = $planModel->getCount($filter);
        //待处理事故记录
        $troubleModel = new Model_Zhianbao_CheckTrouble();
        $filter = array('company_id' => $companyIds,'status' => 0);
        $rs['unDealTrouble'] = $troubleModel->getCount($filter);
        //近一周发文通知-已发布的
        $filter = array(
            'regulator_id' => $regulatorId,
            'create_time < ?' => time(),
            'create_time > ?' => time() - 7 * 86400,
            'is_release' => 'y'
        );
        $rs['lastWeekNoticeCount'] = $noticeModel->getCount($filter);
        //近一年事故记录
        $filter = array(
            'company_id' => $companyIds,
            'create_time < ?' => time(),
            'create_time > ?' => time() - 365 * 86400,
        );
        $rs['lastYearTroubleCount'] = $troubleModel->getCount($filter);
        //知识库文章数
        $knowledgeModel = new Model_Zhianbao_Knowledge();
        $filter = array(
            'regulator_id' => $regulatorId,
        );
        $rs['knowledgeCount'] = $knowledgeModel->getCount($filter);
        return $rs;
    }


}
