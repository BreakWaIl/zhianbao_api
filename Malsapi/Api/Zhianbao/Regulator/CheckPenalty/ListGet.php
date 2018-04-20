<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_CheckPenalty_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
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
        //检测监管者是否存在
        $regulatorDomain = new Domain_Zhianbao_Regulator();
        $regulatorInfo = $regulatorDomain->getBaseInfo($this->regulatorId);
        if(! $regulatorInfo){
            $rs['code'] = 118;
            $rs['msg'] = T('Regulator not exists');
            return $rs;
        }
        $companyIds = $regulatorDomain->getCompanyIds($this->regulatorId);
        $penaltyDomain = new Domain_Zhianbao_CheckPenalty();
      //  $filter = array('company_id' => $companyIds);
        $troubleDomain = new Domain_Zhianbao_CheckTrouble();
        $troubleIds = $troubleDomain->getTroubleIdsByCompanyIds($companyIds);
        $filter = array('trouble_id' => $troubleIds);
        $list = $penaltyDomain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $count = $penaltyDomain->getCount($filter);
        $rs['count'] = $count;
        $rs['list'] = $list;
        return $rs;
    }
	
}
