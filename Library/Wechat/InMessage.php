<?php

class Wechat_InMessage {

    const MSG_TYPE_TEXT = 'text';
    const MSG_TYPE_EVENT= 'event';
    const MSG_TYPE_IMAGE = 'image';
    const MSG_TYPE_VOICE = 'voice';
    const MSG_TYPE_VIDEO = 'video';
    const MSG_TYPE_LOCATION = 'location';
    const MSG_TYPE_LINK = 'link';
    const MSG_TYPE_DEVICE_EVENT = 'device_event';
    const MSG_TYPE_DEVICE_TEXT = 'device_text';

    const MSG_TYPE_UNKNOW = 'unkonw';

    protected $msgType;
    protected $content;
    protected $fromUserName;
    protected $createTime;
    protected $picUrl;

    protected $wechatInfo;

    protected $postData = array();
    
     protected $isTestCase = false;

    public function __construct()
    {
        if (!isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
            throw new Exception(
            T('miss HTTP_RAW_POST_DATA')
            );
        }

        $appId = ltrim($_GET['appid'], '/');
        if ($appId == 'wx570bc396a51b8ff8') {
            $wechatInfo = array(
			'appid' => 'wx570bc396a51b8ff8',
			'original'=>'gh_3c884a361561'
            //'token' => 'platformtestaccount'
			);
			$this->isTestCase = true;
        } else {
            $domainWechat = new Domain_Zhianbao_Wechat();
            $wechatInfo = $domainWechat->getInfoByAppId($appId);
            // var_dump($wechatInfo);exit;
            if(!empty($wechatInfo)){
                $this->wechatInfo = $wechatInfo;
            }else{
                throw new Exception(
                T('appid is invalid')
                );
            }
            $this->isTestCase = false;
        }

        //接受并解析微信中心POST发送XML数据
        $postData = $GLOBALS['HTTP_RAW_POST_DATA'];
        $postData = $this->decryptMsg($postData);
        $postData = $this->parse($postData);
        //var_dump($postData);exit;

        $this->postData = $postData;

        $this->msgType = isset($postData['MsgType'])
        ? $postData['MsgType'] : self::MSG_TYPE_UNKNOW;

        $this->content = isset($postData['Content'])
        ? $postData['Content'] : '';

        $this->fromUserName = isset($postData['FromUserName'])
        ? $postData['FromUserName'] : '';

        $this->event = isset($postData['Event'])
            ? $postData['Event'] : '';

        $this->createTime = isset($postData['CreateTime'])
        ? $postData['CreateTime'] : $_SERVER['REQUEST_TIME'];
    }
    
    public function isTestCase(){
        
        return $this->isTestCase;
    }

    public function decryptMsg($encryptMsg){
        $timeStamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        $msgSignature = $_GET['msg_signature'];

         
        $mpAppId = DI ()->config->get ( 'app.wechat.mp_appid' );
        $mpToken = DI ()->config->get ( 'app.wechat.mp_token' );
        $mpKey = DI ()->config->get ( 'app.wechat.mp_key' );
        
        $pc = new WechatAuth_WxBizMsgCrypt($mpToken, $mpKey, $mpAppId);
        $xml_tree = new DOMDocument();
        $xml_tree->loadXML($encryptMsg);
        $array_e = $xml_tree->getElementsByTagName('Encrypt');
        $encrypt = $array_e->item(0)->nodeValue;
        $format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
        $from_xml = sprintf($format, $encrypt);

        // 第三方收到公众号平台发送的消息
        $msg = '';
        //$get = $get;

        //var_dump($from_xml);
        $errCode = $pc->decryptMsg($msgSignature, $timeStamp, $nonce, $from_xml, $msg);
        //var_dump($errCode);


        //var_dump($msg);
        return $msg;

    }

    private function parse($message) {
        if (! empty ( $message )) {
            $message = $this->xml2array ( $message );
        }
        return $message;
    }

    private function xml2array($xml) {
        if (empty($xml)) {
            return array();
        }
        $result = array();
        $xmlobj = $this->isimplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        if($xmlobj instanceof SimpleXMLElement) {
            $result = json_decode(json_encode($xmlobj), true);
            if (is_array($result)) {
                return $result;
            } else {
                return '';
            }
        } else {
            return $result;
        }
    }
    private function isimplexml_load_string($string, $class_name = 'SimpleXMLElement', $options = 0, $ns = '', $is_prefix = false) {
        libxml_disable_entity_loader(true);
        if (preg_match('/(\<\!DOCTYPE|\<\!ENTITY)/i', $string)) {
            return false;
        }
        return simplexml_load_string($string, $class_name, $options, $ns, $is_prefix);
    }


    public function __call($method, $params)
    {
        if (substr($method, 0, 3) == 'get') {
            $key = ucfirst(substr($method, 3));
            return isset($this->postData[$key]) ? $this->postData[$key] : null;
        }

        throw new PhalApi_Exception_InternalServerError(
        T("Call undefined method WechatInMessage::$method()")
        );
    }

    public function getMsgType()
    {
        return $this->msgType;
    }

    public function getEvent(){
        return $this->event;
    }
    
    public function getContent()
    {
        return $this->content;
    }

    public function getFromUserName()
    {
        return $this->fromUserName;
    }

    public function getCreateTime()
    {
        return $this->createTime;
    }
    
    /*
     * 获取公众号详情
     */
    public function getWechatInfo(){
        
        return $this->wechatInfo;
    }
}
