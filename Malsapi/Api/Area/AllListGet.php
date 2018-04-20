<?php
/**
 * 默认接口服务类
 *
 * @author: Andy 
 */
class Api_Area_AllListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				'Go' => array (

				) 
		);
 	}
	
  
  /**
     * 获取地区列表信息
     * #desc 用于获取地区列表
     * @return int code 操作码，0表示成功
     * @return array list 商户信息列表
     * @return string msg 提示信息
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $domain = new Domain_Area();
        $list =  $domain->getAllAreaList();

        $rs['list'] = $list;
        
        return $rs;
    }
	
}
