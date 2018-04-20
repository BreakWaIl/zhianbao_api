<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_HiddProject_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '公司ID'),
                     'title' => array('name' => 'title','type'=>'string','require'=> false,'desc'=> '项目名称'),
                     'typeId' => array('name' => 'type_id','type'=>'int','require'=> false,'desc'=> '类型ID'),
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

        $hiddProjectDomain = new Domain_Zhianbao_HiddProject();
        $filter = array('regulator_id' => $this->regulatorId);
        if(isset($this->title)){
            $filter['title LIKE ?'] = '%'.$this->title.'%';
        }
        if(isset($this->typeId)){
            $filter['type_id'] = $this->typeId;
        }
        $list = $hiddProjectDomain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $count = $hiddProjectDomain->getCount($filter);
        $rs['count'] = $count;
        $rs['list'] = $list;
        return $rs;
    }
	
}
