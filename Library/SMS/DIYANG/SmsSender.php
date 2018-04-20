<?php
class SMS_DIYANG_SmsSender {

    function __construct($user, $pwd, $productCode) {
        // url 需要根据我们说明文档上适时调整
        $this->url = "http://api.79yt.cn:8080/default.asmx/SendSms";
        $this->user = $user;
        $this->pwd = $pwd;
        $this->productCode = $productCode;
    }

    function sendsms($nationCode, $mobile, $content, & $msg){
        $post_data['username'] = $this->user;
        $post_data['password'] = $this->pwd;
        $post_data['productid'] = $this->productCode;
        $post_data['phonelist'] = $mobile;
        $post_data['msg'] = $content;
        $post_data['longnum'] = '';
        $url = $this->url;
        $o="";
        foreach ($post_data as $k=>$v)
        {
            $o.= "$k=".urlencode($v)."&";
        }
        $post_data=substr($o,0,-1);
        $ch = curl_init();
        $this_header=array("content-type: application/x-www-form-urlencoded; charset=UTF-8");
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_HEADER,$this_header);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $result = curl_exec($ch);
        curl_close($ch); // 关闭CURL会话
        return $result;
    }
}

