<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Order_Update extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'orderId' => array('name' => 'order_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '订单ID'),
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
                'address' => array('name' => 'address', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '详细地址'),
                'mark' => array('name' => 'mark', 'type' => 'string', 'require' => false, 'desc' => '客户备注'),
                'orderAmount' => array('name' => 'order_amount', 'type' => 'float', 'min' => 0.00, 'require' => false, 'desc' => '订单金额'),
            ),
        );
    }

    /**
     * 更新家政订单信息
     * #desc 用于更新家政订单信息
     * #return int code 操作码，0表示成功
     * #return int order_id 订单ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        //判断订单是否存在
        $orderDomain = new Domain_Jiafubao_Order();
        $orderInfo = $orderDomain->getBaseInfo($this->orderId);
        if (empty($orderInfo)) {
            $rs['code'] = 164;
            $rs['msg'] = T('Order not exists');
            return $rs;
        }
        //判断订单是否关闭或终止
        if($orderInfo['order_status'] == 'close' || $orderInfo['order_status'] =='cancel'){
            $rs['code'] = 229;
            $rs['msg'] = T('Order close');
            return $rs;
        }
        //判断订单是否完成
        if($orderInfo['order_status'] == 'finish'){
            $rs['code'] = 228;
            $rs['msg'] = T('Order finish');
            return $rs;
        }

        $data = array(
            'order_id' => $this->orderId,
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
            'last_modify' => time(),
        );
        //是否填写金额
        if(isset($this->orderAmount)){
            $data['total_amount'] = $this->orderAmount;
            $data['service_amount'] = $this->orderAmount;
        }
        $orderDomain  = new Domain_Jiafubao_Order();
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $orderDomain->updateOrder($data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        $rs['info']['order_id'] = $this->orderId;

        return $rs;
    }

}
