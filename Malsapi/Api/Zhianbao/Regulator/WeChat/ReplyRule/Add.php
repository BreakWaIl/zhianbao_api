<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Wechat_ReplyRule_Add extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'wechatId' => array('name' => 'wechat_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公众号ID'),
                     'ruleName' => array('name' => 'rule_name', 'type' => 'string', 'require' => true, 'desc' => '回复规则名称'),
                     'keyWords' => array('name' => 'key_words', 'type' => 'array','format'=>'json', 'require' => true, 'desc' => '关键词 array("key1","key2")'),
                     'module' => array('name' => 'module', 'type' => 'string', 'require' => true, 'desc' => '使用模块'),
                     'returnType' => array('name' => 'return_type', 'type' => 'enum','range'=>array('all','random'), 'require' => true, 'desc' => '回复类型 random:随机返回 all:全部返回'),
                     'contents' => array('name' => 'contents', 'type' => 'array','format'=>'json', 'require' => true, 'desc' => '回复内容 array(array("type"=>"text","content"=>"111"),<br>array("type"=>"news","content"=>array(array("title"=>"Title","description"=>"Description","picurl"=>"PicUrl","url"=>"Url"),<br>array("title"=>"Title","description"=>"Description","picurl"=>"PicUrl","url"=>"Url"))),)'),
            ),
		);
 	}
	
  
  /**
     * 添加自动回复规则
     * #desc 用于添加自动回复规则
     * #return int code 操作码，0表示成功
     * #return int reply_rule_id 回复规则ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //查询公众号
        $domainWechat = new Domain_Zhianbao_Wechat();
        $info = $domainWechat->getBaseInfo($this->wechatId);

        if (empty($info)) {
            DI()->logger->debug('Wechat not exists', $this->wechatId);

            $rs['code'] = 143;
            $rs['msg'] = T('Wechat not exists');
            return $rs;
        }
        $domain = new Domain_Zhianbao_ReplyRule();
        $data = array(
            'wechat_id' => $this->wechatId,
            'name' => $this->ruleName,
            'return_type' => $this->returnType,
            'key_words' => json_encode($this->keyWords),
            'module' => $this->module,
            'contents' => json_encode($this->contents),
        );
        try {
            $ruleId = $domain->addReplyRule($data);
        } catch ( Exception $e ) {
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
            return $rs;
        }

        if(empty($ruleId)){
            $rs['code'] = 144;
            $rs['msg'] = T('Create failed');
            return $rs;
        }
        $rs['reply_rule_id'] = $ruleId;
        return $rs;
    }
	
}
