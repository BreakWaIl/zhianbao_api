<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Staff_WeChat_Check extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家政员ID'),
            ),
        );
    }


    /**
     * 家政员微信免登授权检测
     * #desc 用于家政员微信免登授权检测
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //查找用户
        $staffDomain = new Domain_Jiafubao_CompanyHouseStaff();
        $info = $staffDomain->getBaseInfo($this->staffId);
        if (empty($info)) {
            $rs['code'] = 126;
            $rs['msg'] = T('Staff not exists');
            return $rs;
        }

        $data = array(
            'staff_id' => $this->staffId,
            'source' => 'wechat',
        );
        $info = $staffDomain->wechatCheck($data);

        $rs['info'] = $info;

        return $rs;
    }

}

