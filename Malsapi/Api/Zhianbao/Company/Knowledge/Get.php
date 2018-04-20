<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_Knowledge_Get extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
            ),
		);
 	}
  
  /**
     * 获取首页知识库列表
     * #desc 用于获取首页知识库列表
     * #return int code 操作码，0表示成功
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
        $knowledgeCategroyDomain = new Domain_Zhianbao_KnowledgeCategroy();
        $list = $knowledgeCategroyDomain->getKnowCat($filter);

        $rs['list'] = $list;

        return $rs;
    }
	
}
