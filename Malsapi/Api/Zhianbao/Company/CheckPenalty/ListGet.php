<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_CheckPenalty_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id','type'=>'string','require'=> true,'desc'=> '公司ID'),
                     'title' => array('name' => 'name','type'=>'string','require'=> false,'desc'=> '处罚标题'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
   * 获取事故处罚列表
   * #desc 用于获取事故处罚列表
   * #return int code 操作码，0表示成功
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //检测公司是否存在
        $companyDomain = new Domain_Zhianbao_Company();
        $companyInfo = $companyDomain->getBaseInfo($this->companyId);
        if(! $companyInfo){
            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }
        $companyIds = $this->companyId;
        $penaltyDomain = new Domain_Zhianbao_CheckPenalty();
      //  $filter = array('company_id' => $companyIds);
        $troubleDomain = new Domain_Zhianbao_CheckTrouble();
        $troubleIds = $troubleDomain->getTroubleIdsByCompanyIds($companyIds);
        $filter = array('trouble_id' => $troubleIds);
        if(isset($this->title)){
            $filter['title LIKE ?'] = '%'.$this->title.'%';
        }
        $list = $penaltyDomain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $count = $penaltyDomain->getCount($filter);
        $rs['count'] = $count;
        $rs['list'] = $list;
        return $rs;
    }
	
}
