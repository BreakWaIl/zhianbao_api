<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_UrgentPlan_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
                     'isRepeal' => array('name' => 'is_repeal',  'type' => 'enum', 'range'=>array('y','n','all'), 'require' => true, 'desc' => '是否作废:y 已作废 n 未作废 all 全部'),
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
        //检测监管者是否存在
        $regulatorDomain = new Domain_Zhianbao_Regulator();
        $regulatorInfo = $regulatorDomain->getBaseInfo($this->regulatorId);
        if(! $regulatorInfo){
            $rs['code'] = 118;
            $rs['msg'] = T('Regulator not exists');
            return $rs;
        }
        $companyIds = $regulatorDomain->getCompanyIds($this->regulatorId);

        $planDomain = new Domain_Zhianbao_UrgentPlan();
        $filter = array('company_id' => $companyIds);
        if($this->isRepeal == 'y'){
            $filter['is_repeal'] = 'y';
        }
        if($this->isRepeal == 'n'){
            $filter['is_repeal'] = 'n';
        }
        $list = $planDomain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $total = $planDomain->getCount($filter);

        $rs['count'] = $total;
        $rs['list'] = $list;
        return $rs;
    }
	
}
