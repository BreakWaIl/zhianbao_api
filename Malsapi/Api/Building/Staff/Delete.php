<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Staff_Delete222 extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '员工ID'),
            ),
		);
 	}
	
  
  /**
     * 删除建筑员工
     * #desc 用于删除当前建筑员工
     * #return int code 操作码，0表示成功
     * #return int status 状态 0 成功, 1 失败
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        //判断员工是否存在
        $staffDomain = new Domain_Building_Staff();
        $staffInfo = $staffDomain->getBaseInfo($this->staffId);
        if( !$staffInfo) {
            $rs['code'] = 126;
            $rs['msg'] = T('Staff not exists');
            return $rs;
        }

       // $res = $staffDomain->delStaff($this->staffId);
        $res = 0;
        if( $res){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info'] = $status;

        return $rs;
    }
	
}
