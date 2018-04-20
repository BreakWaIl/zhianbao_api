<?php
class Domain_Jiafubao_User {
	var $model;

	public function __construct() {
		$this->model = new Model_Zhianbao_Company();
	}
    //商户登录
    public function userLogin($user){
        $params = array(
            'login_name' => $user['login_name'],
            'login_password' => $user['login_password'],
            'sysname_name' => $user['sys_type'],
        );

        $result = malsapi_request('Shenpu_User_Login.Go',$params);
        $result = $result['data'];
        if( $result['code'] == 0 ){
            //判断账号所有开通服务
            if(!empty($result['user']['service'] )){
                foreach ($result['user']['service'] as $key=>$value){
                    //判断是否开通家政服务
                    if($value['service_name'] == 'jfb'){
                        $result = $result['user'];
                    }
                }
            }
        }
        return $result;
    }
    //获取权限
    public function authRole($groupId){
        $firstAuth = array();
        $groupModel = new Model_Zhianbao_UserGroup();
        $userAuthModel = new Model_Jiafubao_UserAuthRole();
        $groupInfo = $groupModel->get($groupId);
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
        return $firstAuth;
    }
    //获取公司信息
    public function getBaseByUserId($userId){
        $filter = array('user_id'=>$userId, 'source' =>'jfb');
        $info = $this->model->getByWhere($filter,'*');
        return $info;
    }
    //获取家服宝公司信息
    public function getJfbBaseInfoByUserId($userId){
        $filter = array('user_id'=>$userId, 'source' =>'jfb');
        $zabCompanyinfo = $this->model->getByWhere($filter,'*');
        if(! $zabCompanyinfo){
            return false;
        }else{
            $domain = new Domain_Jiafubao_Company();
            $info = $domain->getBaseInfo($zabCompanyinfo['id']);
            return $info;
        }
    }
    //获取sessionCheck
    public function checkSession($session){
        $params= array(
            'session' => $session,
            'sysname_name' => 'jfb',
        );
        $result = malsapi_request('Shenpu_User_SessionCheck.Go',$params);
        $result = $result['data'];
    //    print_r($result);exit;
        //判断session是否有效
        if( $result['status'] == 1 ){
            //判断是否为主账号
            if($result['info']['parent_id'] == 0){
                $result['jfb']['user_id'] = $result['info']['user_id'];
                $result['jfb']['parent_id'] = $result['info']['parent_id'];
                //获取公司信息
                $company = $this->getBaseByUserId($result['info']['user_id']);
                $result['jfb']['company_id'] = $company['id'];

                //分享人的ID
                $result['jfb']['share_user_id'] = $result['info']['user_id'];
            }else{
                //替换子账号user_id
                $result['jfb']['user_id'] = $result['info']['parent_id'];
                $result['jfb']['parent_id'] = $result['info']['parent_id'];
                //获取公司信息
                $company = $this->getBaseByUserId($result['info']['parent_id']);
                $result['jfb']['company_id'] = $company['id'];

                //分享人的ID
                $result['jfb']['share_user_id'] = $result['info']['user_id'];
            }
            //获取家服保公司信息
            $jfbCompanyDomain = new Domain_Jiafubao_Company();
            $jfbCompanyInfo = $jfbCompanyDomain->getBaseInfo($result['jfb']['company_id']);
            if(! $jfbCompanyInfo){
                return false;
            }else{
                $result['jfb']['jfb_company_id'] = $jfbCompanyInfo['id'];
            }
        }else{
            return false;
        }
        return $result;
    }
    //商户退出
    public function logout($userId){
        $params= array(
            'user_id' => $userId,
            'sysname_name' => 'jfb',
        );

        $result = malsapi_request('Shenpu_User_Logout.Go',$params);
        $result = $result['data'];
        return $result;
    }
    //修改密码
    public function changePwd($loginName,$oldLoginPwd,$newLoginPwd){
        $params= array(
            'login_name' => $loginName,
            'old_login_password' => $oldLoginPwd,
            'new_login_password' => $newLoginPwd,
        );
        $result = malsapi_request('Shenpu_User_ChangePwd.Go',$params);
        $result = $result['data'];
        return $result;
    }
    //找回密码
    public function userFindPwd($loginName,$loginPwd,$code){
        $params= array(
            'login_name' => $loginName,
            'login_password' => $loginPwd,
            'code' => $code,
        );

        $result = malsapi_request('Shenpu_User_FindPwd.Go',$params);
        $result = $result['data'];
        return $result;
    }
    //发送短信
    public function SmsCode($mobile){
        $params= array(
            'mobile' => $mobile
        );

        $result = malsapi_request('Shenpu_Sms_SendCode.Go',$params);
        $result = $result['data'];
        return $result;
    }
    /**
     *
     * 手机号是否注册
     *
     * @param string $mobile
     */
    public function checkRegister($loginName) {
        $rs = array ();

        $filter = array('mobile'=>$loginName);
        $rs = $this->model->getByWhere($filter);

        if (! $rs)
            return false;

        return $rs;
    }

    //删除用户组
    public function delUserGroup($userId,$groupId){
        //获取子账号列表
        $list = $this->getAllSubAccount($userId, $page = 1, $pageSize = 1000, $orderby = 'id:desc');
//        print_r($list);exit;
        if($list['code'] == 0){
            $roleIds = array();
            foreach ($list['list'] as $k=>$v){
                $roleIds[] = $v['role_id'];
            }
            if(!empty($roleIds)){
                //判断该分组下是否有用户
                if(!in_array($groupId,$roleIds)){
                    //删除用户组
                    $userGroupModel = new Model_Zhianbao_UserGroup();
                    $rs = $userGroupModel->delete($groupId);
                    return $rs;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    //添加子账号
    public function subAccountAdd($data){
        $companyCustomerDomain = new Domain_Jiafubao_CompanyCustomer();
        $info = $companyCustomerDomain->getShopInfo($data['user_id']);
        if($info['id'] == 0){
            return false;
        }
        $params= array(
            'user_id' => $data['user_id'],
            'login_name' => $data['login_name'],
            'login_password' => $data['login_password'],
            'shop_id' => $info['id'],
            'role_id' => $data['group_id'],
            'sms_code' => $data['code'],
            'sysname_name' => 'jfb',
        );
        $result = malsapi_request('Shenpu_User_SubAccount_Register.Go',$params);
        $result = $result['data'];
        return $result;
    }

    //获取子账号列表
    public function getAllSubAccount($userId,$page,$pageSize,$orderby){
        $companyCustomerDomain = new Domain_Jiafubao_CompanyCustomer();
        $info = $companyCustomerDomain->getShopInfo($userId);
        if($info['id'] == 0){
            return false;
        }
        $params= array(
            'user_id' => $userId,
            'shop_id' => $info['id'],
            'page' => $page,
            'page_size' => $pageSize,
            'orderby' => $orderby,
        );

        $result = malsapi_request('Shenpu_User_SubAccount_ListGet.Go',$params);

        $result = $result['data'];
        return $result;
    }
    public function getAllrole($all){
        $userGroupModel = new Model_Zhianbao_UserGroup();
        foreach ($all as $k=>$v){
            if($v['role_id'] != 1){
                $info = $userGroupModel->get($v['role_id']);
                $all[$k]['name'] = $info['name'];
            }
            if($v['role_id'] == 1){
                $all[$k]['name'] = '超级管理员';
            }
        }
        return $all;
    }

    //获取监管的公司ID
    public function getCompanyIds($regulatorId){
        $companyModel = new Model_Zhianbao_Company();
        $regulatorToCustomerModel = new Model_Zhianbao_RegulatorToCustomer();
        //获取所有公司
        $list = $companyModel->getAll('*',$filter = array('source' => 'jfb'));
        $customerIds = array();
        foreach ($list as $key=>$value){
            $to_filter = array('company_id' => $value['id']);
            $customer = $regulatorToCustomerModel->getByWhere($to_filter,'*');
            //获取没有监管的公司
            if(empty($customer)){
                $customerIds[] = $value['id'];
            }
        }
        return $customerIds;
    }
    //获取已监管用户列表
    public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
        $rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        return $rs;
    }

    public function getCount($filter){
        return $this->model->getCount ( $filter );
    }
    //添加监管者下属公司
    public function addReguToCompany($regulatorId,$companyId){
        $rs = array();
        $regulatorToCustomerModel = new Model_Zhianbao_RegulatorToCustomer();
        //判断是否已监管
        $filter = array('regulator_id' => $regulatorId, 'company_id' => $companyId);
        $info = $regulatorToCustomerModel->getByWhere($filter,'*');
        if(empty($info)){
            $data = array(
                'regulator_id' => $regulatorId,
                'company_id' => $companyId,
                'create_time' => time(),
                'last_modify' => time(),
            );
            $rs = $regulatorToCustomerModel->insert($data);
        }
        return $rs;
    }
}
