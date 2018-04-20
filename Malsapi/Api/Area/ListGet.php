<?php
/**
 * 默认接口服务类
 *
 * @author: Andy 
 */
class Api_Area_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				'Go' => array (
						//'filter' =>  array('name' => 'filter', 'type' => 'string',  'require' => true, 'desc' => ''),
						'parentId' => array('name' => 'parent_id', 'type' => 'int', 'min' => 0, 'default'=>0,'require' => true, 'desc' => '父类ID'),
						'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
				      'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
				      //'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
				) 
		);
 	}
	
  
  /**
     * 获取地区列表信息
     * #desc 用于获取地区列表
     * @return int code 操作码，0表示成功
     * @return array list 商户信息列表
     * @return string msg 提示信息
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $domain = new Domain_Area();
        $list =  $domain->getAreaList($this->parentId);

        $rs['list'] = $list;
        
        return $rs;
    }
	
}
