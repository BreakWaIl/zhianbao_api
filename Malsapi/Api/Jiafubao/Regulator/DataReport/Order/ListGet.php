<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Regulator_DataReport_Order_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'require' => false, 'desc' => '公司ID'),
                     'beginTime' => array('name' => 'begin_time', 'type'=>'int', 'require'=> false,'desc'=> '开始时间'),
                     'endTime' => array('name' => 'end_time', 'type'=>'int', 'require'=> false,'desc'=> '结束时间'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
   * 获取订单金额统计列表
   * #desc 用于获取订单金额统计列表
   * #return int code 操作码，0表示成功
   * #return int id 公司ID
   * #return int user_id 用户ID
   * #return int name 公司名称
   * #return int mobile 账号
   * #return int create_time 创建时间
   * #return int last_modify 最后更新时间
   * #return int orderTotal 订单总数
   * #return int finishTotal 完成数量
   * #return int amountTotal 累计订单金额
 */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //检测监管者是否存在
        $regulatorDomain = new Domain_Zhianbao_Regulator();
        $regulatorInfo = $regulatorDomain->getBaseInfo($this->regulatorId);
        if(! $regulatorInfo){
            $rs['code'] = 118;
            $rs['msg'] = T('Regulator not exists');
            return $rs;
        }
        $companyIds = $regulatorDomain->getCompanyIds($this->regulatorId);
        $filter = array('id' => $companyIds);
        if(!empty($this->companyId)){
            $filter['id'] = $this->companyId;
        }

        $dataReportDomain = new Domain_Jiafubao_DataReport();
        $list = $dataReportDomain->regulatorGetAllByPage($filter,$this->beginTime,$this->endTime,$this->page,$this->pageSize,$this->orderby);
        $total = $dataReportDomain->RegulatorGetCount($filter);

        $rs['count'] = $total;
        $rs['list'] = $list;
        return $rs;
    }
	
}
