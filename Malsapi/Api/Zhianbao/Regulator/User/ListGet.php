<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_User_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'loginName' => array('name' => 'login_name','type'=>'string','require'=> false,'desc'=> '用户名'),
                     'name' => array('name' => 'name','type'=>'string','require'=> false,'desc'=> '公司名'),
                     'sysType' => array('name' => 'sys_type', 'type' => 'enum', 'range' => array('zab','jfb','zgb','all'), 'require' => true, 'desc' => '系统类型'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
     * 获取监管者列表
   * #desc 用于获取监管者列表
   * #return int code 操作码，0表示成功
   * #return int id  客户id
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $domain = new Domain_Zhianbao_Regulator();
        $filter = array();
        if(isset($this->loginName)){
            $filter['login_name LIKE ?'] = '%'.$this->loginName.'%';
        }
        if(isset($this->name)){
            $filter['name LIKE ?'] = '%'.$this->name.'%';
        }
        if($this->sysType != 'all') {
            $filter['type'] = $this->sysType;
        }
        $list = $domain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);

        $total = $domain->getCount($filter);

        $rs['total'] = $total;
        $rs['list'] = $list;

        return $rs;
    }
	
}
