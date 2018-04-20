<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_Staff_InfoGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'staffId' => array('name' => 'staff_id','type'=>'int','require'=> true,'desc'=> '人员ID'),
            ),
		);
 	}

  
  /**
     * 获取员工信息详情
     * #desc 用于获取员工信息详情
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //查看员工是否存在
        $staffDomain = new Domain_Zhianbao_Staff();
        $staffInfo = $staffDomain->getBaseInfo($this->staffId);
        if(! $staffInfo){
            $rs['code'] = 126;
            $rs['msg'] = T('Staff not exists');
            return $rs;
        }

        $rs['info'] = $staffInfo;
        return $rs;
    }

}

