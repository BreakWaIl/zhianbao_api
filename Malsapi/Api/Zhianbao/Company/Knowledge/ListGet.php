<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_Knowledge_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
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
        //判断公司是否存在
        $domainCompany = new Domain_Zhianbao_Company();
        $companyInfo = $domainCompany->getBaseInfo($this->companyId);
        if (empty($companyInfo)) {
            DI()->logger->debug('Company not exists', $this->companyId);

            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }
        $knowledgeCategroyDomain = new Domain_Zhianbao_KnowledgeCategroy();
        $regulatorId = $knowledgeCategroyDomain->getRegulator($this->companyId);
        $filter = array();
        $filter['regulator_id'] = $regulatorId['regulator_id'];
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
