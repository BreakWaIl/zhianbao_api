<?php
class Plugin_Wechat_Text implements Wechat_Plugin_Text {

    public function process($inMessage, &$outMessage) {
        $wechatInfo = $inMessage->getWechatInfo();
        $keyWord = $inMessage->getContent();
//        $openId = $inMessage->getFromUserName();
//        $shopId = $wechatInfo['shop_id'];
//        $userId = $wechatInfo['user_id'];
//        $domainShopKV = new Domain_Shenpu_ShopKvStore($shopId,$userId);
//        $intoSearchKey = $domainShopKV->get('searchGoods');
//        if($intoSearchKey){
//            //存在搜索商品关键字
//            $searching = $domainShopKV->get('searchGoods_'.$openId);
//
//            if(isset($searching['shop_value'])){
//            //正在搜索
//                $outMessage = $this->searchGoods($shopId,$userId,$openId,$keyWord);
//            }else{
//                //判断是否要进入商品搜索
//                if($intoSearchKey['shop_value'] == $keyWord){
//                    //进入商品搜索
//                    $outMessage = $this->intoSearch($shopId,$userId,$openId);
//                }else {
//                    //存在商品搜索，但是没有正在搜索,进行普通关键字搜索
//                    $outMessage = $this->keyWord($wechatInfo['id'], $keyWord);
//                }
//            }
//        }else{
            //不存在搜索商品关键字
            $outMessage = $this->keyWord($wechatInfo['id'],$keyWord);
//        }
    }
    //进入商品搜索
    public function intoSearch($shopId,$userId,$openId){
        $domainShopKV = new Domain_Shenpu_ShopKvStore($shopId,$userId);
        $domainShopKV->set('searchGoods_'.$openId,'1');
        $outMessage = new Wechat_OutMessage_Text();
        $outMessage->setContent('请输入内容来搜索商品。(退出搜索请输入"q")');
        return $outMessage;
    }
    //进行商品搜索
    public function searchGoods($shopId,$userId,$openId,$keyWord){
        $domainShopKV = new Domain_Shenpu_ShopKvStore($shopId,$userId);
        if($keyWord == 'q'){
            //退出搜索模式
            $domainShopKV->delete('searchGoods_'.$openId);
            $outMessage = new Wechat_OutMessage_Text();
            $outMessage->setContent('退出搜索商品模式成功！');
        }else {
            //搜索商品
            $GoodsDomain = new Domain_Shenpu_Goods();
            $content = $GoodsDomain->searchGoods($shopId, $keyWord);
            if (!$content) {
                $outMessage = new Wechat_OutMessage_Text();
                $outMessage->setContent('关键词：' . $keyWord . ' 没有搜索到商品！');
            } else {
                //输出搜索到的商品
                $outMessage = new Wechat_OutMessage_News();
               
                $i = 0;
                foreach ($content as $key => $value) {
                    if ($i > 5) {
                        break;
                    }
                    $news_item = new Wechat_OutMessage_News_Item();
                    $news_item->setTitle($value['title']);
                    $news_item->setDescription($value['description']);
                    $news_item->setPicUrl($value['picurl']);
                    $news_item->setUrl($value['url']);
                    $outMessage->addItem($news_item);
                    $i++;
                }
            }
        }
        return $outMessage;
    }
    //普通关键字
    public function keyWord($wechatId,$keyWord)
    {
        //普通关键词
        $domain = new Domain_Zhianbao_ReplyRule();
        $content = $domain->responseKeyWord($wechatId, $keyWord);
        if ($content) {
            if ($content['type'] == 'text') {
                $outMessage = new Wechat_OutMessage_Text();
                $outMessage->setContent($content['content']);
            }
            if ($content['type'] == 'news') {
                $outMessage = new Wechat_OutMessage_News();
               
                $i = 0;
                foreach ($content['content'] as $key => $value) {
                    if ($i > 5) {
                        break;
                    }
                    $news_item = new Wechat_OutMessage_News_Item();
                    $news_item->setTitle($value['title']);
                    $news_item->setDescription($value['description']);
                    $news_item->setPicUrl($value['picurl']);
                    $news_item->setUrl($value['url']);
                    $outMessage->addItem($news_item);
                    $i++;
                }
            }
            return $outMessage;
        }
    }
}
