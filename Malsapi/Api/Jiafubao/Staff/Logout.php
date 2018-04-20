<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Staff_Logout extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'staffId' => array('name' => 'staff_id', 'type' => 'string', 'require' => true, 'desc' => '家政员ID'),
            ),
        );
    }


    /**
     * 家政员退出登录
     * #desc 用于微信家政员退出登录
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //查找用户
        $staffDomain = new Domain_Jiafubao_HouseStaff();
        $info = $staffDomain->getBaseInfo($this->staffId);
        if (empty($info)) {
            $rs['code'] = 126;
            $rs['msg'] = T('Staff not exists');
            return $rs;
        }
        $info = $staffDomain->logout($this->staffId);
        if($info) {
            $status = 0 ;
        }else{
            $status = 1;
        }
        $rs['status'] = $status;
        return $rs;
    }

}

