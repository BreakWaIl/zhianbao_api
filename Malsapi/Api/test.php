<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_test extends PhalApi_Api {

    public function getRules() {
        return array (
				 'Go' => array(

                    ),
        );
    }


    /**
     * 测试
     * #desc 用于测试
     * #return int code 操作码，0表示成功
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
       $mail = new PHPMailer_Lite(true);
        $mail->send('357458089@qq.com','Test title','Test content');
        return $rs;
    }

}

