<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Wechat_ReplyRule_Content_Add extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'replyRuleId' => array('name' => 'reply_rule_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '回复规则ID'),
                     'replyRuleContent' => array('name' => 'reply_rule_content', 'type' => 'array','format'=>'json', 'require' => true, 'desc' => '回复内容 array("type"=>"text","content"=>"111")'),
            ),
		);
 	}
	
  
  /**
     * 添加回复规则内容
     * #desc 用于添加回复规则内容
     * #return int code 操作码，0表示成功
     * #return int reply_rule_id 回复规则ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //查找回复规则
        $domain = new Domain_Zhianbao_ReplyRule();
        $info = $domain->getBaseInfo($this->replyRuleId);

        if (empty($info)) {
            DI()->logger->debug('Reply rule not found', $this->replyRuleId);

            $rs['code'] = 146;
            $rs['msg'] = T('Reply rule not found');
            return $rs;
        }

        $domain->addReplyRuleContent($info,$this->replyRuleContent);
        
        $rs['info']['reply_rule_id'] = $this->replyRuleId;

        return $rs;
    }
	
}
