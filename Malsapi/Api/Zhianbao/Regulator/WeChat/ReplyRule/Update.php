<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Wechat_ReplyRule_Update extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'replyRuleId' => array('name' => 'reply_rule_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '回复规则ID'),
                     'ruleName' => array('name' => 'rule_name', 'type' => 'string', 'require' => true, 'desc' => '回复规则名称'),
                     'returnType' => array('name' => 'return_type', 'type' => 'enum','range' => array('all','random'), 'require' => true, 'desc' => '回复类型'),
            ),
		);
 	}
	
  
  /**
     * 更新自动回复规则
     * #desc 用于更新自动回复规则
     * #return int code 操作码，0表示成功
     * #return int reply_rule_id 回复规则ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $domain = new Domain_Zhianbao_ReplyRule();
        $data = array(
            'id' => $this->replyRuleId,
            'name' => $this->ruleName,
            'return_type'=>$this->returnType,
        );
        $result = $domain->updateReplyRule($data);
        if(! $result){
            $rs['code'] = 108;
            $rs['msg'] = T('Update failed');
            return $rs;
        }
        $rs['reply_rule_id'] = $this->replyRuleId;
        return $rs;
    }
	
}
