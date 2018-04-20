<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_Customer_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'name' => array('name' => 'name', 'type' => 'string', 'require' => false, 'desc' => '客户姓名'),
                     'mobile' => array('name' => 'mobile', 'type' => 'string', 'require' => false, 'desc' => '手机号'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
     * 获取订单客户列表
     * #desc 用于获取订单客户列表
     * #return int code 操作码，0表示成功
     * #return int id 客户ID
     * #return int mobile 手机号
     * #return int create_time 创建时间
     * #return int last_modify 最后更新时间
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断公司是否存在
        $domainCompany = new Domain_Zhianbao_Company();
        $companyInfo = $domainCompany->getBaseInfo($this->companyId);
        if (empty($companyInfo)) {
            DI()->logger->debug('Company not exists', $this->companyId);

            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }

        $filter = array('company_id' => $this->companyId);
        $orderDomain = new Domain_Jiafubao_Order();
        $list = $orderDomain->getAllCustomer($filter,$this->mobile,$this->page,$this->pageSize,$this->orderby);
        $total = $list['total'];unset($list['total']);
       // $total = $orderDomain->getCount($filter);

        $rs['list'] = $list;
        $rs['total'] = $total;

        return $rs;
    }
	
}
