<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Knowledge_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '监管者ID'),
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
        //检测监管者是否存在
        $regulatorDomain = new Domain_Zhianbao_Regulator();
        $regulatorInfo = $regulatorDomain->getBaseInfo($this->regulatorId);
        if(! $regulatorInfo){
            $rs['code'] = 118;
            $rs['msg'] = T('Regulator not exists');
            return $rs;
        }

        //判断文章是否存在
        $domain = new Domain_Zhianbao_Knowledge ();
        $info = $domain->getBaseInfo($this->articleId);
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
