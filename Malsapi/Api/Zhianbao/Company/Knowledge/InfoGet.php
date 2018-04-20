<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_Knowledge_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'articleId' => array('name' => 'article_id', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '文章ID'),
            ),
        );
    }
  
  /**
     * 获取知识库文章详情
     * #desc 用于获取当前知识库文章详情
     * #return int code 操作码，0表示成功
     * #return int id 文章ID
     * #return int company_id 公司ID
     * #return int cat_id 分类ID
     * #return string title 文章标题
     * #return string content 文章内容
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
        //判断文章是否存在
        $domain = new Domain_Zhianbao_Knowledge ();
        $info = $domain->getCompany($this->articleId,$regulatorId);
        if( !$info) {
            DI()->logger->debug('Article not found', $this->articleId);

            $rs['code'] = 108;
            $rs['msg'] = T('Article not exists');
            return $rs;
        }

        $rs['info'] = $info;

        return $rs;
    }
    
}
