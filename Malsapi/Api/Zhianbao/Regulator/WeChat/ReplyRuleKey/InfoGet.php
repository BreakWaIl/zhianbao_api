<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Wechat_ReplyRuleKey_InfoGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                'replyRuleId' => array('name' => 'reply_rule_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '回复规则ID'),
                'keyWordId' => array('name' => 'key_word_id', 'type' => 'int', 'require' => true, 'desc' => '关键词Id'),
            ),
		);
 	}


    /**
     * 获取单个回复规则关键词
     * #desc 用于获取单个回复规则关键词
     * #return int code 操作码，0表示成功
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $domain = new Domain_Zhianbao_ReplyRule();
        $info = $domain->getBaseInfo($this->replyRuleId);

        if (empty($info)) {
            DI()->logger->debug('Reply rule not found', $this->replyRuleId);

            $rs['code'] = 146;
            $rs['msg'] = T('Reply rule not found');
            return $rs;
        }

        $keyWords = json_decode($info['key_words'],true);
        $rs['key_word'] = $keyWords[$this->keyWordId];

        return $rs;
    }
	
}
