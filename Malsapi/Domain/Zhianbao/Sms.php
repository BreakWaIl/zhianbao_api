<?php
class Domain_Zhianbao_Sms {
    var $model;
    var $smsVaildTime = 900;//默认有效期15分钟

    //生成验证码
    public function sendCode($mobile){
        $smsCode = new Model_Zhianbao_SmsCheckCode ();
        $data = array (
            'mobile' => $mobile,
            'code' => rand ( 100000, 999999 ),
            'status' => 'n' ,
            'create_time' => time (),
        );
        $has = $smsCode->getByWhere(array('mobile'=>$mobile));
        if($has) {
            $id = $smsCode->updateByWhere(array('mobile'=>$mobile),$data);
        }else{
            $id = $smsCode->insert($data);
        }
        if (! $id) {
            throw new LogicException ( T ( 'Create failed' ), 144 );
        }else{
            $content = '【喵神科技】您的验证码：'.$data['code'].'，在5分钟有效。如非本人操作请忽略本短信！退订回N';
            $this->sendSms('',$mobile, $content, '6',0,0);
        }
        return $id;
    }
    //地阳短信
    public function sendSms($nationCode, $mobile, $content, $productCode,$userId,$shopId) {
        switch ($productCode){
            case '1' : $user = DI ()->config->get ( 'app.sms.diyang_sms_wl_user' ); $pwd = DI ()->config->get ( 'app.sms.diyang_sms_wl_pwd' );break;
            case '2' : $user = DI ()->config->get ( 'app.sms.diyang_sms_yx_user' ); $pwd = DI ()->config->get ( 'app.sms.diyang_sms_yx_pwd' );break;
            case '6' : $user = DI ()->config->get ( 'app.sms.diyang_sms_yzm_user' ); $pwd = DI ()->config->get ( 'app.sms.diyang_sms_yzm_pwd' );break;
        }
        $sender = new SMS_DIYANG_SmsSender ( $user, $pwd, $productCode);
        $msg = '';

        if(DI()->config->get('sys.debug')){
            $result = '1|1';
        }else{
            $result = $sender->sendSms ( $nationCode, $mobile, $content, $msg );
        }
        $sended = explode("|",$result);
        $send_num = $sended[0];
        $result = strip_tags($result);
        $errormsg = '';
        switch ($result){
            case '-1' : $errormsg = '余额为空';break;
            case '-2' : $errormsg = '余额不足';break;
            case '-3' : $errormsg = '产品关闭，不能提交';break;
            case '-4' : $errormsg = '用户发送限制,不能发送';break;
            case '-5' : $errormsg = '用户发送的号码条数低于发送下限,不能发送';break;
            case '-6' : $errormsg = '内容包含敏感词!';break;
            case '-7' : $errormsg = '该产品需要带签名，请加上签名!';break;
            case '-8' : $errormsg = '数据库异常';break;
            case '-9' : $errormsg = '账号或密码不存在';break;
            case '-10' : $errormsg = '号码为空';break;
            case '-11' : $errormsg = '内容为空';break;
            case '-12' : $errormsg = '产品不存在或已停用';break;
            case '-13' : $errormsg = '未分配的扩展号';break;
            case '-14' : $errormsg = '该内容不允许发送';break;
            case '-15' : $errormsg = '彩信主题长度超过上限!';break;
            case '-16' : $errormsg = '内容长度超过上限!';break;
            case '-17' : $errormsg = '网关余额不足！';break;
            case '-18' : $errormsg = '内容签名与报备签名不一致！';break;
            case '-19' : $errormsg = '彩信格式不正确';break;
            case '-20' : $errormsg = '用户发送的产品已经超出每日发送限额,不能发送';break;
            case '-21' : $errormsg = '产品发送有时间限制无法提交';break;
        }
        if($errormsg == ''){
            //更新发送日志
            $domainSmsLog = new Domain_Zhianbao_SmsSendLog();
            $domainSmsLog->addSendLog($userId,$shopId,$mobile,$content);
            //更新发送日志

            return $result;
        }else{
            throw new LogicException ( T ( 'SMS failure' ) . ',' . $errormsg, 135 );
        }

    }


    //验证手机验证码

    function checkCode($mobile,$smsCode){
        $filter = array(
                'mobile' => $mobile,
                'code' => $smsCode,
                'status'=>'n' );
        $smsCode = new Model_Zhianbao_SmsCheckCode ();
        $check = $smsCode->getByWhere($filter);

        if($check){
            if($check['create_time'] > (time()-$this->smsVaildTime)){
                $data = array (
                    'status' => 'y',
                );
                $smsCode->updateByWhere ( $filter, $data );
                return true;
            }else{
                throw new LogicException ( T ( 'Sms code error' ), 159 );
            }
        }else{
            throw new LogicException ( T ( 'Sms code error' ), 159 );
        }
    }

    function checkShenpuCode($mobile,$smsCode){
        $params= array(
            'mobile' => $mobile,
            'code' => $smsCode,
        );

        $result = malsapi_request('Shenpu_Sms_CheckCode.Go',$params);
        $result = $result['data']['info'];
        return $result;

    }

}
