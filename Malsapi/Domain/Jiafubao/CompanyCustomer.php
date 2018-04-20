<?php
class Domain_Jiafubao_CompanyCustomer {

    //手动添加客户
    public function offlineAdd($data){
        $shopInfo = $this->getShopInfo($data['user_id']);
        $shopId = $shopInfo['id'];
        if($shopId > 0){
            $params = array(
                'shop_id' => $shopId,
                'company_id' => $data['company_id'],
                'name' => $data['name'],
                'sex' => $data['sex'],
                'mobile' => $data['mobile'],
                'province' => $data['province'],
                'city' => $data['city'],
                'district' => $data['district'],
                'address' => $data['address'],
                'source' => 'jfb_offline',
                'is_check' => 'n',
            );

            $result = malsapi_request('Shenpu_Customer_Add.Go',$params);
            $result = $result['data'];
            if( $result['code'] == 0 ){
                $result = $result['info']['customer_id'];
            }
        }else{
            return false;
        }

        return $result;
    }
    //获取客户列表
    public function getAllCustomer($userId,$name,$mobile,$page,$pageSize,$orderby){
        $rs = array();
        $shopInfo = $this->getShopInfo($userId);
        $shopId = $shopInfo['id'];
        if( $shopId > 0){
            $params = array(
                'shop_id' => $shopId,
                'page' => $page,
                'page_size' => $pageSize,
                'orderby' => $orderby,
            );
            if(!empty($name)){
                $params['login_name'] = $name;
            }
            if(!empty($mobile)){
                $params['mobile'] = $mobile;
            }
            $result = malsapi_request('Shenpu_Customer_ListGet.Go',$params);
            $result = $result['data'];
            if($result['code'] == 0){
                $rs['list'] = $result['list'];
                $rs['total'] = $result['total'];
            }else{
                return false;
            }
        }else{
            return false;
        }

        return $rs;
    }
    //获取客户详情
    public function getBaseInfo($customerId){
        $params = array(
            'customer_id' => $customerId,
        );

        $result = malsapi_request('Shenpu_Customer_InfoGet.Go',$params);
        $result = $result['data'];
        if($result['code'] == 0){
            $result = $result['info'];
        }else{
            return false;
        }
        return $result;
    }
    //更新客户信息
    public function updateCustomer($data){
        $params = array(
            'customer_id' => $data['customer_id'],
            'realname' => $data['realname'],
            'sex' => $data['sex'],
            'birthday' => $data['birthday'],
            'country' => 1,
            'province' => $data['province'],
            'city' => $data['city'],
            'district' => $data['district'],
            'address' => $data['address'],
            'remark' => $data['remark'],
        );

        $result = malsapi_request('Shenpu_Customer_Update.Go',$params);
        $result = $result['data'];
        return $result;
    }
    //判断会员是否存在
    public function getBaseInfoByFilter($userId,$customerMobile){
        $shopInfo = $this->getShopInfo($userId);

        $shopId = $shopInfo['id'];
        if( $shopId == 0){
            return false;
        }
        $shop_params = array(
            'shop_id' => $shopId,
            'mobile' => $customerMobile,
        );
        $result = malsapi_request('Shenpu_Customer_ListGet.Go',$shop_params);
        $result = $result['data'];
        if($result['code'] == 0){
            if($result['total'] > 0){
                $customerId = $result['list'][0]['id'];
            }else{
                //客户不存在 创建客户
                $params = array(
                    'shop_id' => $shopId,
                    'realname' => $customerMobile,
                    'mobile' => $customerMobile,
                    'source' => 'jfb_offline',
                );
                $info = malsapi_request('Shenpu_Customer_Offline_Add.Go',$params);
                $info = $info['data'];
                if($info['code'] != 0){
                    return false;
                }else{
                    $customerId = $info['info']['customer_id'];
                }
            }
        }else{
            return false;
        }
        return $customerId;
    }
    public function getShopInfo($userId){
        //获取用户的店铺
        $shop_params = array(
            'user_id' => $userId,
            'operator_id' => $userId,
        );
        $list = malsapi_request('Shenpu_Shop_ListGet.Go',$shop_params);
        $list = $list['data']['list'];
        if( ! isset($list[0])){
            return array('id' => 0);
        }else{
            $rs = $list[0];
        }
        return $rs;
    }
    //监管者获取客户列表
    public function reguGetAllCustomer($regulatorId,$name,$mobile,$page,$pageSize){
        $rs = array();
        //获取监管者的公司列表
        $company_list = $this->getAllCompanyIds($regulatorId);
        $shop_list = array();
        foreach ($company_list as $key=>$value){
            //获取公司的神铺店铺
            $shop_list[] = $this->getShopInfo($value);
        }
        //var_dump(111);exit;
//        print_r($shop_list);exit;
        //获取所有客户
        $result = array();
        foreach ($shop_list as $kk=>$vv){
            if($vv['id'] > 0) {
                $params = array(
                    'shop_id' => $vv['id'],
                    'page' => 1,
                    'page_size' => 99999,
                    'orderby' => 'id:desc',
                );
                $customer_list = malsapi_request('Shenpu_Customer_ListGet.Go', $params);
                $result[] = $customer_list['data']['list'];
            }
        }
       // print_r($result);exit;
        //重组客户列表
        $new_customer_list = array();
        foreach($result as $_k=>$_v){
            foreach($_v as $_nk => $_nv){
                $new_customer_list[] = $_nv;
            }
        }
        //按真实姓名搜索
        if(!empty($name)){
            $search = array();
            foreach ($new_customer_list as $key=>$val){
                if($val['realname'] ==  $name ){
                    $search[] = $val;
                }
            }
            $new_customer_list = $search;
        }
        //按手机号搜索
        if(!empty($mobile)){
            $search = array();
            foreach ($new_customer_list as $key=>$val){
                if($val['mobile'] ==  $mobile ){
                    $search[] = $val;
                }
            }
            $new_customer_list = $search;
        }
        $list = array();
        $total = 0;
        if(!empty($new_customer_list)){
            $start = ($page-1) * $pageSize;
            $list = array_slice($new_customer_list, $start, $pageSize);
            $total = COUNT($new_customer_list);
        }
        $rs['list'] = $list;
        $rs['total'] = $total;
        return $rs;
    }
    //获取监管者下的公司列表
    public function getAllCompanyIds($regulatorId){
        $regulatorToCustomerModel = new Model_Zhianbao_RegulatorToCustomer();
        $customerModel = new Model_Zhianbao_Company();
        $filter = array('regulator_id' => $regulatorId);
        $customerList = $regulatorToCustomerModel->getAll('*',$filter);
        $customerIds = array();
        foreach ($customerList as $key => $value){
            $info = $customerModel->get($value['company_id']);
            $customerIds[] = $info['user_id'];
        }
        return $customerIds;
    }
}
