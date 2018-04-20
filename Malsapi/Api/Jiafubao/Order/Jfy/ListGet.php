<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Order_Jfy_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'bbcCustomerId' => array('name' => 'bbc_customer_id', 'type' => 'int', 'require' => false, 'desc' => '会员ID'),
                     'orderStatus' => array('name' => 'order_status', 'type' => 'enum','range' => array('noPay','work','close','finish','all'), 'require' => true, 'desc' => '订单状态: noPay 未支付 work 工作中 close 关闭 finish 完成 all 全部'),
                     'serviceType' => array('name' => 'service_type' , 'type' => 'enum' , 'range' => array('tempClean','quickClean','longHours','nanny','careElderly','careBaby','matron','depthClean'),'require' => false, 'desc' => '服务类型 tempClean:临时保洁,quickClean:宅速洁,longHours:长期钟点工,nanny:保姆,careElderly:看护老人,careBaby:育儿嫂,matron:月嫂,depthClean:开荒保洁'),
                     'isJfy' => array('name' => 'is_jfy', 'type' => 'enum', 'range' => array('y','n'), 'default'=>'n', 'require' => true, 'desc' => '是否为平台订单：y 是 n 否'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
     * 获平台客户订单列表
   * #desc 用于获取平台当前客户的订单列表
   * #return int code 操作码，0表示成功
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $filter = array();
        $filter['bbc_customer_id'] = $this->bbcCustomerId;
        $filter['is_jfy'] = $this->isJfy;
        if($this->orderStatus == 'noPay'){
            $filter['pay_status'] = '0';
        }
        if($this->orderStatus == 'work'){
            $filter['order_status'] = 'work';
        }
        if($this->orderStatus == 'close'){
            $filter['order_status'] = array('cancel','close');
        }
        if($this->orderStatus == 'finish'){
            $filter['order_status'] = 'finish';
        }

//print_r($filter);exit;
        $domain = new Domain_Jiafubao_Order();
        $list = $domain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $total = $domain->getCount($filter);

        $rs['total'] = $total;
        $rs['list'] = $list;

        return $rs;
    }
	
}
