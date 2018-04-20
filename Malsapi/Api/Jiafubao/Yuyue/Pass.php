<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Yuyue_Pass extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'yuyueId' => array('name' => 'yuyue_id', 'type' => 'int', 'min' => 1,'require' => true, 'desc' => '预约ID'),
            ),
        );
    }


    /**
     * 通过预约
     * #desc 用于通过预约
     * #return int code 操作码，0表示成功
     * #return int id 预约ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $domain = new Domain_Jiafubao_Yuyue();
        $data = array('status' => 'y','last_modify' => time());
        $status = $domain->update($this->yuyueId,$data);
        $rs['info']['status'] = $status;
        return $rs;
    }

}
