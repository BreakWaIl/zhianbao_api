<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_DataReport_Service_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'beginTime' => array('name' => 'begin_time', 'type'=>'string', 'require'=> false,'desc'=> '开始时间'),
                     'endTime' => array('name' => 'end_time', 'type'=>'string', 'require'=> false,'desc'=> '结束时间'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
//                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
     * 获取服务质量统计
     * #desc 用于获取服务质量统计
     * #return int code 操作码，0表示成功
     * #return int orderTotal 订单总数
     * #return int order_close_total 退单数量
     * #return int orderCloseRatio 退单率
     * #return int orderChangeRatio 换单率
     * #return int customerFraction 客户平均分
     * #return int staffFraction 家政员平均分
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

        $filter = array();
        $filter['company_id'] = $this->companyId;
        $dataReportDomain = new Domain_Jiafubao_DataReport();

        if(!empty($this->beginTime) && !empty($this->endTime)){
            $filter['beginTime'] = strtotime($this->beginTime);
            $filter['endTime'] = strtotime($this->endTime) + 86400;
        }else{
            $filter['beginTime'] = strtotime(date("Y-m-d",time()-7*86400));
            $filter['endTime'] = strtotime(date("Y-m-d"));
        }
        $list = $dataReportDomain->getServiceByPage($filter,$this->page,$this->pageSize);
        if( !$list){
            $rs['code'] = 189;
            $rs['msg'] = T('The query time can not exceed 90 days');
            return $rs;
        }else{
            $day = $list['day'];
            unset($list['day']);
            $total = $day;
        }

        $rs['list'] = $list;
        $rs['total'] = $total;

        return $rs;
    }
	
}
