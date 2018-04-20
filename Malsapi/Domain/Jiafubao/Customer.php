<?php
class Domain_Jiafubao_Customer {
	var $model;

	public function __construct() {
		$this->model = new Model_Jiafubao_Customer();
	}

	//获取详情
    public function getBaseInfo($staffId, $cols = '*'){
        $rs = array ();
        $id = intval ( $staffId );
        if ($id <= 0) {
            return $rs;
        }

//        $rs = $this->model->get ( $id);
//
//        if (! $rs){
//            return false;
//        }else{
//            $rs['create_time'] = date("Y-m-d H:i:s", $rs['create_time']);
//            $rs['last_modify'] = date("Y-m-d H:i:s", $rs['last_modify']);
//        }
        $companyCustomerDomain = new Domain_Jiafubao_CompanyCustomer();
        $rs = $companyCustomerDomain->getBaseInfo($staffId);
        if (! $rs){
            return false;
        }
        return $rs;
    }
    //获取列表
	public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
		$rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
		return $rs;
	}
	//获取数量
	public function getCount($filter) {
		return $this->model->getCount ( $filter );
	}

    //注册
    public function CustomerRegister($data) {
        $filter = array(
            'shop_id' => $data['shop_id'],
            'mobile' => $data['mobile'],
        );
        $mobile = $this->model->getByWhere($filter,$fields = 'mobile');
        if ($mobile) {
            throw new LogicException ( T ( 'Mobile exists' ), 158 );
        }
        $customerId = $this->model->insert ( $data );
        if (! $customerId) {
            throw new LogicException ( T ( 'Create failed' ), 144 );
        }
        return $customerId;

    }



    //验证码登录
    public function codeLogin($mobile){
        $rs = array ();
        $filter = array('login_name' => $mobile);
        //获取客户信息c
        $bbcCustomerModel = new Model_Jiafubao_Customer();
        $rs = $bbcCustomerModel->getByWhere ( $filter );

        if (! $rs){
            //会员不存在---创建会员
            $rs = array(
                'login_name' => $mobile,
                'mobile' => $mobile,
                'source' => 'h5',
                'create_time' => time(),
            );
            $rs['id'] = $bbcCustomerModel->insert($rs);
            if(!  $rs['id']){
                return false;
            }

        }
//        $customerSession = new Domain_Shenpu_CustomerSession();
//        $session =  $customerSession->makeSession($rs ['id']);
        $customerSession = new Domain_Jiafubao_CustomerSession();
        $session =  $customerSession->makeSession($rs ['id']);
        //种入COOKIE
        $rs['session'] = $session;
        //   DI ()->cookie->set('sp_csid',$session);
        return $rs;
    }

    //登录
    public function Login($filter,$pwd) {
        $rs = array ();

        //获取客户信息
        $bbcCustomerModel = new Model_Jiafubao_Customer();
        $rs = $bbcCustomerModel->getByWhere ( $filter );

        if (! $rs){
            return false;
        }else{
            $original_pwd = MD5(MD5($pwd.$rs['salt']));
            if($original_pwd != $rs['login_pwd'] )
                return false;
        }
//        $customerSession = new Domain_Shenpu_CustomerSession();
//       $session =  $customerSession->makeSession($rs ['id']);
        $customerSession = new Domain_Jiafubao_CustomerSession();
        $session =  $customerSession->makeSession($rs ['id']);
        //种入COOKIE
        $rs['session'] = $session;
        //   DI ()->cookie->set('sp_csid',$session);
        return $rs;

    }
    //根据条件查询
    public function getBaseInfoByFilter($filter){
        $rs = $this->model->getByWhere($filter);
        return $rs;
    }

    //更新客户密码
    public function updateLogPwd($mobile,$data){
        $filter = array('mobile'=>$mobile);
        return $this->model->updateByWhere($filter,$data);
    }



    //微信免登-检测会员是否存在
    public function wechatCheck($data){
        $agentInfo = DI ()->cookie->get('zab_agent');
        $agentInfo = json_decode($agentInfo,true);
        if(isset($agentInfo)){
            $data['identify'] =  $agentInfo['openid'];
        }else{
            return false;
        }
        $customerAuthModel = new Model_Jiafubao_CustomerAuth();
        $authInfo = $customerAuthModel->getByWhere($data);
        if(! $authInfo){
            //当前openid和customerid不匹配的情况下，查看是否存在customerid的auth，存在的话更新openid
            $filter = array('customer_id'=>$data['customer_id']);
            $authInfo = $customerAuthModel->getByWhere($filter);
            if($authInfo){
                $customerAuthModel->update($authInfo['id'],$data);
                $authId = $authInfo['id'];
            }else {
                $authId = $customerAuthModel->insert($data);
            }
        }else{
            $authId = $authInfo['id'];
        }
        if(! $authId){
            return false;
        }
        return $authId;
    }



    public function wechatLogin($data){
        $agentInfo = DI ()->cookie->get('zab_agent');
        $agentInfo = json_decode($agentInfo,true);
        if(isset($agentInfo)){
            $data['identify'] =  $agentInfo['openid'];
        }else{
            return false;
        }
        $customerAuthModel = new Model_Jiafubao_CustomerAuth();
        $customerAuthInfo = $customerAuthModel->getByWhere($data);
        if(! $customerAuthInfo){
            return false;
        }
        $bbcCustomerId = $customerAuthInfo['customer_id'];
        $customerSession = new Domain_Jiafubao_CustomerSession();
        $session =  $customerSession->makeSession($bbcCustomerId);
        return $session;
    }

    //退出登录
    public function logout($customerId){
        //删除session
        $customerSession = new Domain_Jiafubao_CustomerSession();
        $rs =  $customerSession->deleteSession($customerId);
//        if(! $rs){
//            return false;
//        }
        //删除微信授权
        $filter = array('customer_id' => $customerId);
        $customerAuth = new Model_Jiafubao_CustomerAuth();
        $customerAuthInfo = $customerAuth->deleteByWhere($filter);
        if(! $customerAuthInfo){
            return false;
        }
        return true;
    }
    //小程序登录
    public function miniSoftLogin($openId){
        $customerAuthModel = new Model_Jiafubao_CustomerAuth();
        $filter['identify'] = $openId;
        $customerAuthInfo = $customerAuthModel->getByWhere($filter);
        if(! $customerAuthInfo){
            return false;
        }
        $bbcCustomerId = $customerAuthInfo['customer_id'];
        $customerSession = new Domain_Jiafubao_CustomerSession();
        $session =  $customerSession->makeSession($bbcCustomerId);
        return $session;
    }


    //小程序验证码登录
    public function miniSoftCodeLogin($mobile,$openId){
        $rs = array ();
        $filter = array('login_name' => $mobile);
        //获取客户信息c
        $bbcCustomerModel = new Model_Jiafubao_Customer();
        $rs = $bbcCustomerModel->getByWhere ( $filter );

        if (! $rs){
            //会员不存在---创建会员
            $rs = array(
                'login_name' => $mobile,
                'mobile' => $mobile,
                'source' => 'h5',
                'create_time' => time(),
            );
            $rs['id'] = $bbcCustomerModel->insert($rs);
            if(!  $rs['id']){
                return false;
            }

        }

        $customerSession = new Domain_Jiafubao_CustomerSession();
        $session =  $customerSession->makeSession($rs ['id']);
        //种入COOKIE
        $rs['session'] = $session;
        //检测授权
        $data = array(
            'customer_id' => $rs['id'],
            'identify' => $openId
        );
        $this->miniSoftWechatCheck($data);
        return $rs;
    }

    //小程序检测授权
    //微信免登-检测会员是否存在
    public function miniSoftWechatCheck($data){
        $customerAuthModel = new Model_Jiafubao_CustomerAuth();
        $authInfo = $customerAuthModel->getByWhere($data);
        if(! $authInfo){
            //当前openid和customerid不匹配的情况下，查看是否存在customerid的auth，存在的话更新openid
            $filter = array('customer_id'=>$data['customer_id']);
            $authInfo = $customerAuthModel->getByWhere($filter);
            if($authInfo){
                $customerAuthModel->update($authInfo['id'],$data);
                $authId = $authInfo['id'];
            }else {
                $authId = $customerAuthModel->insert($data);
            }
        }else{
            $authId = $authInfo['id'];
        }
        if(! $authId){
            return false;
        }
        return $authId;
    }

}
