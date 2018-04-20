<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Order_Appraise_Add extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'orderId' => array('name' => 'order_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '订单ID'),
                     'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => false, 'desc' => '家政员ID'),
                     'customerId' => array('name' => 'customer_id', 'type' => 'int', 'min' => 1, 'require' => false, 'desc' => '会员ID'),
                     'level' => array('name' => 'level', 'type' => 'int', 'min' => 1, 'max' => 5,'require' => true, 'desc' => '评价星级'),
                     'mark' => array('name' => 'mark', 'type' => 'array','format' => 'json','require' => false, 'desc' => '标签'),
                     'content' => array('name' => 'content', 'type' => 'string','require' => false, 'desc' => '评价内容'),
            ),
		);
 	}
  
  /**
     * 添加订单评价
   * #desc 用于添加订单评价
   * #return int code 操作码，0表示成功
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $data = array(
            'level' => $this->level,
            'content' => $this->content,
            'create_time' => time(),
            'last_modify' => time()
        );
        //判断订单是否存在
        $orderDomain = new Domain_Jiafubao_Order();
        $orderInfo = $orderDomain->getBaseInfo($this->orderId);
        if (empty($orderInfo)) {
            $rs['code'] = 164;
            $rs['msg'] = T('Order not exists');
            return $rs;
        }
        $data['order_id'] = $this->orderId;
       // $data['demand_id'] = $orderInfo['demand_id'];

        if(isset($this->staffId)){
            //判断家政人员是否存在
            $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
            $staffInfo = $houseStaffDomain->getBaseInfo($this->staffId);
            if( !$staffInfo) {
                $rs['code'] = 126;
                $rs['msg'] = T('Staff not exists');
                return $rs;
            }

            $data['appraise_id'] = $this->staffId;
            $data['type'] = 'staff';
        }
        if(isset($this->customerId)){
            //判断会员是否存在
//            $domain = new Domain_Jiafubao_Customer();
//            $info = $domain->getBaseInfo($this->customerId);
//            if (empty($info)) {
//                $rs['code'] = 160;
//                $rs['msg'] = T('Customer not exists');
//                return $rs;
//            }
            //判断是否为平台订单
            if($orderInfo['is_jfy'] == 'y'){
                if($this->customerId != $orderInfo['bbc_customer_id']){
                    $rs['code'] = 160;
                    $rs['msg'] = T('Customer not exists');
                    return $rs;
                }
            }else{
                if($this->customerId != $orderInfo['customer_id']){
                    $rs['code'] = 160;
                    $rs['msg'] = T('Customer not exists');
                    return $rs;
                }
            }
            $data['appraise_id'] = $this->customerId;
            $data['type'] = 'customer';
        }
        if(isset($this->mark)) {
            $data['mark'] = json_encode($this->mark);
        }
        $domain = new Domain_Jiafubao_OrderAppraise();
        //判断是否评价过
        $filter = array(
            'order_id' => $this->orderId,
            'appraise_id' =>  $data['appraise_id'],
            'type' => $data['type'],
        );
        $has = $domain->getBaseInfoByFilter($filter);
        if($has){
            $rs['code'] = 195;
            $rs['msg'] = T('Inrepeatable evaluation');
            return $rs;
        }
        $result = $domain->addAppraise($data);
        if(! $result){
            $rs['code'] = 102;
            $rs['msg'] = T('Add failed');
            return $rs;
        }
        $rs['info']['appraise_id'] = $result;
        return $rs;
    }
	
}
