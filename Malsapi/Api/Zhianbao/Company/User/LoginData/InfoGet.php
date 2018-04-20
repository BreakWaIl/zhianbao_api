<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_User_LoginData_InfoGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'sysType' => array('name' => 'sys_type', 'type' => 'enum', 'range' => array('zab','jfb','zgb'), 'require' => true, 'desc' => '系统类型'),
                     'beginTime' => array('name' => 'begin_time', 'type' => 'int', 'require' => false, 'desc' => '开始时间'),
                     'endTime' => array('name' => 'end_time', 'type' => 'int', 'require' => false, 'desc' => '结束时间'),
                 ),
		);
 	}

  
  /**
     * 获取系统使用情况
     * #desc 用于获取系统使用情况
     * #return int user_id 商户ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //检测是否存在
        $domain = new Domain_Zhianbao_User();
        $data = array(
            'sys_type' => $this->sysType
        );
        if( isset($this->beginTime)){
            $data['begin_time'] = $this->beginTime;
        }else{
            $data['begin_time'] = strtotime(date('Y-m-d')) - 7 * 86400;
        }
        if( isset($this->endTime)){
            $data['end_time'] = $this->endTime;
        }else{
            $data['end_time'] = strtotime(date('Y-m-d'));
        }
        $details = $domain->getSysUseDetails($data);
        $rs['info'] = $details;
        return $rs;
    }

}

