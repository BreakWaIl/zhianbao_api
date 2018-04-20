<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Wechat_ReplyRule_InfoGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                'replyRuleId' => array('name' => 'reply_rule_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '回复规则ID'),
            ),
		);
 	}


    /**
     * 获取单个回复规则详情
     * #desc 用于获取回复规则详情
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


        $rs['info'] = $info;

        return $rs;
    }
	
}
