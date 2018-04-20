<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Knowledge_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '监管者ID'),
                     'catId' => array('name' => 'cat_id', 'type' => 'int', 'require' => false, 'desc' => '分类ID'),
                     'title' => array('name' => 'title', 'type' => 'string', 'require' => false, 'desc' => '文章标题'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
     * 获取知识库文章列表
     * #desc 用于获取知识库文章列表
     * #return int code 操作码，0表示成功
     * #return int id 分类ID
     * #return string cat_name 分类名称
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

        $filter = array();
        $filter['regulator_id'] = $this->regulatorId;
        if(!empty($this->catId)){
            $filter['cat_id'] = $this->catId;
        }
        if(!empty($this->title)){
            $filter['title LIKE ?'] = '%'.$this->title.'%';
        }

        $domainCategroy = new Domain_Zhianbao_Knowledge();

        $list = $domainCategroy->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $total = $domainCategroy->getCount($filter);

        $rs['list'] = $list;
        $rs['total'] = $total;

        return $rs;
    }
	
}
