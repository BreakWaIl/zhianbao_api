<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_CheckTrouble_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
                     'title' => array('name' => 'title','type'=>'string','require'=> false,'desc'=> '事故标题'),
                     'beginTime' => array('name' => 'begin_create_time',  'type' => 'string', 'require' => false, 'desc' => '开始时间'),
                     'endTime' => array('name' => 'end_create_time',  'type' => 'string', 'require' => false, 'desc' => '结束时间'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
   * 获取事故列表
   * #desc 用于获取事故列表
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

        $troubleDomain = new Domain_Zhianbao_CheckTrouble();
        $filter = array('company_id' => $companyIds);
        if(isset($this->title)){
            $filter['title LIKE ?'] = '%'.$this->title.'%';
        }
        if(!empty($this->beginTime) && !empty($this->endTime)){
            $filter['create_time > ?'] = strtotime($this->beginTime);
            $filter['create_time < ?'] = strtotime($this->endTime);
        }
        $list = $troubleDomain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $count = $troubleDomain->getCount($filter);
        $rs['count'] = $count;
        $rs['list'] = $list;
        return $rs;
    }
	
}
