<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Order_Partner_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'jfbCompanyId' => array('name' => 'jfb_company_id', 'type' => 'string', 'require' => false, 'desc' => '家服云公司ID'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
     * 获取客户合作单列表
     * #desc 用于获取合作单列表
     * #return int code 操作码，0表示成功
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $filter = array('partner_company_id' => $this->jfbCompanyId);
        $domain = new Domain_Jiafubao_OrderPartner();
        $list = $domain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $total = $domain->getCount($filter);

        $rs['total'] = $total;
        $rs['list'] = $list;

        return $rs;
    }
	
}
