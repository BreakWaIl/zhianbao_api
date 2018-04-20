<?php
class Domain_Jiafubao_HouseStaff {
	var $model;

	public function __construct() {
		$this->model = new Model_Jiafubao_HouseStaff();
	}

	//获取详情
    public function getBaseInfo($staffId, $cols = '*'){
        $rs = array ();
        $id = intval ( $staffId );
        if ($id <= 0) {
            return $rs;
        }

        $rs = $this->model->get ( $id);

        if (! $rs){
            return false;
        }else{
            $rs['avatar'] = json_decode($rs['avatar'], true);
            $rs['birthday'] = date("Y-m-d", $rs['birthday']);
            $rs['create_time'] = date("Y-m-d H:i:s", $rs['create_time']);
            $rs['last_modify'] = date("Y-m-d H:i:s", $rs['last_modify']);
//            $companyModel = new Model_Zhianbao_Company();
//            $companyInfo = $companyModel->get($rs['company_id']);
//            $rs['company_name'] = $companyInfo['name'];
        }

        return $rs;
    }
    //添加家政人员
    public function addHouseStaff($data){
        $rs = $this->model->insert($data);
        if(!$rs){
            throw new LogicException ( T ( 'Add failed' ) , 102 );
        }
        return $rs;
    }
    //更新家政人员
    public function updateHouseStaff($data){
        $id = intval($data['staff_id']);
        unset($data['staff_id']);
        $rs = $this->model->update($id,$data);
        if(!$rs){
            throw new LogicException ( T ( 'Update failed' ) , 104 );
        }
        return $rs;
    }
    //删除家政人员
    public function deleteHouseStaff($staffId){
        $rs = $this->model->delete($staffId);
        return $rs;
    }
    //获取列表
	public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
        $companyModel = new Model_Zhianbao_Company();
		$rs = $this->model->getAll ( 'id,company_id,name,birthday,sex,mobile,create_time,last_modify', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key=>$value){
            $rs[$key]['birthday'] = date("Y-m-d",$value['birthday']);
            $companyInfo = $companyModel->get($value['company_id']);
            $rs[$key]['company_name'] = $companyInfo['name'];
        }
		return $rs;
	}
	//获取数量
	public function getCount($filter) {
		return $this->model->getCount ( $filter );
	}
	//检测身份证号码
    public function hashCardID($cardID){
        $filter = array('cardID' => $cardID);
        $rs = $this->model->getByWhere($filter,'*');
        return $rs;
    }
	//检测是否正在使用
    public function isUser($staffId){
        $houseKeepCardModel = new Model_Jiafubao_HouseKeepCard();
        $filter = array('staff_id' => $staffId);
        //获取家政卡
        $rs = $houseKeepCardModel->getAll('*',$filter);

        return $rs;
    }
    //检测家政员是否存在
    public function getBaseName($companyId,$staffName){
        $filter = array(
            'company_id' => $companyId,
            'name LIKE ?' => '%'.$staffName.'%',
        );
        $rs = $this->model->getAll('id',$filter);
        return $rs;
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
        $staffAuthModel = new Model_Jiafubao_StaffAuth();
        $authInfo = $staffAuthModel->getByWhere($data);
        if(! $authInfo){
            //当前openid和customerid不匹配的情况下，查看是否存在customerid的auth，存在的话更新openid
            $filter = array('staff_id'=>$data['staff_id']);
            $authInfo = $staffAuthModel->getByWhere($filter);
            if($authInfo){
                $staffAuthModel->update($authInfo['id'],$data);
                $authId = $authInfo['id'];
            }else {
                $authId = $staffAuthModel->insert($data);
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
        $staffAuthModel = new Model_Jiafubao_StaffAuth();
        $customerAuthInfo = $staffAuthModel->getByWhere($data);
        if(! $customerAuthInfo){
            return false;
        }
        $staffId = $customerAuthInfo['staff_id'];
        $staffSession = new Domain_Jiafubao_StaffSession();
        $session =  $staffSession->makeSession($staffId);
        return $session;
    }


    //验证码登录
    public function codeLogin($mobile){
        $rs = array ();
        $filter = array('mobile' => $mobile);
        //获取客户信息c
        $staffModel = new Model_Jiafubao_HouseStaff();
        $rs = $staffModel->getByWhere ( $filter );
        if (! $rs){
            return false;
        }
//        $customerSession = new Domain_Shenpu_CustomerSession();
//        $session =  $customerSession->makeSession($rs ['id']);
        $staffSessionDomain = new Domain_Jiafubao_StaffSession();
        $session =  $staffSessionDomain->makeSession($rs ['id']);
        //种入COOKIE
        $rs['session'] = $session;
        //   DI ()->cookie->set('sp_csid',$session);
        return $rs;
    }

    //登录
    public function Login($filter,$pwd) {
        $rs = array ();

        //获取客户信息
        $staffModel = new Model_Zhianbao_Staff();
        $rs = $staffModel->getByWhere ( $filter );

        if (! $rs){
            return false;
        }
//        $customerSession = new Domain_Shenpu_CustomerSession();
//       $session =  $customerSession->makeSession($rs ['id']);
        $staffSessionDomain = new Domain_Jiafubao_StaffSession();
        $session =  $staffSessionDomain->makeSession($rs ['id']);
        //种入COOKIE
        $rs['session'] = $session;
        //   DI ()->cookie->set('sp_csid',$session);
        return $rs;

    }

    //退出登录
    public function logout($staffId){
        //删除session
        $staffSessionDomain = new Domain_Jiafubao_StaffSession();
        $rs =  $staffSessionDomain->deleteSession($staffId);
        if(! $rs){
            return false;
        }
        //删除微信授权
        $filter = array('staff_id' => $staffId);
        $staffAuthModel = new Model_Jiafubao_StaffAuth();
        $customerAuthInfo = $staffAuthModel->deleteByWhere($filter);
        if(! $customerAuthInfo){
            return false;
        }
        return true;
    }

    //家政员注册
    public function staffRegister($data){
        $filter = array(
            'mobile' => $data['mobile'],
        );
        $mobile = $this->model->getByWhere($filter,$fields = 'mobile');
        if ($mobile) {
            throw new LogicException ( T ( 'Mobile exists' ), 158 );
        }
        $staffId = $this->model->insert ( $data );
        if (! $staffId) {
            throw new LogicException ( T ( 'Create failed' ), 144 );
        }
        return $staffId;
    }



}
