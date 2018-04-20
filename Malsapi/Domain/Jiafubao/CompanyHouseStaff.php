<?php
class Domain_Jiafubao_CompanyHouseStaff {
	var $model;

	public function __construct() {
		$this->model = new Model_Jiafubao_CompanyHouseStaff();
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
            $rs['crime_experience'] = json_decode($rs['crime_experience'], true);
            $rs['create_time'] = date("Y-m-d H:i:s", $rs['create_time']);
            $rs['last_modify'] = date("Y-m-d H:i:s", $rs['last_modify']);
            $rs['native_place_district'] = json_decode($rs['native_place_district'], true);
            if(!empty($rs['idcard_p'])){
                $rs['idcard_p'] = json_decode($rs['idcard_p'], true);
            }else{
                $rs['idcard_p'] = array();
            }
            if(!empty($rs['idcard_p'])){
                $rs['idcard_n'] = json_decode($rs['idcard_n'], true);
            }else{
                $rs['idcard_n'] = array();
            }
            $domainArea = new Domain_Area();
            if($rs['native_place'] > 0){
                //籍贯
                $rs['native_place_name'] = $domainArea->getAreaNameById($rs['native_place']);
            }else{
                $rs['native_place_name'] = '';
            }
            if(!empty($rs['native_place_district'])){
                //拼接户籍省市区
                $province = $domainArea->getAreaNameById($rs['native_place_district']['province']);
                $city = $domainArea->getAreaNameById($rs['native_place_district']['city']);
                $district = $domainArea->getAreaNameById($rs['native_place_district']['district']);
                $rs['native_place_district_name'] = $province.$city.$district;
            }else{
                $rs['native_place_district_name'] = '';
            }
            $rs['now_district_name'] = '';
            //判断是否住店、获取现居住地址
            if($rs['is_dormitory'] == 'n'){
                if(!empty($rs['now_district'])){
                    $rs['now_district'] = json_decode($rs['now_district'], true);
                    //拼接现居住省市区
                    $now_province = $domainArea->getAreaNameById($rs['now_district']['province']);
                    $now_city = $domainArea->getAreaNameById($rs['now_district']['city']);
                    $now_district = $domainArea->getAreaNameById($rs['now_district']['district']);
                    $rs['now_district_name'] = $now_province.$now_city.$now_district;
                }
            }
       //     print_r($rs);exit;
            //获取公司信息
            $companyModel = new Model_Zhianbao_Company();
            $companyInfo = $companyModel->get($rs['company_id']);
            $rs['company_name'] = $companyInfo['name'];
            //获取公司电话
            $jfbCompanyModel = new Model_Jiafubao_Company();
            $filter = array('company_id' => $rs['company_id']);
            $company_info = $jfbCompanyModel->getByWhere($filter,'telephone');
            $rs['company_telephone'] = $company_info['telephone'];
            //获取家政员工作需求
            $demandDomain = new Domain_Jiafubao_StaffDemand();
            $demandInfo = $demandDomain->getBaseInfo($rs['company_id'],$rs['id']);
            $rs['staff_demand'] = $demandInfo;
        }

        return $rs;
    }
    //添加家政人员
    public function addHouseStaff($data,$demand_data){
        $rs = $this->model->insert($data);
        if( !$rs){
            throw new LogicException (T('Add failed'), 102);
        }else{
            //添加家政员业务需求
            $demand_data['staff_id'] = $rs;
            $demandDomain = new Domain_Jiafubao_StaffDemand();
            $demandDomain->updateDemand($demand_data);
            //添加到总库
            $staffModel = new Model_Jiafubao_Staff();
            $filter = array('cardID' => $data['cardID']);
            $info = $staffModel->getByWhere($filter,'*');
            if(empty($info)){
                $res = $staffModel->insert($filter);
                if(!$res){
                    throw new LogicException (T('Add failed'), 102);
                }
            }
        }

//        if($rs){
//            $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
//            $houseStaffDomain->checkStaff($rs);
//        }else{
//            throw new LogicException (T('Add failed'), 102);
//        }
        return $rs;
    }
    //更新家政人员
    public function updateHouseStaff($data,$demand_data){
        $id = intval($data['staff_id']);
        unset($data['staff_id']);
        $rs = $this->model->update($id,$data);
        if( !$rs){
            return false;
        }else{
            //添加家政员业务需求
            $demandDomain = new Domain_Jiafubao_StaffDemand();
            $demandDomain->updateDemand($demand_data);
        }
//        if(!$rs){
//            return false;
//        }else{
//            $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
//            $houseStaffDomain->checkStaff($id);
//        }
        return $rs;
    }
    //删除家政人员
    public function deleteHouseStaff($staffId){
        $rs = $this->model->delete($staffId);
        return $rs;
    }
    //更新家政员上下岗
    public function isPostHouseStaff($data){
        $id = intval($data['staff_id']);
        unset($data['staff_id']);
        $rs = $this->model->update($id,$data);
        if( !$rs){
            return false;
        }
        return $rs;
    }
    //判断是否家政员是否在订单中
    public function isUserOrder($filter){
        $rs = true;
        $orderToStaffModel = new Model_Jiafubao_OrderStaff();
        $filter['status'] = array('test','work');
        $info = $orderToStaffModel->getByWhere($filter, '*');
        if( !empty($info)){
            return false;
        }
        return $rs;
    }
    //获取列表
	public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
        $workStaffIds = array();
        if(isset($filter['can_work'])){
            $orderModel = new Model_Jiafubao_Order();
            $orderFilter = array('company_id' => $filter['company_id'],'staff_id > ?'=> 0, 'order_status' => array('work','test'));
            $workStaffList = $orderModel->getAll('staff_id',$orderFilter);
            foreach ($workStaffList as $key => $value){
                $workStaffIds[] = $value['staff_id'];
            }
        }
        $companyModel = new Model_Zhianbao_Company();
        unset($filter['can_work']);
		$rs = $this->model->getAll ( 'id,company_id,name,cardID,birthday,sex,mobile,is_check,online,create_time,last_modify,is_post', $filter, $page, $page_size, $orderby );
		$return = array();
        foreach ($rs as $key=>$value){
            if(in_array($value['id'],$workStaffIds)){
                continue;
            }
            $rs[$key]['birthday'] = date("Y-m-d",$value['birthday']);
            $companyInfo = $companyModel->get($value['company_id']);
            $rs[$key]['company_name'] = $companyInfo['name'];
            $return[] = $rs[$key];
        }
       // $return['total'] = $total;
		return $return;
	}
	//获取可使用家政员列表
    public function getCanOrderAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
        $rs = $this->model->getAll ( 'id,name,sex', $filter, $page, $page_size, $orderby );
        $zabCompanyId = $filter['company_id'];
        $jfbCompanyModel = new Model_Jiafubao_Company();
        $shareStaffModel = new Model_Jiafubao_CompanyShareHouseStaff();
        $jfbCompanyInfo = $jfbCompanyModel->getByWhere(array('company_id' => $zabCompanyId),'id');
        $jfbCompanyId = $jfbCompanyInfo['id'];
        $shareFilter = $filter;
        $shareFilter['company_id'] = $jfbCompanyId;
        $shareList = $shareStaffModel->getAll('staff_id,name,sex',$shareFilter);
        foreach ($shareList as $key => $value){
            $shareList[$key]['id'] = $value['staff_id'];
            $shareList[$key]['name'] = $value['name'].'【荐】';
        }
        $return = array_merge($rs,$shareList);
        //获取推荐的家政员
        return $return;
    }
	//获取数量
	public function getCount($filter) {
	   // $count = $this->model->getCount ( $filter );
        $count = 0;
        $workStaffIds = array();
        if(isset($filter['can_work'])){
            $orderModel = new Model_Jiafubao_Order();
            $orderFilter = array('company_id' => $filter['company_id'],'staff_id > ?'=> 0, 'order_status' => array('work','test'));
            $workStaffList = $orderModel->getAll('staff_id',$orderFilter);
            foreach ($workStaffList as $key => $value){
                $workStaffIds[] = $value['staff_id'];
            }
        }
        unset($filter['can_work']);
        $rs = $this->model->getAll ( 'id,company_id,name,cardID,birthday,sex,mobile,is_check,online,create_time,last_modify,is_post', $filter );
        //$total = count($rs);
        if(isset($filter['can_work'])) {
            foreach ($rs as $key => $value) {
                if (in_array($value['id'], $workStaffIds)) {
                    $count++;
                }
            }
        }else{
            $count = $this->model->getCount ( $filter );
        }
		return $count;
	}
	//按更新时间排序
	public function timeSort($list){
	    if(empty($list)){
            $person = array();
        }else{
            $person = $this->my_sort($list,'last_modify',SORT_DESC,SORT_STRING);
        }
        return $person;
    }
    function my_sort($arrays,$sort_key,$sort_order = SORT_DESC,$sort_type = SORT_NUMERIC ){
        if(is_array($arrays)){
            foreach ($arrays as $array){
                if(is_array($array)){
                    $key_arrays[] = $array[$sort_key];
                }else{
                    return false;
                }
            }
        }else{
            return false;
        }
        array_multisort($key_arrays,$sort_order,$sort_type,$arrays);
        return $arrays;
    }
	//检测身份证号码
    public function hashCardID($companyId,$cardID){
        $filter = array('company_id' => $companyId,'cardID' => $cardID);
        $rs = $this->model->getByWhere($filter,'*');
        return $rs;
    }
	//检测是否正在使用
    public function isUser($staffId){
        $houseKeepCardModel = new Model_Jiafubao_CompanyHouseStaffCard();
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
        $staffModel = $this->model;
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
        $staffModel = $this->model;
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
    //审核公司家政员并同步至平台
    public function checkStaff($companyStaffId)
    {
//        $staffModel = new Model_Jiafubao_HouseStaff();
//        $companyStaffCardModel = new Model_Jiafubao_CompanyHouseStaffCard();
//        $staffCardModel = new Model_Jiafubao_HouseKeepCard;
//        $companyAbilityCertModel = new Model_Jiafubao_CompanyStaffAbilityCert();
//        $abilityCertModel = new Model_Jiafubao_StaffAbilityCert();
//        $companyDemandModel = new Model_Jiafubao_CompanyStaffDemand();
//        $demandModel = new Model_Jiafubao_StaffDemand();
//        $healthModel = new Model_Jiafubao_StaffHealthCard();
//        $companyHealthModel = new Model_Jiafubao_CompanyStaffHealthCard();
//        $insuranceModel = new Model_Jiafubao_StaffInsurance();
//        $companyInsuranceModel = new Model_Jiafubao_CompanyStaffInsurance();
//        $skillModel = new Model_Jiafubao_StaffSkillCert();
//        $companySkillModel = new Model_Jiafubao_CompanyStaffSkillCert();
//        $staffId = false;
//        //更新状态为已审核
//        $data = array('is_check' => 'y', 'last_modify' => time());
//        $rs = $this->model->update($companyStaffId, $data);
//        if (!$rs) {
//            return false;
//        }
//        $companyStaffInfo = $this->model->get($companyStaffId, 'name,avatar,birthday,sex,mobile,address,cardID,learn_experience,work_experience,society_experience,crime_experience,online,nation,education,marriage,industry,native_place,native_place_district,native_place_address,now_district,is_dormitory,is_post');
//        if (!$companyStaffInfo) {
//            return false;
//        }
//
//        //查看总库是否有该家政员
//        $filter = array('cardID' => $companyStaffInfo['cardID']);
//        $staffInfo = $staffModel->getByWhere($filter);
//        if (!$staffInfo) {
//            //添加新的家政员
//            $staffId = $staffModel->insert($companyStaffInfo);
//            if (!$staffId) {
//                return false;
//            }
//        } else {
//            //更新家政员信息
//            $rs = $staffModel->update($staffInfo['id'], $companyStaffInfo);
//            if (!$rs) {
//                return false;
//            }
//        }
//
//        //同步家政卡信息
//        $filter = array('staff_id' => $companyStaffId, 'is_check' => 'n');
//        $companyStaffCardInfo = $companyStaffCardModel->getByWhere($filter, 'id,name,card_bn,end_time');
//        if ($companyStaffCardInfo) {
//            //更新家政卡为已审核
//            $data = array('is_check' => 'y', 'last_modify' => time());
//            $rs = $companyStaffCardModel->update($companyStaffCardInfo['id'], $data);
//            if (!$rs) {
//                return false;
//            }
//            if ($staffId) {
//                //新增的家政员
//                $companyStaffCardInfo['staff_id'] = $staffId;
//                $companyStaffCardInfo['create_time'] = time();
//                $companyStaffCardInfo['last_modify'] = time();
//                unset($companyStaffCardInfo['id']);
//                $cardId = $staffCardModel->insert($companyStaffCardInfo);
//                if (!$cardId) {
//                    return false;
//                }
//            } else {
//                $filter = array('staff_id' => $staffInfo['id']);
//                $search = $staffCardModel ->getByWhere($filter);
//                if($search){
//                    //更新的家政员
//                    $companyStaffCardInfo['staff_id'] = $staffInfo['id'];
//                    $companyStaffCardInfo['last_modify'] = time();
//                    unset($companyStaffCardInfo['id']);
//                    $filter = array('staff_id' => $staffInfo['id']);
//                    $rs = $staffCardModel->updateByWhere($filter, $companyStaffCardInfo);
//                    if (!$rs) {
//                        return false;
//                    }
//                }else{
//                    $companyStaffCardInfo['staff_id'] = $staffInfo['id'];
//                    $companyStaffCardInfo['create_time'] = time();
//                    $companyStaffCardInfo['last_modify'] = time();
//                    unset($companyStaffCardInfo['id']);
//                    $rs = $staffCardModel->insert($companyStaffCardInfo);
//                    if (!$rs) {
//                        return false;
//                    }
//                }
//            }
//        }
//
//
//        //同步专项能力证书
//        $filter = array('staff_id' => $companyStaffId, 'is_check' => 'n');
//        $companyAbilityCertList = $companyAbilityCertModel->getAll('id,type,img_url,level,cert_bn,issued,train_course,train_time,train_score,train_organization,train_periods,skill,status,remark', $filter);
//        if ($companyAbilityCertList) {
//            foreach ($companyAbilityCertList as $key => $value) {
//                //更新为已审核
//                $data = array('is_check' => 'y', 'last_modify' => time());
//                $rs = $companyAbilityCertModel->update($value['id'], $data);
//                if (!$rs) {
//                    return false;
//                }
//                if ($staffId) {
//                    $data = array(
//                        'staff_id' => $staffId,
//                        'type' => $value['type'],
//                        'img_url' => $value['img_url'],
//                        'level' => $value['level'],
//                        'cert_bn' => $value['cert_bn'],
//                        'issued' => $value['issued'],
//                        'train_course' => $value['train_course'],
//                        'train_time' => $value['train_time'],
//                        'train_score' => $value['train_score'],
//                        'train_organization' => $value['train_organization'],
//                        'train_periods' => $value['train_periods'],
//                        'skill' => $value['skill'],
//                        'status' => $value['status'],
//                        'remark' => $value['remark'],
//                        'create_time' => time(),
//                        'last_modify' => time(),
//                    );
//                    $certId = $abilityCertModel->insert($data);
//                    if (!$certId) {
//                        return false;
//                    }
//                } else {
//                    //更新的家政员-先查找证书是否存在,存在的话更新不存在插入
//                    $filter = array('staff_id' => $staffInfo['id'], 'type' => $value['type'], 'level' => $value['level']);
//                    $certInfo = $abilityCertModel->getByWhere($filter);
//                    $data = array(
//                        // 'staff_id' => $staffInfo['id'],
//                        'type' => $value['type'],
//                        'img_url' => $value['img_url'],
//                        'level' => $value['level'],
//                        'cert_bn' => $value['cert_bn'],
//                        'issued' => $value['issued'],
//                        'train_course' => $value['train_course'],
//                        'train_time' => $value['train_time'],
//                        'train_score' => $value['train_score'],
//                        'train_organization' => $value['train_organization'],
//                        'train_periods' => $value['train_periods'],
//                        'skill' => $value['skill'],
//                        'status' => $value['status'],
//                        'remark' => $value['remark'],
//                        //   'create_time' => time(),
//                        'last_modify' => time(),
//                    );
//                    if ($certInfo) {
//                        //证书存在
//                        $rs = $abilityCertModel->update($certInfo['id'], $data);
//                        if (!$rs) {
//                            return false;
//                        }
//                    } else {
//                        //证书不存在
//                        $data['staff_id'] = $staffInfo['id'];
//                        $data['create_time'] = time();
//                        $rs = $abilityCertModel->insert($data);
//                        if (!$rs) {
//                            return false;
//                        }
//                    }
//                }
//            }
//        }
//
//        //同步家政员需求
//        $filter = array('staff_id' => $companyStaffId, 'is_check' => 'n');
//        $companyStaffDemandInfo = $companyDemandModel->getByWhere( $filter , 'id,demand,expected_salary,good_cuisine,cook_taste,is_home,work_time,work_picture');
//        if ($companyStaffDemandInfo) {
//            if ($staffId) {
//                //新增的家政员
//                $data = array(
//                    'staff_id' => $staffId,
//                    'demand' => $companyStaffDemandInfo['demand'],
//                    'expected_salary' => $companyStaffDemandInfo['expected_salary'],
//                    'good_cuisine' => $companyStaffDemandInfo['good_cuisine'],
//                    'cook_taste' => $companyStaffDemandInfo['cook_taste'],
//                    'is_home' => $companyStaffDemandInfo['is_home'],
//                    'work_time' => $companyStaffDemandInfo['work_time'],
//                    'create_time' => time(),
//                    'last_modify' => time(),
//                    'work_picture' => $companyStaffDemandInfo['work_picture'],
//                );
//                $certId = $demandModel->insert($data);
//                if (!$certId) {
//                    return false;
//                }
//            } else {
//                $filter = array('staff_id' => $staffInfo['id']);
//                $search = $demandModel ->getByWhere($filter);
//                if($search){
//                    $data = array(
//                        'demand' => $companyStaffDemandInfo['demand'],
//                        'expected_salary' => $companyStaffDemandInfo['expected_salary'],
//                        'good_cuisine' => $companyStaffDemandInfo['good_cuisine'],
//                        'cook_taste' => $companyStaffDemandInfo['cook_taste'],
//                        'is_home' => $companyStaffDemandInfo['is_home'],
//                        'work_time' => $companyStaffDemandInfo['work_time'],
//                        'last_modify' => time(),
//                    );
//                    $filter = array('staff_id' => $staffInfo['id']);
//                    $rs = $demandModel->updateByWhere($filter, $data);
//                    if (!$rs) {
//                        return false;
//                    }
//                }else{
//                    $data = array(
//                        'staff_id' => $staffInfo['id'],
//                        'demand' => $companyStaffDemandInfo['demand'],
//                        'expected_salary' => $companyStaffDemandInfo['expected_salary'],
//                        'good_cuisine' => $companyStaffDemandInfo['good_cuisine'],
//                        'cook_taste' => $companyStaffDemandInfo['cook_taste'],
//                        'is_home' => $companyStaffDemandInfo['is_home'],
//                        'work_time' => $companyStaffDemandInfo['work_time'],
//                        'create_time' => time(),
//                        'last_modify' => time(),
//                    );
//                    $rs = $demandModel->insert($data);
//                    if (!$rs) {
//                        return false;
//                    }
//                }
//            }
//        }
//
//        //同步健康卡
//        $filter = array('staff_id' => $companyStaffId, 'is_check' => 'n', 'status' => 'y');
//        $companyHealthInfo = $companyHealthModel->getByWhere( $filter , 'id,health_level,card_bn,send_time,end_time,status,img_url');
//        if ($companyHealthInfo) {
//            if ($staffId) {
//                //新增的家政员
//                $data = array(
//                    'staff_id' => $staffId,
//                    'health_level' => $companyHealthInfo['health_level'],
//                    'card_bn' => $companyHealthInfo['card_bn'],
//                    'send_time' => $companyHealthInfo['send_time'],
//                    'end_time' => $companyHealthInfo['end_time'],
//                    'status' => $companyHealthInfo['status'],
//                    'img_url' => $companyHealthInfo['img_url'],
//                    'create_time' => time(),
//                    'last_modify' => time(),
//                );
//                $healthId = $healthModel->insert($data);
//                if (!$healthId) {
//                    return false;
//                }
//            } else {
//                $filter = array('staff_id' => $staffInfo['id']);
//                $search = $healthModel ->getByWhere($filter);
//                if($search){
//                    //更新的家政员
//                    $data = array(
//                        'health_level' => $companyHealthInfo['health_level'],
//                        'card_bn' => $companyHealthInfo['card_bn'],
//                        'send_time' => $companyHealthInfo['send_time'],
//                        'end_time' => $companyHealthInfo['end_time'],
//                        'status' => $companyHealthInfo['status'],
//                        'img_url' => $companyHealthInfo['img_url'],
//                        'last_modify' => time(),
//                    );
//                    $filter = array('staff_id' => $staffInfo['id']);
//                    $rs = $healthModel->updateByWhere($filter, $data);
//                    if (!$rs) {
//                        return false;
//                    }
//                }else{
//                    //插入新的卡
//                    $data = array(
//                        'staff_id' => $staffInfo['id'],
//                        'health_level' => $companyHealthInfo['health_level'],
//                        'card_bn' => $companyHealthInfo['card_bn'],
//                        'send_time' => $companyHealthInfo['send_time'],
//                        'end_time' => $companyHealthInfo['end_time'],
//                        'status' => $companyHealthInfo['status'],
//                        'img_url' => $companyHealthInfo['img_url'],
//                        'create_time' => time(),
//                        'last_modify' => time(),
//                    );
//                    $rs = $healthModel->insert( $data);
//                    if (!$rs) {
//                        return false;
//                    }
//                }
//
//            }
//        }
//
//
//
//        //同步保险记录
//        $filter = array('staff_id' => $companyStaffId, 'is_check' => 'n');
//        $companyInsuranceInfo = $companyInsuranceModel->getByWhere( $filter , 'id,title,content,insured_name,policy_bn,end_time,is_delete');
//        if ($companyInsuranceInfo) {
//            //$companyInsuranceInfo['end_time'] = strtotime($companyInsuranceInfo['end_time']);
//            //更新为已审核
//            $data = array('is_check' => 'y', 'last_modify' => time());
//            $rs = $companyInsuranceModel->update($companyInsuranceInfo['id'], $data);
//            if (!$rs) {
//                return false;
//            }
//            if ($staffId) {
//                $data = array(
//                    'staff_id' => $staffId,
//                    'title' => $companyInsuranceInfo['title'],
//                    'content' => $companyInsuranceInfo['content'],
//                    'insured_name' => $companyInsuranceInfo['insured_name'],
//                    'policy_bn' => $companyInsuranceInfo['policy_bn'],
//                    'end_time' => $companyInsuranceInfo['end_time'],
//                    'create_time' => time(),
//                    'last_modify' => time(),
//                );
//                $insuranceId = $insuranceModel->insert($data);
//                if (!$insuranceId) {
//                    return false;
//                }
//            } else {
//                $filter = array('staff_id' => $staffInfo['id'], 'title' => $companyInsuranceInfo['title'], 'content' => $companyInsuranceInfo['content'], 'insured_name' => $companyInsuranceInfo['insured_name'], 'policy_bn' => $companyInsuranceInfo['policy_bn'], 'end_time' => $companyInsuranceInfo['end_time']);
//                $insuranceInfo = $insuranceModel->getByWhere($filter);
//                if (! $insuranceInfo) {
//                    //证书存在
//                    unset($data['is_check']);
//                    $rs = $insuranceModel->update($insuranceInfo['id'], $data);
//                    if (!$rs) {
//                        return false;
//                    }
//                    //保险记录不存在-先删除所有的保险记录
//                    $filter = array('staff_id' => $staffInfo['id']);
//                    $rs = $insuranceModel->deleteByWhere($filter);
//                    //添加新的保险记录
//                    $data = array(
//                        'title' => $companyInsuranceInfo['title'],
//                        'staff_id' => $staffInfo['id'],
//                        'content' => $companyInsuranceInfo['content'],
//                        'insured_name' => $companyInsuranceInfo['insured_name'],
//                        'policy_bn' => $companyInsuranceInfo['policy_bn'],
//                        'end_time' => $companyInsuranceInfo['end_time'],
//                        'create_time' => time(),
//                        'last_modify' => time(),
//                    );
//                    $rs = $insuranceModel->insert($data);
//                    if (!$rs) {
//                        return false;
//                    }
//                }
//
//            }
//        }
//
//        //同步技能证书
//        $filter = array('staff_id' => $companyStaffId, 'is_check' => 'n');
//        $companySkillCertList = $companySkillModel->getAll('id,type,img_url,level,cert_bn,issued,occupation,theoretical_score,operating_score,evaluation_score,train_organization,accreditation_time,status,remark', $filter);
//        if ($companySkillCertList) {
//            foreach ($companySkillCertList as $key => $value) {
//                //更新为已审核
//                $data = array('is_check' => 'y', 'last_modify' => time());
//                $rs = $companySkillModel->update($value['id'], $data);
//                if (!$rs) {
//                    return false;
//                }
//                if ($staffId) {
//                    $data = array(
//                        'staff_id' => $staffId,
//                        'type' => $value['type'],
//                        'img_url' => $value['img_url'],
//                        'level' => $value['level'],
//                        'cert_bn' => $value['cert_bn'],
//                        'issued' => $value['issued'],
//                        'occupation' => $value['occupation'],
//                        'theoretical_score' => $value['theoretical_score'],
//                        'operating_score' => $value['operating_score'],
//                        'evaluation_score' => $value['evaluation_score'],
//                        'train_organization' => $value['train_organization'],
//                        'accreditation_time' => $value['accreditation_time'],
//                        'status' => $value['status'],
//                        'remark' => $value['remark'],
//                        'create_time' => time(),
//                        'last_modify' => time(),
//                    );
//                    $certId = $skillModel->insert($data);
//                    if (!$certId) {
//                        return false;
//                    }
//                } else {
//                    //更新的家政员-先查找证书是否存在,存在的话更新不存在插入
//                    $filter = array('staff_id' => $staffInfo['id'], 'type' => $value['type'], 'level' => $value['level']);
//                    $certInfo = $skillModel->getByWhere($filter);
//                    $data = array(
//                        // 'staff_id' => $staffInfo['id'],
//                        'type' => $value['type'],
//                        'img_url' => $value['img_url'],
//                        'level' => $value['level'],
//                        'cert_bn' => $value['cert_bn'],
//                        'issued' => $value['issued'],
//                        'occupation' => $value['occupation'],
//                        'theoretical_score' => $value['theoretical_score'],
//                        'operating_score' => $value['operating_score'],
//                        'evaluation_score' => $value['evaluation_score'],
//                        'train_organization' => $value['train_organization'],
//                        'accreditation_time' => $value['accreditation_time'],
//                        'status' => $value['status'],
//                        'remark' => $value['remark'],
//                        //   'create_time' => time(),
//                        'last_modify' => time(),
//                    );
//                    if ($certInfo) {
//                        //证书存在
//                        $rs = $skillModel->update($certInfo['id'], $data);
//                        if (!$rs) {
//                            return false;
//                        }
//                    } else {
//                        //证书不存在
//                        $data['staff_id'] = $staffInfo['id'];
//                        $data['create_time'] = time();
//                        $rs = $skillModel->insert($data);
//                        if (!$rs) {
//                            return false;
//                        }
//                    }
//                }
//            }
//        }


        return true;

    }

    //生成家政员二维码
    public function qrcode($staffId){
     //   $url = 'http://jfbadh5.mshenpu.com/shareOrder?staffId='.$staffId;
        $url = 'http://jfbadh5.mshenpu.com/shareStaff?staffId='.$staffId;
        $rs = $qrCode = 'http://pan.baidu.com/share/qrcode?w=150&h=150&url='.$url;
        return $rs;
    }
    //家政员证书
    public function share($filter){

        $abilityCertModel = new Model_Jiafubao_CompanyStaffAbilityCert();
        $skillCertModel = new Model_Jiafubao_CompanyStaffSkillCert();
        //获取专项能力证书
        $ability_list = $abilityCertModel->getAll('*',$filter);
        //获取职业资格证书
        $skill_list = $skillCertModel->getAll('*',$filter);
        $rs = array_merge($ability_list,$skill_list);

        return $rs;
    }
    //根据身份证号返回对应的星座
    public function constellation($cid) {
     //   if (!$this->isIdCard($cid)) return '';
        $bir = substr($cid,10,4);
        $month = (int)substr($bir,0,2);
        $day = (int)substr($bir,2);
        $strValue = '';
        if (($month == 1 && $day >= 20) || ($month == 2 && $day <= 18)) {
            $strValue = "水瓶座";
        } else if (($month == 2 && $day >= 19) || ($month == 3 && $day <= 20)) {
            $strValue = "双鱼座";
        } else if (($month == 3 && $day > 20) || ($month == 4 && $day <= 19)) {
            $strValue = "白羊座";
        } else if (($month == 4 && $day >= 20) || ($month == 5 && $day <= 20)) {
            $strValue = "金牛座";
        } else if (($month == 5 && $day >= 21) || ($month == 6 && $day <= 21)) {
            $strValue = "双子座";
        } else if (($month == 6 && $day > 21) || ($month == 7 && $day <= 22)) {
            $strValue = "巨蟹座";
        } else if (($month == 7 && $day > 22) || ($month == 8 && $day <= 22)) {
            $strValue = "狮子座";
        } else if (($month == 8 && $day >= 23) || ($month == 9 && $day <= 22)) {
            $strValue = "处女座";
        } else if (($month == 9 && $day >= 23) || ($month == 10 && $day <= 23)) {
            $strValue = "天秤座";
        } else if (($month == 10 && $day > 23) || ($month == 11 && $day <= 22)) {
            $strValue = "天蝎座";
        } else if (($month == 11 && $day > 22) || ($month == 12 && $day <= 21)) {
            $strValue = "射手座";
        } else if (($month == 12 && $day > 21) || ($month == 1 && $day <= 19)) {
            $strValue = "魔羯座";
        }
        return $strValue;

    }
    // 检查是否是身份证号
    function isIdCard($number) {
        //检查是否是身份证号
        // 转化为大写，如出现x
        $number = strtoupper($number);
        //加权因子
        $wi = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        //校验码串
        $ai = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        //按顺序循环处理前17位
        $sigma = 0;
        for($i = 0;$i < 17;$i++){
            //提取前17位的其中一位，并将变量类型转为实数
            $b = (int) $number{$i};      //提取相应的加权因子
            $w = $wi[$i];     //把从身份证号码中提取的一位数字和加权因子相乘，并累加
            $sigma += $b * $w;
        }

        //计算序号
        $snumber = $sigma % 11;
        //按照序号从校验码串中提取相应的字符。
        $check_number = $ai[$snumber];
        if($number{17} == $check_number){
            return true;
        }else{
            return false;
        }
    }
    //根据身份证号，自动返回对应的生肖
    function get_shengxiao($cid) {
      //  if(!$this->isIdCard($cid)) return '';
        $start = 1901;
        $end = $end = (int)substr($cid,6,4);
        $x = ($start - $end) % 12;
        $value = "";
        if($x == 1 || $x == -11){
            $value = "鼠";
        }
        if($x == 0) {
            $value = "牛";
        }
        if($x == 11 || $x == -1){
            $value = "虎";
        }
        if($x == 10 || $x == -2){
            $value = "兔";
        }
        if($x == 9 || $x == -3){
            $value = "龙";
        }
        if($x == 8 || $x == -4){
            $value = "蛇";
        }
        if($x == 7 || $x == -5){
            $value = "马";
        }
        if($x == 6 || $x == -6){
            $value = "羊";
        }
        if($x == 5 || $x == -7){
            $value = "猴";
        }
        if($x == 4 || $x == -8){
            $value = "鸡";
        }
        if($x == 3 || $x == -9){
            $value = "狗";
        }
        if($x == 2 || $x == -10){
            $value = "猪";
        }
        return $value;
    }
    function getAgeByID($cid){
        if(empty($cid)) return '';
        $date=strtotime(substr($cid,6,8));
        $today=strtotime('today');
        $diff=floor(($today-$date)/86400/365);
        $age=strtotime(substr($cid,6,8).' +'.$diff.'years')>$today?($diff+1):$diff;

        return $age;
    }

}
