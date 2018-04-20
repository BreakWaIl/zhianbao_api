<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_AuthRole_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(

            ),
		);
 	}
  
  /**
   * 获取系统权限列表
   * #desc 用于获取系统权限列表
   * #return int code 操作码，0表示成功
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $authRoleDomain = new Domain_Building_UserAuthRole();
        $list = $authRoleDomain->getUserAuthRole();
        $rs['list'] = $list;
        return $rs;
    }
	
}
