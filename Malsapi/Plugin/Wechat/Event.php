<?php
class Plugin_Wechat_Event implements Wechat_Plugin_Event {

    public function process($inMessage, &$outMessage) {
        $domain = new Domain_Shenpu_WxFans();
        $wechatInfo = $inMessage->getWechatInfo();
        $event = $inMessage->getEvent();
        $domainShopKV = new Domain_Shenpu_ShopKvStore($wechatInfo['shop_id'], $wechatInfo['user_id']);
        $content = $domainShopKV->get('AutoReply');
        $content = json_decode($content['shop_value'],true);
        $fromUserName = $inMessage->getFromUserName();
        if( $content ) {
            if ($event == 'subscribe') {
                //处理粉丝信息 这里只存openid
                $fansInfo = $domain->getBaseInfoByOpenId($fromUserName);
                if($fansInfo){
                    $data = array('subscribe'=>1,'subscribe_time'=>time(),'update_time'=>time());
                    $domain->updateWxFans(array('openid'=>$fromUserName),$data);
                }else{
                    $data = array('shop_id'=>$wechatInfo['shop_id'],'wechat_id'=>$wechatInfo['id'],'openid'=>$fromUserName,'subscribe'=>1,'subscribe_time'=>time(),'update_time'=>time());
                    $domain->addWxFans($data);
                }
                //处理回复信息
                if($content['type'] == 'text') {
                    $outMessage = new Wechat_OutMessage_Text();
                    $outMessage->setContent($content['content']);
                }
                if($content['type'] == 'news'){
                    $outMessage = new Wechat_OutMessage_News();
                    foreach ($content['content'] as $key => $value){
                        $news_item = new Wechat_OutMessage_News_Item();
                        $news_item->setTitle($value['title']);
                        $news_item->setDescription($value['description']);
                        $news_item->setPicUrl($value['picurl']);
                        $news_item->setUrl($value['url']);
                        $outMessage->addItem($news_item);
                    }
                }
            }
            if ($event == 'unsubscribe') {
                $domain->updateWxFans(array('openid'=>$fromUserName),array('subscribe'=>0,'update_time'=>time()));
            }
        }
    }
}
