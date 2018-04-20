<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Order_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
//                'companyId' => array('name' => 'company_id', 'type' => 'int',  'require' => true, 'default' => 0 ,'desc' => '公司ID'),
                'userId' => array('name' => 'user_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'customerId' => array('name' => 'customer_id', 'type' => 'int', 'min' => 1, 'require' => false, 'desc' => '客户ID'),
                'customerMobile' => array('name' => 'customer_mobile', 'type' => 'string', 'require' => false, 'desc' => '发布用户电话'),
                'serviceType' => array('name' => 'service_type' , 'type' => 'enum' , 'range' => array('tempClean','quickClean','longHours','nanny','careElderly','careBaby','matron','depthClean'),'require' => true, 'desc' => '服务类型 tempClean:临时保洁,quickClean:宅速洁,longHours:长期钟点工,nanny:保姆,careElderly:看护老人,careBaby:育儿嫂,matron:月嫂,depthClean:开荒保洁'),
                'content' => array('name' => 'content', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '服务内容 json类型 array(
   "serviceTimeType" => "",//服务时间类型，shor短期,long长期
   "serviceBeginTime" => "",//服务开始时间
   "serviceEndTime" => "",//服务结束时间
   "liveHome" => "",//是否住家 y/n
   "hasPet" => "",//是否有宠物 none:没有,smallDog:小型犬,bigDog:大型犬,cat:猫,other:其他
   "homeArea" => "",//房屋面积
   "cookieTaste" => "",//做饭口味
   "weekServiceTime" => "",//每周服务时间
   "dayServiceBeginTime" => "",//每天服务开始时间
   "dayServiceEndTime" => "",//每天服务结束时间
   "oldManSex" => "",//老人性别
   "oldManAge" => "",//老人年龄
   "independLive" => "",//生活自理 all:全自理,half:半自理,cant:不能自理
   "needDrug" => "",//用药情况 y:需要用药,n:不需要用药
   "babyCount" => "",//宝宝数量
   "babySex" => "",//宝宝性别
   "dueDate" => "",//预产期
   "extRequire" => "",//其他需求
 ); 
            '),
                'consignee' => array('name' => 'consignee', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '收货人'),
                'mobile' => array('name' => 'ship_mobile', 'type' => 'string', 'min' => 11, 'max'=> 11,'require' => true, 'desc' => '手机号码'),
                'country' => array('name' => 'country', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '国家'),
                'province' => array('name' => 'province', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '省份'),
                'city' => array('name' => 'city', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '城市'),
                'district' => array('name' => 'district', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '区县'),
                'mark' => array('name' => 'mark', 'type' => 'string', 'require' => true, 'default' => '', 'desc' => '买家备注'),
                'address' => array('name' => 'address', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '详细地址'),
                'source' => array('name' => 'source', 'type' => 'enum', 'range' => array('online','offline'),  'require' => true, 'desc' => '订单来源'),
                'goodsOrderId' => array('name' => 'goods_order_id', 'type' => 'int', 'min' => 0, 'require' => false, 'desc' => '商品订单ID'),
                'orderAmount' => array('name' => 'order_amount', 'type' => 'float', 'min' => 0.00, 'require' => false, 'desc' => '订单金额'),
            ),
        );
    }

    /**
     * 发布家政订单
     * #desc 用于发布家政订单
     * #return int code 操作码，0表示成功
     * #return int demand_id  需求ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //获取用户
        $domain = new Domain_Jiafubao_User();
        $userInfo = $domain->getBaseByUserId($this->userId);
        if( empty($userInfo)){
            $rs['code'] = 112;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }
        //判断会员是否存在
        if(isset($this->customerMobile)) {
            $companyCustomerDomain = new Domain_Jiafubao_CompanyCustomer();
            $id = $companyCustomerDomain->getBaseInfoByFilter($this->userId,$this->customerMobile);
            if( !$id) {
                $rs['code'] = 102;
                $rs['msg'] = T('Add failed');
                return $rs;
            }
            $customerId = $id;
        }
        if(isset($this->customerId)){
            $customerId = $this->customerId;
        }

        $data = array(
//            'company_id' => $this->companyId,
//            'accept_company_id' => $this->companyId,
            'company_id' => $userInfo['id'],
            'accept_company_id' => $userInfo['id'],
            'customer_id' => $customerId,
            'service_type' => $this->serviceType,
            'content' => $this->content,
            'consignee' => $this->consignee,
            'mobile' => $this->mobile,
            'country' => $this->country,
            'province' => $this->province,
            'city' => $this->city,
            'district' => $this->district,
            'address' => $this->address,
            'mark' => $this->mark,
            'publish' => 'n',
            'order_status' => 'confirm',
            'change_time' => 0,
            'source' => $this->source,
            'create_time' => time(),
            'last_modify' => time(),
            'goods_order_id' => $this->goodsOrderId,
        );
        if($data['source'] == 'offline'){
            $data['goods_order_id'] = 0;
        }
        //是否填写金额
        if(isset($this->orderAmount)){
            $data['total_amount'] = $this->orderAmount;
            $data['service_amount'] = $this->orderAmount;
        }
        $orderDomain  = new Domain_Jiafubao_Order();
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $orderId = $orderDomain->addOrder($data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        $rs['info']['order_id'] = $orderId;
        $info = $orderDomain->getBaseInfo($orderId);
        if( $info){
            $rs['info']['order_bn'] = $info['bn'];
        }
        return $rs;
    }

}
