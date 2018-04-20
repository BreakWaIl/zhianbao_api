<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Knowledge_Delete extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '监管者ID'),
                     'articleId' => array('name' => 'article_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '文章ID'),
            ),
		);
 	}
	
  
  /**
     * 删除知识库文章
     * #desc 用于删除当前知识库文章
     * #return int code 操作码，0表示成功
     * #return int status 状态 0 成功, 1 失败
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

        $res = $domain->deleteArticle($this->articleId);
        if( $res){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info'] = $status;

        return $rs;
    }
	
}
