<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Order_Check_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'orderStatus' => array('name' => 'order_status', 'type' => 'enum','range' => array('y','n','all'), 'require' => true, 'desc' => '订单状态: y 已发布 n 未发布 all 全部'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
     * 获平台订单列表
   * #desc 用于获取平台订单列表
   * #return int code 操作码，0表示成功
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $filter = array();
        $filter['is_jfy'] = 'y';
        //已发布的订单
        if($this->orderStatus == 'y'){
            $filter['publish'] = array('y','order');
        }
        //未发布的订单
        if($this->orderStatus == 'n'){
            $filter['publish'] = 'n';
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
