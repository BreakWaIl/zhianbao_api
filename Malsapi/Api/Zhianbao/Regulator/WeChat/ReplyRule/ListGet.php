<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Wechat_ReplyRule_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'wechatId' => array('name' => 'wechat_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公众号ID'),
                     'module' => array('name' => 'module', 'type' => 'string',  'require' => false, 'desc' => '模块名'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
	
  
  /**
     * 获取回复规则列表
     * #desc 用于获取回复规则列表
     * #return int code 操作码，0表示成功
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $domainWechat = new Domain_Zhianbao_Wechat();
        $wechatInfo = $domainWechat->getBaseInfo($this->wechatId);
        if (empty($wechatInfo)) {
            DI()->logger->debug('Wechat not exists', $this->wechatId);

            $rs['code'] = 143;
            $rs['msg'] = T('Wechat not exists');
            return $rs;
        }
        $domain = new Domain_Zhianbao_ReplyRule();
        $filter = array(
            'wechat_id' => $this->wechatId,
        );
        if(isset($this->module)){
            $filter ['module'] = $this->module;
        }
        $list = $domain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $count = $domain->getCount($filter);
        $rs['list'] = $list;
        $rs['count'] = $count;

        return $rs;
    }
	
}
