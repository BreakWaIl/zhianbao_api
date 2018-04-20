<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Customer_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'name' => array('name' => 'name', 'type' => 'string', 'require' => false, 'desc' => '客户名称'),
                     'searchMobile' => array('name' => 'search_mobile', 'type' => 'string', 'require' => false, 'desc' => '手机号码'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
     * 获取客户列表
   * #desc 用于获取当前店铺中的客户列表
   * #return int code 操作码，0表示成功
   * #return int id  客户id
   * #return string login_name 客户名称
   * #return string realname 真实姓名
   * #return string mobile 手机号码
   * #return string source 客户来源
   * #return int create_time 注册时间
   * #return int last_modify 最后更新时间
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $filter = array();
        if(!empty($this->name)){
            $filter['name LIKE ?'] = $this->logName . '%';
        }
        if(!empty($this->searchMobile)){
            $filter['mobile LIKE ?'] = '%' . $this->searchMobile . '%';
        }

        $domain = new Domain_Jiafubao_Customer();
        $list = $domain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        foreach ($list as $key => $value){
            unset($list[$key]['login_pwd']);
            unset($list[$key]['salt']);
        }
        $total = $domain->getCount($filter);

        $rs['total'] = $total;
        $rs['list'] = $list;

        return $rs;
    }
	
}
