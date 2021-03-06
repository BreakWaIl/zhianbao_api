<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Order_Jfy_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'bbcCustomerId' => array('name' => 'bbc_customer_id', 'type' => 'int', 'min' => 1, 'require' => false, 'desc' => '客户ID'),
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
                'totalAmount' => array('name' => 'total_amount', 'type' => 'float',  'require' => true, 'desc' => '总金额'),
                'discountAmount' => array('name' => 'discount_amount', 'type' => 'float',  'require' => true, 'desc' => '优惠金额'),
                'serviceAmount' => array('name' => 'service_amount', 'type' => 'float',  'require' => true, 'desc' => '服务金额'),
                'goodsOrderId' => array('name' => 'goods_order_id', 'type' => 'int', 'min' => 0, 'require' => false, 'desc' => '商品订单ID'),
                'isJfy' => array('name' => 'is_jfy', 'type' => 'enum', 'range' => array('y','n'), 'default'=>'n', 'require' => true, 'desc' => '是否为平台订单：y 是 n 否'),
            ),
        );
    }

    /**
     * 添加平台家政订单
     * #desc 用于添加平台家政订单
     * #return int code 操作码，0表示成功
     * #return int demand_id  需求ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $data = array(
            'company_id' => 0,
            'accept_company_id' => 0,
            'customer_id' => 0,
            'bbc_customer_id' => $this->bbcCustomerId,
            'total_amount' => $this->totalAmount,
            'discount_amount' => $this->discountAmount,
            'service_amount' => $this->serviceAmount,
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
            'order_status' => 'wait',
            'change_time' => 0,
            'source' => $this->source,
            'create_time' => time(),
            'last_modify' => time(),
            'goods_order_id' => $this->goodsOrderId,
            'is_jfy' => $this->isJfy,
        );

        $orderDomain  = new Domain_Jiafubao_Order();
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            //发布订单
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
