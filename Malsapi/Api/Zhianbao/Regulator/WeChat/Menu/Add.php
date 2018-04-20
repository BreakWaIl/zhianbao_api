<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_WeChat_Menu_Add extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'wechatId' => array('name' => 'wechat_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公众号ID'),
                     'menuContent' => array('name' => 'menu_content', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '菜单内容'),
            ),
		);
 	}
	
  
  /**
     * 获取微信自定义菜单
     * #desc 用于获取自定义菜单
     * #return int code 操作码，0表示成功， 1表示添加失败
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
        //获取自定义菜单
        $appid = $appId = DI ()->config->get ( 'app.wechat.mp_appid' );
        $addRs = $domainWechat->addWechatMenu($appid,$this->wechatId,$this->menuContent,$info['regulator_id']);
        if(! $addRs){
            DI()->logger->debug('Create failed', $this->wechatId);

            $rs['code'] = 144;
            $rs['msg'] = T('Create failed');
            return $rs;

        }
        return $rs;
    }
}
