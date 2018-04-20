<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Bill_Sub_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'type' => array('name' => 'type', 'type'=>'enum','range' => array('expenditure','income','borrow','all'), 'default' => 'all', 'require'=> false,'desc'=> '类型：expenditure 支出, income 收入 borrow 借支 all 全部'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
   * 获取管理员出入账单列表
   * #desc 用于获取管理员出入账单列表
   * #return int code 操作码，0表示成功
   * #return int id 账单ID
   * #return int company_id 公司ID
   * #return int sub_id 管理员ID
   * #return string sub_name 管理员ID
   * #return int project_id 项目ID
   * #return string project_name 项目名称
   * #return string type 类型：expenditure 支出, income 收入 borrow 借支
   * #return string title 出入账标题
   * #return float amount 出入金额
   * #return string remark 备注
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
        //获取支出、收入
        if($this->type != 'all'){
            $filter['type'] = $this->type;
        }

        $billSubDomain = new Domain_Building_BillSub();
        $list = $billSubDomain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $count = $billSubDomain->getCount($filter);

        $rs['count'] = $count;
        $rs['list'] = $list;

        return $rs;
    }
	
}
