<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Yuyue_Update extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'yuyueId' => array('name' => 'yuyue_id', 'type' => 'int', 'min' => 1,'require' => true, 'desc' => '预约ID'),
                'mobile' => array('name' => 'mobile', 'type' => 'string', 'require' => true, 'desc' => '预约人手机'),
                'jzMobile' => array('name' => 'jz_mobile', 'type' => 'string', 'require' => true, 'desc' => '家政公司手机'),
                'content' => array('name' => 'content', 'type' => 'array','format'=>'json', 'require' => true, 'desc' => '内容'),
            ),
        );
    }


    /**
     * 更新预约
     * #desc 用于更新预约
     * #return int code 操作码，0表示成功
     * #return int id 预约ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $domain = new Domain_Jiafubao_Yuyue();
        $content = json_encode($this->content);
        $data = array(
            'mobile' => $this->mobile,
            'jz_mobile' => $this->jzMobile,
            'content' => $content,
            'last_modify' => time()
        );
        $id = $domain->update($this->yuyueId,$data);
        $rs['info']['id'] = $id;
        return $rs;
    }

}
