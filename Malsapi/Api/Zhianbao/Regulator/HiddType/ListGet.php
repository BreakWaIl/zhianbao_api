<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_HiddType_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '公司ID'),
                     'name' => array('name' => 'name','type'=>'string','require'=> false,'desc'=> '类型名称'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
   * 获取隐患类型列表
   * #desc 用于获取隐患类型列表
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

        $hiddTypeDomain = new Domain_Zhianbao_HiddType();
        $filter = array('regulator_id' => $this->regulatorId);
        if(isset($this->name)){
            $filter['name LIKE ?'] = '%'.$this->name.'%';
        }
        $list = $hiddTypeDomain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $count = $hiddTypeDomain->getCount($filter);
        $rs['count'] = $count;
        $rs['list'] = $list;
        return $rs;
    }
	
}
