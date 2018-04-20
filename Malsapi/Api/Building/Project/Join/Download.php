<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Project_Join_Download extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'newName' => array('name' => 'new_name', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '新名字'),
                     'imgId' => array('name' => 'img_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '图片ID'),
                ),
		);
 	}

  
  /**
     * 下载员工二维码
     * #desc 用于下载员工二维码
     * #return int status 状态 0 成功 1 失败
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $projectDomain = new Domain_Building_Project();

        $info = $projectDomain->download( $this->newName, $this->imgId);
        $rs['info'] = $info;

        return $rs;
    }

}

