<?php
class Domain_Zhianbao_Wechat {
    public function getBaseInfo($userWechatId, $cols = '*') {
        $rs = array ();

        $userWechatId = intval ( $userWechatId );
        if ($userWechatId <= 0) {
            return $rs;
        }

        // 版本1：简单的获取
        $model = new Model_Zhianbao_Wechat ();
        $rs = $model->get ( $userWechatId, $cols );

        if (! $rs)
        return false;

        if (isset ( $rs ['create_time'] )) {
            $rs ['create_time'] = date ( 'Y-m-d H:i:s', $rs ['create_time'] );
        }

        if (isset ( $rs ['update_time'] )) {
            $rs ['update_time'] = date ( 'Y-m-d H:i:s', $rs ['update_time'] );
        }

        return $rs;
    }
    public function getWechatList($filter){
        $model = new Model_Zhianbao_Wechat ();
        $rs = $model->getAll('*',$filter);
        return $rs;
    }
    public function getByRegulatorId($regulatorId,$cols = '*'){
        $model = new Model_Zhianbao_Wechat ();
        $rs = $model->getByWhere( array('regulator_id'=>$regulatorId), $cols );
        if (! $rs)
        return false;
        return $rs;
    }
    public function getInfoByAppId($appId, $cols = '*') {
        $rs = array ();

        // 版本1：简单的获取
        $model = new Model_Zhianbao_Wechat ();
        $rs = $model->getByWhere( array('appid'=>$appId), $cols );

        if (! $rs)
        return false;

        if (isset ( $rs ['create_time'] )) {
            $rs ['create_time'] = date ( 'Y-m-d H:i:s', $rs ['create_time'] );
        }

        if (isset ( $rs ['update_time'] )) {
            $rs ['update_time'] = date ( 'Y-m-d H:i:s', $rs ['update_time'] );
        }

        return $rs;
    }

    /**
     * 
     * 根据公众号appid获取绑定的公众号数据
     * 
     * 同一个公众号会存在多条wechat 记录
     * 
     * @param unknown_type $appId
     * @param unknown_type $cols
     */
    public function getBindWechatByAppId($appId, $cols = '*') {
        $rs = array ();

        // 版本1：简单的获取
        $model = new Model_Zhianbao_Wechat ();
        $rs = $model->getByWhere( array('appid'=>$appId,'is_bind'=>'y'), $cols );

        if (! $rs)
        return false;

        if (isset ( $rs ['create_time'] )) {
            $rs ['create_time'] = date ( 'Y-m-d H:i:s', $rs ['create_time'] );
        }

        if (isset ( $rs ['update_time'] )) {
            $rs ['update_time'] = date ( 'Y-m-d H:i:s', $rs ['update_time'] );
        }

        return $rs;
    }

    public function getAccessToken($appId,$wechatId) {
        $info = $this->getBaseInfo ( $wechatId, 'access_token,appid,appsecret,access_token_utime,is_auth' );

        if (empty ( $info ) ) {
            throw new LogicException ( T ( 'Wechat does not exist' ), 108 );
        }

        //一键授权
        if($info['is_auth'] == 'y'){
            $domainWechatApp = new Domain_Zhianbao_WechatApp ();
            return $domainWechatApp->getAuthorizerAccessToken($appId,$wechatId);
        }
        $accessToken = ! empty ( $info ['access_token'] ) ? json_decode ( $info ['access_token'], true ) : array ();
        $appid = $info ['appid'];
        $appsecret = $info ['appsecret'];
        $accessTokenUTime = $info ['access_token_utime'];
        if (empty ( $appid ) || empty ( $appsecret )) {
            throw new LogicException ( T ( 'Wechat are missing appid or appsecret' ), 122 );
        }
        $flag = false;
        if (! empty ( $accessToken )) {
            // 提前15分钟刷新,如果最后次刷新时间减去过期时间段加上提前15分钟 大于当前时间，应该刷新token
            $need_flush_time = $accessTokenUTime + $accessToken ['expires_in'] - 900;
            if ($need_flush_time <= time ()) {
                $flag = true;
            }
        } else {
            $flag = true;
        }
        if ($flag) {
            $curl = new PhalApi_CUrl ();
            $url = sprintf ( DI ()->config->get ( 'app.wechat.get_access_token_url' ), $appid, $appsecret );
            $accessToken = $curl->get ( $url );
            $accessToken = !empty ( $accessToken ) ? json_decode($accessToken,true) : '';
            if (empty ( $accessToken ) || !isset ( $accessToken ['access_token'] )) {
                throw new LogicException ( T ( 'Request WeChat token failed' ), 123 );
            }
            	
            $model = new Model_Zhianbao_Wechat ();
            $data = array (
					'access_token' => json_encode ( $accessToken ),
					'access_token_utime' => time () 
            );
            if (! $model->update ( $wechatId, $data )) {
                $rs ['code'] = 105;
                $rs ['msg'] = T ( 'Update failed' );
                return $rs;
            }
        }

        return $accessToken['access_token'];
    }
    public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = '') {
        $model = new Model_Zhianbao_Wechat ();


        $rs = $model->getAll ( '*', $filter, $page, $page_size, $orderby );

        return $rs;
    }
    public function deleteWechat($wechatInfo) {
        $userWechatId = $wechatInfo['id'];
        // 		$data = array (
        // 				'update_time' => time ()
        // 		);
        // 		$model = new Model_Zhianbao_Wechat ();
        // 		$rs = $model->update ( $userWechatId, $data );


        //添加到删除表里
        $this->addWechatDeleteInfo($wechatInfo);

        //删除公众号数据
        $model = new Model_Zhianbao_Wechat ();
        $model->delete($userWechatId);

        $domainWechatApp = new Domain_Malswx_WechatApp ();
        $domainWechatApp->clearAuth($userWechatId);

        return true;
    }
    // 	public function getByWechatId($userWechatId) {
    // 		$model = new Model_Zhianbao_Wechat ();
    // 		$rs = $model->getByWechatId ( $userWechatId );

    // 		return $rs;
    // 	}
    public function updateWechatInfo($userWechat) {
        $model = new Model_Zhianbao_Wechat ();
        $id = $userWechat ['id'];
        unset ( $userWechat ['id'] );

        $rs = $model->update ( $id, $userWechat );

        return $rs;
    }
    public function bindWechat($wechatId) {
        $model = new Model_Zhianbao_Wechat ();
        $data = array (
				'is_bind' => 'y' 
				);
				$rs = $model->update ( $wechatId, $data );

				return $rs;
    }
    public function getAllWechat() {
        $model = new Model_Zhianbao_Wechat ();
        $rs = $model->getAll ();

        return $rs;
    }
    public function addWechatInfo($userWechat) {
        $model = new Model_Zhianbao_Wechat ();
        $rs = $model->insert ( $userWechat );

        return $rs;
    }
    public function getWechatInfo($appid,$wechatId){
        $accessToken = $this->getAccessToken($appid,$wechatId);
        $url = 'https://api.weixin.qq.com/datacube/getusersummary?access_token='.$accessToken;
        $data = json_encode(array('begin_date' => date('Y-m-d',time()-86400), 'end_date' => date('Y-m-d',time()-86400)));
        $result = json_decode($this->send_post($url,$data),true);
        $rs['yesterday_follow_fans'] = isset($result['list'][0]['new_user']) ? $result['list'][0]['new_user'] : 0;
        $rs['yesterday_unfollow_fans'] = isset($result['list'][0]['cancel_user']) ? $result['list'][0]['cancel_user'] : 0;
        $img_url = 'https://api.weixin.qq.com/datacube/getusercumulate?access_token='.$accessToken;//趋势图url
        $data = json_encode(array('begin_date' => date('Y-m-d',time()-86400*7), 'end_date' => date('Y-m-d',time()-86400)));
        $result = json_decode($this->send_post($img_url,$data),true);
        $rs['cumulate_user'] = isset($result['list']) ? $result['list'] : '';
        $rs['yesterday_follow_amount'] = isset($result['list']) ? $result['list'][6]['cumulate_user'] : 0;
        return $rs;
    }

    public function uploadMaterial($appid,$wechatId,$type,$media){

        if($type == 'mpnews'){

        }
        if($type == 'video'){

        }
        if($type == 'voice'){

        }
        if($type == 'image'){
            //			$img = file_get_contents($media[0]);
            //			$img_name = str_replace('http://img.malssh.cn/','',$media[0]);
            //			file_put_contents('../images/'.$img_name,$img);
            //			$data = array('media'=>'@../images/'.$img_name);
            //var_dump(realpath('../images/'.$img_name));exit;
            //$data = array('media'=>'@http://img.malssh.cn/14809951643966.jpeg');
            $accessToken = 'gaKq-vj-Lypb_gjw_aiTTaGSLdEuYZH3hp4IemHOer5DUqC0SM_ayllpxt0Xnk9AITt-DKj2BIMlN4ObiwGooS_KLYOjn6MHDD6NiO1bqj8TQzvW5evSW1BhfHz-3fgfBDXbAJAIKE';
            $url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token='.$accessToken.'&type='.$type;
            $this->curlPost($url,array());
        }

        //
        //		//$accessToken = $this->getAccessToken($appid,$wechatId);
        //		$accessToken = 'tbfKd00mg3w4Yl7gkysAMFAkkqe2FoRPMbIVpJkykO0fimdX2w0Fai6175lhtLMMicNIc2rUTIqZpwYWVc8O2genb8pq-saqrGkyzHxShXB3nUnIjLdhgb2-mSLZu-X5YJCjAEARCV';
        //		$url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.$accessToken.'&type='.$type;
        //
        //		$data = json_encode($media);
        //		$result = $this->send_post($url,$data);
        //		$rs = isset($result) ? $result : '';
        //		return $rs;
    }

    public function curlPost($url,$data){
        //	header("Content-type:text/html;charset=utf-8");
        $ch = curl_init ();  //初始化curl
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, 1 );  //使用post请求
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, json_encode($data));  //提交数据
        curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, true);  //重定向地址也输出
        $return = curl_exec ( $ch ); //得到返回值
        curl_close ( $ch );  //关闭
        var_dump($return);exit;
        return $output;
    }

    public function getWechatMenu($appid,$wechatId){
        $accessToken = $this->getAccessToken($appid,$wechatId);
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token='.$accessToken;
        $result = $this->send_post($url);
        $result = json_decode($result,true);
        if(! isset($result['errcode'])){
            return $result;
        }else{
            if($result['errcode'] == 46003){
                return 'nothave';
            }
            return false;
        }
        return $result;
    }
    public function addWechatMenu($appid,$wechatId,$content,$regulatorId){
        $domainShopKV = new Domain_Zhianbao_RegulatorConfig($regulatorId);
        $domainShopKV->set('WechatMenu',$content);
        $isOpen = $domainShopKV->get('WechatMenuSwitch');
        if(isset($isOpen) && $isOpen == 'open'){
            $accessToken = $this->getAccessToken($appid,$wechatId);
            $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$accessToken;
            $result = $this->send_post($url,$content);
            $result = json_decode($result,true);
            if($result['errcode'] == 0){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }

    public function delWechatMenu($appid,$wechatId,$regulatorId){
        //关闭微信自定义菜单
        $domainShopKV = new Domain_Zhianbao_RegulatorConfig($regulatorId);
        $domainShopKV->delete('WechatMenuSwitch');
        //删除远程微信自定义菜单
        $accessToken = $this->getAccessToken($appid,$wechatId);
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$accessToken;
        $result = $this->send_post($url);
        $result = json_decode($result,true);
        if($result['errcode'] == 0){
            return true;
        }else{
            return false;
        }
    }

    public function massWechatMsg($userId , $appid , $wechatId , $msgType , $textContent = '' , $mediaId = ''){
        $fansModel = new Model_Shenpu_WxFans();
        $fansList = $fansModel->getAll('openid',array('wechat_id'=>$wechatId));
        $fansOpenid = array();
        foreach ($fansList as $key => $value){
            $fansOpenid[] = $value['openid'];
        }
        $data = array('touser' => $fansOpenid);
        if($msgType == 'mpnews'){
            $data['mpnews']['media_id'] = $mediaId;
            $data['msgtype'] = $msgType;
        }
        if($msgType == 'text'){
            $data['text']['content'] = $textContent;
            $data['msgtype'] = $msgType;
        }
        if($msgType == 'voice'){
            $data['voice']['media_id'] = $mediaId;
            $data['msgtype'] = $msgType;
        }
        if($msgType == 'image'){
            $data['image']['media_id'] = $mediaId;
            $data['msgtype'] = $msgType;
        }
        $accessToken = $this->getAccessToken($appid,$wechatId);
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.$accessToken;
        $data = json_encode($data);
        $result = $this->send_post($url,$data);
        $result = json_decode($result,true);
        if($result['errcode'] == 0){
            $masslogModel = new Model_Zhianbao_WechatMassLog();
            $log = array(
				'user_id' => $userId,
				'wechat_id' => $wechatId,
				'touser' => json_encode($fansOpenid),
				'msg_type' => $msgType,
				'media_id' => $mediaId,
				'content' => $textContent,
				'msg_id'  => $result['msg_id'].'',
				'create_time' => time(),
            );
            $masslogModel->insert($log);
            return true;
        }else{
            return false;
        }
    }
    //同步粉丝
    public function syncFans($shopId,$appid,$wechatId,$nextOpenid = ''){
        $fansModel = new Model_Shenpu_WxFans();
        //获取所有的粉丝的openid
        $openids = array('openid'=>array(),'total'=>0);
        do{
            $result = $this->syncOpenid($appid,$wechatId,$nextOpenid);
            if(! isset($result['data']['openid'])){
                return false;
            }
            $openids['openid'] = array_merge($openids['openid'],$result['data']['openid']);
            $openids['total'] = $result['total'];
        }while($result['data']['openid'][$result['count']-1] != $result['next_openid'] );
        //过滤掉取消关注的拿不到信息的粉丝
        $query_fans= $fansModel->getAll('openid',array('shop_id'=>$shopId,'wechat_id'=>$wechatId));
        $querty_fans_openid = array();
        foreach ($query_fans as $query_fan){
            $querty_fans_openid[] = $query_fan['openid'];
        }
        $unfollow_fans_openid = array_diff($querty_fans_openid,$openids['openid']);
        $unfollow_data = array('update_time'=>time(),'subscribe'=>0);
        $fansModel->updateByWhere(array('openid'=>$unfollow_fans_openid),$unfollow_data); //更新取消关注的状态
        //根据粉丝的openid来查询基本信息--每一百条处理一次
        $post_data['user_list'] = $openids['openid'];
        $buffSize = ceil($openids['total'] / 100);
        $fans = array();
        for($i = 0 ; $i < $buffSize ; $i ++){
            $buffer = array_slice($openids['openid'], $i * 100, 100);
            $fansInfos = $this->syncFansInfo($buffer,$appid,$wechatId);
            if(! isset($fansInfos['user_info_list'])){
                return false;
            }
            $fans = $fansInfos['user_info_list'];
            foreach ($fans as $key => $value){
                //整理粉丝数据
                $data = array(
					'shop_id' => $shopId,
					'wechat_id' => $wechatId,
					'openid' => $value['openid'],
					'subscribe' => $value['subscribe'],
					'nickname' => $value['nickname'],
					'sex' => $value['sex'],
					'country' => $value['country'],
					'province' => $value['province'],
					'city' => $value['city'],
					'language' => $value['language'],
					'headimgurl' => $value['headimgurl'],
					'subscribe_time' => $value['subscribe_time'],
					'remark' => $value['remark'],
					'groupid' => $value['groupid'],
					'update_time' => time(),
					'tagid_list' => json_encode($value['tagid_list']),

                );
                if(in_array($value['openid'],$querty_fans_openid)){
                    $update_rs = $fansModel->updateByWhere(array('openid'=>$value['openid'],'wechat_id'=>$wechatId),$data);
                    if(! $update_rs){
                        return false;
                    }
                }else{
                    $insert_rs = $fansModel->insert($data);
                    if(! $insert_rs){
                        return false;
                    }
                }
            }
        }
        return true;
    }
    //请求微信获取粉丝的openid
    public function syncOpenid($appid,$wechatId,$nextOpenid = ''){
        $accessToken = $this->getAccessToken($appid,$wechatId);
        $url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$accessToken;
        if(! empty($nextOpenid)){
            $url .= '&next_openid='.$nextOpenid;
        }
        $result = $this->send_post($url);
        $result = json_decode($result,true);
        return $result;
    }
    //根据openid来同步粉丝信息 -- 每次最多请求100条
    public function syncFansInfo($openids,$appid,$wechatId){
        $accessToken = $this->getAccessToken($appid,$wechatId);
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token='.$accessToken;
        foreach ($openids as $openid){
            $post_data['user_list'][] = array('openid'=>$openid,'lang'=>'zh-CN');
        }
        $result = $this->send_post($url,json_encode($post_data));
        return json_decode($result,true);
    }
    //网页授权获取用户信息
    public function getWebAuthUserInfo($appid,$mp_appid,$code){
        $domain = new Domain_Shenpu_WechatApp();
        $componentAppid = DI ()->config->get ( 'app.wechat.mp_appid' );
        $changeTokenUrl = DI ()->config->get ( 'app.wechat.get_web_authorizer_change_token_url' );
        $userInfoUrl = DI ()->config->get ( 'app.wechat.get_web_authorizer_user_info_url' );
        $wechatInfo = $domain->getByAppId($mp_appid);
        $componentAccessToken = $domain->getComponentAccessToken($wechatInfo);
        $changeTokenUrl = sprintf($changeTokenUrl,$appid,$code,$componentAppid,$componentAccessToken);
        $accessTokenRs = $this->send_post($changeTokenUrl);
        $accessTokenInfo = json_decode($accessTokenRs,true);
        if((!$accessTokenInfo) || !empty($accessTokenInfo['errcode'])){
            throw new LogicException ( T ( 'Get failed' ), 163 );
        }
        $userInfoUrl = sprintf($userInfoUrl,$accessTokenInfo['access_token'],$accessTokenInfo['openid']);
        $userInfoRs = $this->send_post($userInfoUrl);
        $userInfo = json_decode($userInfoRs,true);
        return $userInfo;
    }
    //代收款静默授权
    public function getAgentAuthUserInfo($appid,$mp_appid,$code){
        $domain = new Domain_Zhianbao_WechatApp();
        $componentAppid = DI ()->config->get ( 'app.wechat.mp_appid' );
        $changeTokenUrl = DI ()->config->get ( 'app.wechat.get_web_authorizer_change_token_url' );
        $wechatInfo = $domain->getByAppId($mp_appid);
        $componentAccessToken = $domain->getComponentAccessToken($wechatInfo);
        $changeTokenUrl = sprintf($changeTokenUrl,$appid,$code,$componentAppid,$componentAccessToken);
        $accessTokenRs = $this->send_post($changeTokenUrl);
        $accessTokenInfo = json_decode($accessTokenRs,true);
        if((!$accessTokenInfo) || !empty($accessTokenInfo['errcode'])){
            throw new LogicException ( T ( 'Get failed' ), 148 );
        }
        return $accessTokenInfo;
    }

    private function send_post($url, $post_data = '') {
        $options = array(
			'http' => array(
				'method' => 'POST',
				'header' => 'Content-type:application/x-www-form-urlencoded',
				'content' => $post_data,
				'timeout' => 15 * 60 // 超时时间（单位:s）
        )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        return $result;
    }
    /*public function addWechatDeleteInfo($userWechat) {
     $model = new Model_Zhianbao_WechatDelete();
     $rs = $model->insert ( $userWechat );

     return $rs;
     }*/
//    //获取支付时的openid
//    public function getPayOpenid($userId,$shopId){
//        $domainShopKV = new Domain_Shenpu_ShopKvStore($shopId, $userId);
//        $info = $domainShopKV->get('AgentPay');
//        if((! isset($info)) || (isset($info) && $info['shop_value'] == 'open')) {
//            //开启了代收款
//            $agentInfo = DI ()->cookie->get('zab_agent');
//            $agentInfo = json_decode($agentInfo,true);
//            if(isset($agentInfo)){
//                return $agentInfo['openid'];
//            }else{
//                return false;
//            }
//        }else{
//            $agentInfo = DI ()->cookie->get('sp_ci');
//            $agentInfo = json_decode($agentInfo,true);
//            if(isset($agentInfo)){
//                return $agentInfo['openid'];
//            }else{
//                return false;
//            }
//        }
//    }
    //开启微信自定义菜单
    public function openWechatMenu($appid,$wechatId,$regulatorId){
        $domainShopKV = new Domain_Zhianbao_RegulatorConfig($regulatorId);
        $domainShopKV->set('WechatMenuSwitch','open');
        $info = $domainShopKV->get('WechatMenu');
        if($info) {
           $menuContent = $info;
            $rs = $this->addWechatMenu($appid,$wechatId,$menuContent,$regulatorId);
            if( ! $rs){
                return false;
            }
        }else{
            return true;
        }
    }
    //获取本地自定义菜单
    public function getLocalWechatMenu($appid,$wechatId,$regulatorId){
        $domainShopKV = new Domain_Zhianbao_RegulatorConfig($regulatorId);
        $info = $domainShopKV->get('WechatMenu');
        $swtich = $domainShopKV->get('WechatMenuSwitch');
        if(isset($swtich) && $swtich == 'open'){
            $open = true;
        }else{
            $open = false;
        }
        if($info) {
            //本地存放了自定义菜单
            $menuInfo = json_decode($info,true);
            $menuInfo['is_open'] = $open;
            return $menuInfo;
        }else{
            //本地未存放自定义菜单 先从线上拉 存到本地
            $rs = $this->getWechatMenu($appid,$wechatId);
            if(! $rs){
                return false;
            }
            if($rs == 'nothave'){
                return 'nothave';
            }
            $rs = $rs['menu'];
            $domainShopKV->set('WechatMenu',json_encode($rs));
            $rs['is_open'] = $open;
            return $rs;
        }

    }
    //发送模板消息
    public function sendTempMsg($appid,$wechatId,$type,$tempData){
        $accessToken = $this->getAccessToken($appid,$wechatId);
        //$accessToken = 'oabs4Vl1CJUNaHkFPVxhjCHP17CtHoPU1yzvM-MvYABmM-aqgrNjJDrVs7rX-uG8FNI3_jSiHZMoNwiA95pNi5JqGIMD35_UFVNQBgdmq_wXIHfACAPAZ';
        /*
        $tempBn = '';
        $tempId = '';
        //根据模板编号获取模板ID
        switch ($type){
            case 'payOk' : $tempBn = 'TM00015';break;
            case 'ship' : $tempBn = 'OPENTM200565259';break;
            case 'agreeCancel' : $tempBn = 'TM00431';break;
            case 'rejectCancel' : $tempBn = 'TM00431';break;
        }
        if(! $tempBn){
            return false;
        }
        $url = 'https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token='.$accessToken;
        $data = array('template_id_short' => $tempBn);
        $result = $this->send_post($url,json_encode($data));
        $result = json_decode($result,true);
        if($result['errcode'] == ''){
            //拿到模板ID后发送模板消息
            $tempId = $result['template_id'];
            $tempData['template_id'] = $tempId;
            $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$accessToken;
            $result = $this->send_post($url,json_encode($tempData));
            return true;
        }else{
            return false;
        }
        */
        $filter = array(
            'wechat_id' => $wechatId,
            'type' => $type,
        );
        $templatModel = new Model_Zhianbao_WechatTemp();
        $info = $templatModel->getByWhere($filter,'template_id');
        if(empty($info)){
            $tempBn = '';
            $tempId = '';
            //根据模板编号获取模板ID
            switch ($type){
                case 'payOk' : $tempBn = 'TM00015';break;
                case 'ship' : $tempBn = 'OPENTM200565259';break;
                case 'agreeCancel' : $tempBn = 'TM00431';break;
                case 'rejectCancel' : $tempBn = 'TM00431';break;
            }
            if(! $tempBn){
                return false;
            }
            $url = 'https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token='.$accessToken;
            $data = array('template_id_short' => $tempBn);
            $result = $this->send_post($url,json_encode($data));
            $result = json_decode($result,true);
            $tempId = $result['template_id'];
            //插入消息模板ID
            $data = array('wechat_id' =>$wechatId, 'type' =>$type, 'template_id' =>$tempId, 'create_time' =>time());
            $templatModel->insert($data);
        }else{
            $tempId = $info['template_id'];
        }
        //拿到模板ID后发送模板消息
        $tempData['template_id'] = $tempId;
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$accessToken;
        $result = $this->send_post($url,json_encode($tempData));
        return true;
    }
    //生成jsapi-ticket
    public function getJsApi($appid,$redurl){
        $wechatInfo = $this->getInfoByAppId('wxd9f15546184ef6c4');
        if(! $wechatInfo){
            return false;
        }
        $accessToken = $this->getAccessToken($appid,$wechatInfo['id']);
        $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$accessToken.'&type=jsapi';
        $result = $this->send_post($url);
        $result = json_decode($result,true);
        if(! isset($result['ticket'])){
            return false;
        }
        $noncestr = PhalApi_Tool::createRandStr ( 16 ) ;
        $times = time();
        $string = 'jsapi_ticket='.$result['ticket'].'&noncestr='.$noncestr.'&timestamp='.$times.'&url='.$redurl;
        $signature = sha1($string);
        $rs = array(
            'appid' => $wechatInfo['appid'],
            'noncestr' => $noncestr,
            'timestamp' => $times,
            'signature' => $signature,
        );
        return $rs;
    }
    //发送粉丝消息
    public function sendFanTempMsg($appid,$wechatId,$fansData){
        $accessToken = $this->getAccessToken($appid,$wechatId);
        //$accessToken = 'EKd26d81UN0o3yhiuCZJJXHIOFLXmpBgF68FBq2nQy3-eilM3041yL1A--4SghGOdc2vGXy8611qYAf5JTrPu4cRAzZWd878WaXzU7w61xw5nsoBWOmlqUbK3HdwOEb4TGOdAGAJWE';
        if($accessToken){
            $data = json_encode($fansData, JSON_UNESCAPED_UNICODE);
            $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$accessToken;
            $result = $this->https_post($url,$data);
            return true;
        }else{
            return false;
        }
    }
    function https_post($url,$data){
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_URL,$url);
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,$url);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,$url);
        curl_setopt($curl,CURLOPT_POST,1);
        curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }
}
