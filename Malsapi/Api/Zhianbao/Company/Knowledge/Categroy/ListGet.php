<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_Knowledge_Categroy_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'catName' => array('name' => 'cat_name', 'type' => 'string', 'require' => false, 'desc' => '分类名称'),
            ),
		);
 	}
  
  /**
     * 获取知识库分类列表
     * #desc 用于获取知识库分类列表
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
        $filter = array();
        $filter['company_id'] = $this->companyId;
//        if(!empty($this->catName)){
//            $filter['cat_name LIKE ?'] = '%'.$this->catName.'%';
//        }

        $domainCategroy = new Domain_Zhianbao_KnowledgeCategroy();
        $list = $domainCategroy->getAllCat($filter,$this->catName);

        $rs['list'] = $list;

        return $rs;
    }
	
}
