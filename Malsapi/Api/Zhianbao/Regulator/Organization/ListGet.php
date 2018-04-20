<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Organization_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'require' => false, 'desc' => '企业ID'),
                     'name' => array('name' => 'name', 'type' => 'string', 'require' => false, 'desc' => '组织名称'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
   * 获取企业安全组织结构列表
   * #desc 用于获取企业安全组织结构列表
   * #return int code 操作码，0表示成功
   * #return int company_id 公司ID
   * #return string company_name 公司名称
   * #return string name 名称
   * #return string content 安全组织结构
   * #return int create_time 创建时间
   * #return int last_modify  最后更新时间
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

        $filter = array();
        $filter['company_id'] = $companyIds;
        if(!empty($this->companyId)){
            if(!in_array($this->companyId, $companyIds)){
                $rs['code'] = 100;
                $rs['msg'] = T('Company not exists');
                return $rs;
            }
            $filter['company_id'] = $this->companyId;
        }
        if(!empty($this->name)){
            $filter['name LIKE ?'] = '%'. $this->name .'%';
        }
        $organizationDomain = new Domain_Zhianbao_Organization();
        $list = $organizationDomain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $total = $organizationDomain->getCount($filter);

        $rs['count'] = $total;
        $rs['list'] = $list;
        return $rs;
    }
	
}
