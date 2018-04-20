<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Bill_Settle_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'status' => array('name' => 'status', 'type'=>'enum','range' => array('y','n','all'), 'default'=>'all', 'require'=> false,'desc'=> '结算状态: y 已结算 n 未结算 all 全部'),
                     'projectId' => array('name' => 'project_id', 'type' => 'int', 'require' => false, 'desc' => '项目ID'),
                     'staffId' => array('name' => 'staff_id', 'type' => 'int', 'require' => false, 'desc' => '员工ID'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
   * 获取结算单列表
   * #desc 用于获取结算单列表
   * #return int code 操作码，0表示成功
   * #return int id 账单ID
   * #return int company_id 公司ID
   * #return string company_name 公司名称
   * #return int project_id 项目ID
   * #return string project_name 项目名称
   * #return int staff_id 员工ID
   * #return string staff_name 员工名称
   * #return string cardID 身份证号
   * #return string mobile 手机号
   * #return string start_time 开始时间
   * #return string end_time 结束时间
   * #return float work_day 工日
   * #return float work_price 工价
   * #return float total_amount 总计金额
   * #return float borrow_amount 借支金额
   * #return float balance_amount 余额
   * #return string remark 备注
   * #return string settle_status 结算状态: y 已结算 n 未结算
   * #return int operate_id 操作人ID
   * #return int create_time 创建时间
   * #return int last_modify 最后更新时间
   */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断公司是否存在
        $domainCompany = new Domain_Zhianbao_Company();
        $companyInfo = $domainCompany->getBaseInfo($this->companyId);
        if (empty($companyInfo)) {
            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }

        $filter = array('company_id' => $this->companyId);
        //获取已结算、未结算
        if($this->status != 'all'){
            $filter['settle_status'] = $this->status;
        }
        //获取所选项目下的
        if(!empty($this->projectId)){
            $filter['project_id'] = $this->projectId;
        }
        //获取所选员工的
        if(!empty($this->staffId)){
            $filter['staff_id'] = $this->staffId;
        }

        $billSettleDomain = new Domain_Building_BillSettle();
        $list = $billSettleDomain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $count = $billSettleDomain->getCount($filter);

        $rs['count'] = $count;
        $rs['list'] = $list;

        return $rs;
    }
	
}
