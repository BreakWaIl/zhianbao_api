<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Order_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id', 'type' => 'string', 'require' => false, 'desc' => '监管者ID'),
                     'companyId' => array('name' => 'company_id', 'type' => 'string', 'require' => false, 'desc' => '公司ID'),
                     'customerId' => array('name' => 'customer_id', 'type' => 'int', 'require' => false, 'desc' => '会员ID'),
                     'staffId' => array('name' => 'staff_id', 'type' => 'string', 'require' => false, 'desc' => '家政员ID'),
                     'orderStatus' => array('name' => 'order_status', 'type' => 'enum','range' => array('wait','confirm','test','work','test_close','change','close','finish','all'), 'require' => true, 'desc' => '订单状态'),
                     'publish' => array('name' => 'publish', 'type' => 'enum','range' => array('y','n','order'), 'require' => false, 'desc' => '是否发布到市场'),
                     'isJfy' => array('name' => 'is_jfy', 'type' => 'enum','range' => array('y','n'), 'require' => false, 'desc' => '是否为平台订单:y 是 n 否'),
                     'searchType' => array('name' => 'search_type', 'type' => 'enum','range' => array('myPublish','myAccept'), 'require' => false, 'desc' => '查询类型 myPublish:我发布的 myAccept:我接单的'),
                     'consignee' => array('name' => 'consignee', 'type' => 'string',  'require' => false, 'desc' => '收货人'),
                     'mobile' => array('name' => 'ship_mobile', 'type' => 'string', 'min' => 11, 'max'=> 11,'require' => false, 'desc' => '手机号码'),
                     'beginTime' => array('name' => 'begin_time', 'type' => 'string', 'min' => 10, 'max'=> 10,'require' => false, 'desc' => '开始创建时间'),
                     'endTime' => array('name' => 'end_time', 'type' => 'string', 'min' => 10, 'max'=> 10,'require' => false, 'desc' => '结束创建时间'),
                     'upPrice' => array('name' => 'up_price', 'type' => 'float', 'require' => false, 'desc' => '订单金额上限'),
                     'downPrice' => array('name' => 'down_price', 'type' => 'float', 'require' => false, 'desc' => '订单金额下限'),
                     'source' => array('name' => 'source', 'type' => 'enum','range' => array('microShop','offlineShop','market'), 'require' => false, 'desc' => '订单来源'),
                     'serviceType' => array('name' => 'service_type' , 'type' => 'enum' , 'range' => array('tempClean','quickClean','longHours','nanny','careElderly','careBaby','matron','depthClean'),'require' => false, 'desc' => '服务类型 tempClean:临时保洁,quickClean:宅速洁,longHours:长期钟点工,nanny:保姆,careElderly:看护老人,careBaby:育儿嫂,matron:月嫂,depthClean:开荒保洁'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
     * 获取客户订单列表
   * #desc 用于获取当前店铺中的订单列表
   * #return int code 操作码，0表示成功
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $filter = array();
        if(isset($this->regulatorId)) {
            //检测监管者是否存在
            $regulatorDomain = new Domain_Zhianbao_Regulator();
            $regulatorInfo = $regulatorDomain->getBaseInfo($this->regulatorId);
            if (!$regulatorInfo) {
                $rs['code'] = 118;
                $rs['msg'] = T('Regulator not exists');
                return $rs;
            }
            $companyIds = $regulatorDomain->getCompanyIds($this->regulatorId);

            if(empty($companyIds)){
                $rs['count'] = 0;
                $rs['list'] = array();
                return $rs;
            }
            $filter ['company_id'] = $companyIds;
        }
        if(isset($this->companyId)) {
            //判断公司是否存在
            $domainCompany = new Domain_Zhianbao_Company();
            $companyInfo = $domainCompany->getBaseInfo($this->companyId);
            if (empty($companyInfo)) {
                $rs['code'] = 100;
                $rs['msg'] = T('Company not exists');
                return $rs;
            }
//            $filter ['accept_company_id'] = $this->companyId;
        }
        if(isset($this->staffId)) {
            //判断家政员是否存在
            $staffDomain = new Domain_Jiafubao_CompanyHouseStaff();
            $staffInfo = $staffDomain->getBaseInfo($this->staffId);
            if (empty($staffInfo)) {
                $rs['code'] = 126;
                $rs['msg'] = T('Staff not exists');
                return $rs;
            }
            $filter['staff_id'] = $staffInfo['id'];
        }
        if(isset($this->publish)){
            $filter['publish'] = $this->publish;
            //查看市场中的订单-此时只能查看公司当地的订单
            $jfbCompanyModel = new Model_Jiafubao_Company();
            $companyFilter = array('company_id' => $companyInfo['id']);
            $jfbComapnyInfo = $jfbCompanyModel->getByWhere($companyFilter);
            if( (!$jfbComapnyInfo) || ($jfbComapnyInfo['city'] < 1)){
                //请先完善地区
                $rs['code'] = 220;
                $rs['msg'] = T('Please improve the company information first');
                return $rs;
            }else{
                $filter['city'] = $jfbComapnyInfo['city'];
            }
            if(isset($this->isJfy)){
                $filter['is_jfy'] = $this->isJfy;
            }
        }
        if(isset($this->mobile)){
            $filter['mobile'] = $this->mobile;
        }
        if(isset($this->consignee)){
            $filter['consignee'] = $this->consignee;
        }
        if(isset($this->customerId)){
            $filter['customer_id'] = $this->customerId;
        }
        if($this->orderStatus != 'all'){
            if($this->orderStatus == 'close'){
                $filter['order_status'] = array('cancel','close');
            }else if($this->orderStatus == 'confirm'){
                $filter['order_status'] = array('confirm','change');
            }else{
                $filter['order_status'] = $this->orderStatus;
            }
        }
        if(isset($this->searchType)){
            if($this->searchType == 'myPublish'){
                $filter ['company_id'] = $this->companyId;
                $filter['publish != ?'] = 'n';
                unset($filter ['accept_company_id']);
            }
            if($this->searchType == 'myAccept'){
                $filter ['accept_company_id'] = $this->companyId;
                unset($filter ['company_id']);
                $filter['publish != ?'] = 'y';
            }
        }
        if(isset($this->source)){
            if($this->source == 'microShop'){
                $filter['source'] = 'online';
            }
            if($this->source == 'offlineShop'){
                $filter['source'] = 'offline';
            }
            if($this->source == 'market'){
                $filter['publish'] = array('y','order');
            }
            $filter ['accept_company_id'] = $this->companyId;
        }
        if(isset($this->beginTime) && isset($this->endTime)){
            $filter['create_time > ?'] = $this->beginTime;
            $filter['create_time < ?'] = $this->endTime;
        }
        if(isset($this->upPrice)){
            $filter['total_amount < ?'] = $this->upPrice;
        }
        if(isset($this->downPrice)){
            $filter['total_amount > ?'] = $this->downPrice;
        }
        if(isset($this->serviceType)){
            $filter['service_type'] = $this->serviceType;
        }
        $domain = new Domain_Jiafubao_Order();
        $list = $domain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $total = $domain->getCount($filter);

        $rs['total'] = $total;
        $rs['list'] = $list;

        return $rs;
    }
	
}
