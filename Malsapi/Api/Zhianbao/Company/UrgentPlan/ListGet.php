<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_UrgentPlan_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'name' => array('name' => 'name', 'type' => 'string', 'require' => false, 'desc' => '预案名称'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
     * 获取应急预案列表
     * #desc 用于获取应急预案列表
     * #return int code 操作码，0表示成功
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
        if(!empty($this->name)){
            $filter['name LIKE ?'] = '%'.$this->name.'%';
        }

        $planDomain = new Domain_Zhianbao_UrgentPlan();
        $list = $planDomain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $total = $planDomain->getCount($filter);

        $rs['list'] = $list;
        $rs['total'] = $total;

        return $rs;
    }
	
}
