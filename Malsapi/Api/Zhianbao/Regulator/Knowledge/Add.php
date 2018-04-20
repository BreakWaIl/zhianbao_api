<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Knowledge_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '监管者ID'),
                'catId' => array('name' => 'cat_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '分类ID'),
                'title' => array('name' => 'title', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '文章标题'),
                'content' => array('name' => 'content', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '文章内容'),
            ),
        );
    }


    /**
     * 添加知识库文章
     * #desc 用于添加知识库文章
     * #return int code 操作码，0表示成功
     * #return int article_id  文章ID
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

        //判断分类是否存在
        $domainCat = new Domain_Zhianbao_KnowledgeCategroy();
        $info = $domainCat->getBaseInfo($this->catId);
        if( !$info) {
            DI()->logger->debug('Categroy not found', $this->catId);

            $rs['code'] = 106;
            $rs['msg'] = T('Categroy not exists');
            return $rs;
        }

        $data = array(
            'regulator_id' => $this->regulatorId,
            'cat_id' => $this->catId,
            'title' => $this->title,
            'content' => $this->content,
            'create_time' => time(),
            'last_modify' => time(),
        );
        $domain = new Domain_Zhianbao_Knowledge();
        $articleId = $domain->addArticle($data);

        $rs['info']['article_id'] = $articleId;

        return $rs;
    }

}
