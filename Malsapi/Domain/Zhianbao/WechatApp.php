<?php
class Domain_Zhianbao_WechatApp {
    public function getByAppId($appId, $cols = '*') {
        $rs = array ();

        // 版本1：简单的获取
        $model = new Model_Zhianbao_WechatApp ();
        $rs = $model->getByWhere ( array (
				'appid' => $appId 
        ), $cols );

        if (! $rs)
        return false;

        return $rs;
    }

    public function getAuthByWechatId($wechatId, $cols = '*') {
        $rs = array ();

        // 版本1：简单的获取
        $model = new Model_Zhianbao_WechatAuth();
        $rs = $model->getByWhere ( array (
				'user_wechat_id' => $wechatId
        ), $cols );

        if (! $rs)
        return false;

        return $rs;
    }

    public function updateTicket($id, $componentVerifyTicket) {
        $utime = time ();
        $model = new Model_Zhianbao_WechatApp ();
        $data = array (
				'component_verify_ticket' => $componentVerifyTicket,
				'component_verify_ticket_utime' => $utime 
        );

        $rs = $model->update ( $id, $data );

        return $rs;
    }
    public function getAuthUrl($appId, $callback) {
        $info = $this->getByAppId ( $appId, '*' );
        if (empty ( $info )) {
            throw new LogicException ( T ( 'Appid does not exist' ), 126 );
        }

        $accessToken = $this->getComponentAccessToken ( $info );

        $preAuthCode = $this->getPreAuthCode ( $info );

        $url = DI ()->config->get ( 'app.wechat.get_component_auth_url' );
        $url = sprintf ( $url, $appId, $preAuthCode, $callback );

        return $url;
    }
    public function getComponentAccessToken(& $info) {
        $appId = $info ['appid'];
        $id = $info ['id'];
        $appsecret = $info ['appsecret'];

        $accessToken = $info ['component_access_token'];
        $component_verify_ticket = $info ['component_verify_ticket'];
        $accessTokenUTime = $info ['component_access_token_utime'];
        $accessTokenUTimeExpiresIn = $info ['component_access_token_expires_in'];

        $flag = false;
        if (! empty ( $accessTokenUTime )) {
            // 提前15分钟刷新,如果（最后次刷新时间+有效期）减去提前时间 小于当前时间，应该刷新token
            $need_flush_time = $accessTokenUTime + $accessTokenUTimeExpiresIn - 900;
             
            if ($need_flush_time <= time ()) {
                $flag = true;
            }
        } else {
            $flag = true;
        }

        if ($flag) {
             
            $data_string = '{"component_appid":"' . $appId . '" ,"component_appsecret": "' . $appsecret . '", "component_verify_ticket": "' . $component_verify_ticket . '" }';
            $curl = new PhalApi_CUrl ();
            $url = DI ()->config->get ( 'app.wechat.get_component_token_url' );
            $arrAccessToken = $curl->post ( $url, $data_string );
            // echo $data_string; var_dump($arrAccessToken);exit;
            // exit ();
            $arrAccessToken = ! empty ( $arrAccessToken ) ? json_decode ( $arrAccessToken, true ) : '';
            if (empty ( $arrAccessToken ) || ! isset ( $arrAccessToken ['component_access_token'] )) {
                DI ()->logger->error ( 'Request Data', $url.'==>'.$data_string );
                DI ()->logger->error ( 'Request WeChat failed(api_component_token)', $arrAccessToken );
                throw new LogicException ( T ( 'Request WeChat token failed' ), 123 );
            }
             
            if (! $this->updateComponentAccessToken ( $id, $arrAccessToken )) {
                $rs ['code'] = 105;
                $rs ['msg'] = T ( 'Update failed' );
                return $rs;
            }
             
            $accessToken = $arrAccessToken ['component_access_token'];

            $info ['component_access_token'] = $accessToken;//修复token刷新了，info还是旧值的问题
        }

        return $accessToken;
    }
    public function getPreAuthCode($info) {
        $appId = $info ['appid'];
        $id = $info ['id'];
        $component_access_token = $info ['component_access_token'];
        $pre_auth_code = $info ['pre_auth_code'];
        $preAuthCodeUTime = $info ['pre_auth_code_utime'];
        $preAuthCodeExpiresIn = $info ['pre_auth_code_expires_in'];

        $flag = true;
        // 		if (! empty ( $preAuthCodeUTime )) {
        // 			// 提前5分钟刷新,如果（最后次刷新时间+有效期）减去提前时间 小于当前时间，应该刷新token
        // 			$need_flush_time = $preAuthCodeUTime + $preAuthCodeExpiresIn - 300;
         
        // 			if ($need_flush_time <= time ()) {
        // 				$flag = true;
        // 			}
        // 		} else {
        // 			$flag = true;
        // 		}

        if ($flag) {
             
            $data_string = '{"component_appid":"' . $appId . '" }';
             
            $curl = new PhalApi_CUrl ();
            $url = DI ()->config->get ( 'app.wechat.get_component_preauthcode_url' );
            $url = sprintf ( $url, $component_access_token );
            $arrPreAuthCode = $curl->post ( $url, $data_string );
            // var_dump ( $arrPreAuthCode );
            // exit ();
            $arrPreAuthCode = ! empty ( $arrPreAuthCode ) ? json_decode ( $arrPreAuthCode, true ) : '';
            if (empty ( $arrPreAuthCode ) || ! isset ( $arrPreAuthCode ['pre_auth_code'] )) {
                DI ()->logger->error ( 'Request WeChat failed(api_create_preauthcode)', $arrPreAuthCode );
                throw new LogicException ( T ( 'Request WeChat pre_auth_code failed' ), 123 );
            }
             
            if (! $this->updateComponentPreAuthCode ( $id, $arrPreAuthCode )) {
                $rs ['code'] = 105;
                $rs ['msg'] = T ( 'Update failed' );
                return $rs;
            }
             
            $pre_auth_code = $arrPreAuthCode ['pre_auth_code'];
        }

        return $pre_auth_code;
    }
    public function updateComponentAccessToken($id, $componentAccessToken) {
        $uTime = time ();
        $model = new Model_Zhianbao_WechatApp ();
        $data = array (
				'component_access_token' => $componentAccessToken ['component_access_token'],
				'component_access_token_utime' => $uTime,
				'component_access_token_expires_in' => $componentAccessToken ['expires_in'] 
        );

        $rs = $model->update ( $id, $data );

        return $rs;
    }
    public function updateComponentPreAuthCode($id, $preAuthCode) {
        $uTime = time ();
        $model = new Model_Zhianbao_WechatApp ();
        $data = array (
				'pre_auth_code' => $preAuthCode ['pre_auth_code'],
				'pre_auth_code_utime' => $uTime,
				'pre_auth_code_expires_in' => $preAuthCode ['expires_in'] 
        );

        $rs = $model->update ( $id, $data );

        return $rs;
    }

    /**
     * 公众号扫码绑定初始化
     * 公众号之前绑定过其他商户，解绑后，可以绑定到其他商户
     *
     * @param int $appId
     * @param int $userId
     * @param int $shopId
     * @param string $authCode
     * @param int $expiresIn
     */
    public function init($appId, $regulatorId, $authCode, $expiresIn) {
        $info = $this->getByAppId ( $appId, '*' );
        if (empty ( $info )) {
            throw new LogicException ( T ( 'Appid does not exist' ), 126 );
        }

        //获取app token
        $accessToken = $this->getComponentAccessToken ( $info );

        // 获取公众号token
        $authorizationInfo = $this->initAuthorizerAccessToken ( $info, $authCode );


        //检查是否有重复授权,没有的话说明没有绑定过
        if($this->checkWechatIsAuth($authorizationInfo['authorizer_appid'])){
            throw new LogicException ( T ( 'Cannot duplicate authorization' ), 138 );
        }

        //检查当前店铺是否已经有授权，并且已存在的授权跟绑定的授权不是同一个
        if(!$this->checkAuthIsSameWechat($regulatorId,$authorizationInfo['authorizer_appid'])){
            throw new LogicException ( T ( 'The current wechat has been bound, please go to cancel binding' ), 139 );
        }

         
        // 获取公众号账号基本信息
        $authorizerInfo = $this->getAuthorizerInfo ( $info, $authorizationInfo );

        //获取当前店铺绑定的公众号ID
        $wechatId = $this->getShopOfWechatId($regulatorId);
        if($wechatId){	// 取消授权，再次授权
            $this->updateWechat ( $wechatId, $authorizerInfo );
        }else{// 创建公众号
            $wechatId = $this->createWechat ( $regulatorId, $authorizerInfo ,$authorizationInfo);
        }

        // 创建公众号授权信息
        $this->addWechatAuth ( $regulatorId,$wechatId, $info, $authorizationInfo );

        return $wechatId;
    }

    //获取店铺的公众号ID
    public function getShopOfWechatId($regulatorId){
        $domainWechat = new Domain_Zhianbao_Wechat();
        $wechatInfo =  $domainWechat->getByRegulatorId($regulatorId,'id');
        if(!empty($wechatInfo)){
            return $wechatInfo['id'];
        }else{
            return false;
        }
    }

    public function initAuthorizerAccessToken($info, $authCode) {
        $appId = $info ['appid'];
        $component_access_token = $info ['component_access_token'];

        $data_string = '{"component_appid":"' . $appId . '" ,"authorization_code": "' . $authCode . '"}';

        $curl = new PhalApi_CUrl ();
        $url = DI ()->config->get ( 'app.wechat.get_authorizer_access_token_url' );
        $url = sprintf ( $url, $component_access_token );
        $arrAuthorizationInfo = $curl->post ( $url, $data_string );

        //var_dump ( $arrAuthorizationInfo );exit ();

        $arrAuthorizationInfo = ! empty ( $arrAuthorizationInfo ) ? json_decode ( $arrAuthorizationInfo, true ) : '';
        if (empty ( $arrAuthorizationInfo ) || ! isset ( $arrAuthorizationInfo ['authorization_info'] )) {
            DI ()->logger->error ( 'Request WeChat failed(api_query_auth)', $arrAuthorizationInfo );
            throw new LogicException ( T ( 'Request WeChat authorization_info failed' ), 123 );
        }

        return $arrAuthorizationInfo ['authorization_info'];
    }
    public function getAuthorizerAccessToken($appId,$wechatId) {

        $authInfo = $this->getAuthByWechatId ( $wechatId, '*' );
        if (empty ( $authInfo )) {
            throw new LogicException ( T ( 'Wechat auth does not exist' ), 129 );
        }

        $appInfo = $this->getByAppId ( $appId, '*' );
        if (empty ( $appInfo )) {
            throw new LogicException ( T ( 'Appid does not exist' ), 126 );
        }


        //应用数据
        //获取app token
        $component_access_token = $this->getComponentAccessToken ( $appInfo );

        //公众号授权数据
        $id = $authInfo ['id'];
        $authorizerRefreshToken = $authInfo ['authorizer_refresh_token'];
        $refreshTime = $authInfo ['authorizer_refresh_token_utime'];
        $authorizerAppid = $authInfo ['authorizer_appid'];
        $expiresIn = $authInfo ['expires_in'];
        $authorizerAccessToken = $authInfo ['authorizer_access_token'];

        $flag = false;
        if (! empty ( $refreshTime )) {
            // 提前5分钟刷新,如果（最后次刷新时间+有效期）减去提前时间 小于当前时间，应该刷新token
            $need_flush_time = $refreshTime + $expiresIn - 900;

            if ($need_flush_time <= time ()) {
                $flag = true;
            }
        } else {
            $flag = true;
        }

        if ($flag) {

            $data_string = '{"component_appid":"' . $appId . '" ,"authorizer_appid": "' . $authorizerAppid . '","authorizer_refresh_token": "' . $authorizerRefreshToken . '"}';

            $curl = new PhalApi_CUrl ();
            $url = DI ()->config->get ( 'app.wechat.get_authorizer_refresh_token_url' );
            $url = sprintf ( $url, $component_access_token );
            $arrRefreshToken = $curl->post ( $url, $data_string );
            // var_dump ( $arrRefreshToken );
            // exit ();
            $arrRefreshToken = ! empty ( $arrRefreshToken ) ? json_decode ( $arrRefreshToken, true ) : '';
            if (empty ( $arrRefreshToken ) || ! isset ( $arrRefreshToken ['authorizer_access_token'] )) {
                DI ()->logger->error ( 'Request WeChat failed(api_authorizer_token)', $arrRefreshToken );
                DI ()->logger->error ( 'Request WeChat data_string ', $url.'==>'.$data_string );
                throw new LogicException ( T ( 'Request WeChat authorizer_access_token failed' ), 123 );
            }

            if (! $this->updateAuthorizerAccessToken ( $id, $arrRefreshToken )) {
                $rs ['code'] = 105;
                $rs ['msg'] = T ( 'Update failed' );
                return $rs;
            }

            $authorizerAccessToken = $arrRefreshToken ['authorizer_access_token'];
        }

        return $authorizerAccessToken;
    }
    public function getAuthorizerInfo($info, $authorizationInfo) {
        $appId = $info ['appid'];
        $component_access_token = $info ['component_access_token'];
        $authorizerAppid = $authorizationInfo ['authorizer_appid'];

        $data_string = '{"component_appid":"' . $appId . '" ,"authorizer_appid": "' . $authorizerAppid . '"}';

        $curl = new PhalApi_CUrl ();
        $url = DI ()->config->get ( 'app.wechat.get_authorizer_info_url' );
        $url = sprintf ( $url, $component_access_token );
        $arrAccountInfo = $curl->post ( $url, $data_string );
        // var_dump ( $arrPreAuthCode );
        // exit ();
        $arrAccountInfo = ! empty ( $arrAccountInfo ) ? json_decode ( $arrAccountInfo, true ) : '';
        if (empty ( $arrAccountInfo ) || ! isset ( $arrAccountInfo ['authorizer_info'] )) {
            DI ()->logger->error ( 'Request WeChat failed(api_get_authorizer_info)', $arrAccountInfo );
            throw new LogicException ( T ( 'Request WeChat authorizer_info failed' ), 123 );
        }

        return $arrAccountInfo ['authorizer_info'];
    }
    public function createWechat($regulatorId,$authorizerInfo,$authorizationInfo) {
        $time = time ();
        $wechatType = 'w99';
        if($authorizerInfo['service_type_info']['id'] == 0 || $authorizerInfo['service_type_info']['id'] == 1){
            if($authorizerInfo['verify_type_info']['id'] == -1){
                $wechatType = 'w1';
            }else if($authorizerInfo['verify_type_info']['id'] == 0){
                $wechatType = 'w3';
            }
        }else if($authorizerInfo['service_type_info']['id'] == 2){
            if($authorizerInfo['verify_type_info']['id'] == -1){
                $wechatType = 'w2';
            }else if($authorizerInfo['verify_type_info']['id'] == 0){
                $wechatType = 'w4';
            }
        }


        $data = array (
		        'regulator_id'=>$regulatorId,
				'name' => $authorizerInfo ['nick_name'],
				'description' => '',
				'rank' => 0,
				'token' => PhalApi_Tool::createRandStr ( 32 ),
				'encodingaeskey' => PhalApi_Tool::createRandStr ( 43 ),
				'wechat_type' => $wechatType,
				'original' => $authorizerInfo ['user_name'],
				'appid' => $authorizationInfo['authorizer_appid'],
				'appsecret' => '',
				'create_time' => $time,
				'update_time' => $time,
				'is_bind' => 'y',
				'is_auth' => 'y',
				'authorizer_info' => json_encode ( $authorizerInfo ) 
        );
        $domainWechat = new Domain_Zhianbao_Wechat ();

        return $domainWechat->addWechatInfo ( $data );
    }

    public function updateWechat($wechatId, $authorizerInfo) {
        $time = time ();
        $wechatType = 'w99';
        if($authorizerInfo['service_type_info']['id'] == 0 || $authorizerInfo['service_type_info']['id'] == 1){
            if($authorizerInfo['verify_type_info']['id'] == -1){
                $wechatType = 'w1';
            }else if($authorizerInfo['verify_type_info']['id'] == 0){
                $wechatType = 'w3';
            }
        }else if($authorizerInfo['service_type_info']['id'] == 2){
            if($authorizerInfo['verify_type_info']['id'] == -1){
                $wechatType = 'w2';
            }else if($authorizerInfo['verify_type_info']['id'] == 0){
                $wechatType = 'w4';
            }
        }


        $data = array (
				'id'=> $wechatId,
				'name' => $authorizerInfo ['nick_name'],
				'wechat_type' => $wechatType,
				'original' => $authorizerInfo ['user_name'],
				'update_time' => $time,
				'is_bind' => 'y',
				'is_auth' => 'y',
				'authorizer_info' => json_encode ( $authorizerInfo )
        );
        $domainWechat = new Domain_Zhianbao_Wechat ();

        return $domainWechat->updateWechatInfo( $data );
    }

    public function addWechatAuth($regulatorId,$wechatId, $info, $arrAuthorizationInfo) {
        $appId = $info ['appid'];
        $time = time();
        $modelWechatAuth = new Model_Zhianbao_WechatAuth ();
        $data = array (
				'regulator_id'=>$regulatorId,
				'user_wechat_id' => $wechatId,
				'wechat_app_id' => $appId,
				'authorizer_appid'=>$arrAuthorizationInfo['authorizer_appid'],
				'authorizer_access_token' => $arrAuthorizationInfo ['authorizer_access_token'],
				'expires_in' => $arrAuthorizationInfo ['expires_in'],
				'authorizer_refresh_token' => $arrAuthorizationInfo ['authorizer_refresh_token'],
				'authorizer_refresh_token_utime' => $time,
				'authorization_info' => json_encode($arrAuthorizationInfo),
				'create_time' => $time,
        );
        $modelWechatAuth->insert ( $data );
    }

    public function checkWechatIsAuth($authorizerAppid){
        $modelWechatAuth = new Model_Zhianbao_WechatAuth ();
        $filter = array('authorizer_appid'=>$authorizerAppid);
        $row = $modelWechatAuth->getByWhere($filter,'id');
        if(!empty($row)){
             
            return true;
             
        }else{
             
            return false;
        }
    }

    //检查当前店铺是否已经有授权，并且已存在的授权跟绑定的授权不是同一个
    public function checkAuthIsSameWechat($regulatorId,$authorizerAppid){
        $domainWechat = new Domain_Zhianbao_Wechat();
        $wechatInfo =  $domainWechat->getByRegulatorId($regulatorId,'id,is_bind,appid');
        if(!empty($wechatInfo)){
            if($wechatInfo['is_bind'] == 'y' && $wechatInfo['appid'] != $authorizerAppid ){
                return false;
            }else{
                return true;
            }

        }else{
            return true;
        }
    }

    public function updateAuthorizerAccessToken($id, $arrRefreshToken) {
        $uTime = time ();
        $model = new Model_Zhianbao_WechatAuth();
        $data = array (
				'authorizer_access_token' => $arrRefreshToken ['authorizer_access_token'],
				'authorizer_refresh_token_utime' => $uTime,
				'expires_in' => $arrRefreshToken ['expires_in']
        );

        $rs = $model->update ( $id, $data );

        return $rs;
    }

    public function clearAuth($wechatId){
        $model = new Model_Zhianbao_WechatAuth();
        return $model->deleteByWhere(array('user_wechat_id'=>$wechatId));
    }

    public function checkWechatIsExistByAppId($appId){
        $modelWechat = new Domain_Shenpu_Wechat();
        $wechatInfo = $modelWechat->getBindWechatByAppId($appId,'id');
        if(!empty($wechatInfo)){
            return $wechatInfo['id'];
        }else{
            return false;
        }
    }

    /*public function checkUserIsSelfByAppId($appId,$userId){
        $domainWechat = new Domain_Shenpu_Wechat();
        $wechatInfo = $domainWechat->getInfoByAppId($appId,'user_id,is_bind');

        if(!empty($wechatInfo) && $wechatInfo['is_bind'] == 'y' && $wechatInfo['user_id']  == $userId){
            return true;
        }else{
            //DI ()->logger->error ( 'checkUserIsSelfByAppId', $wechatInfo );
            //DI ()->logger->error ( 'checkUserIsSelfByAppId', $userId );
            return false;
        }
    }*/

    //是取消授权
    public function  eventUnauthorized($AuthorizerAppid){

        $wechatId = $this->checkWechatIsExistByAppId($AuthorizerAppid);
        if(empty($wechatId)){
           //DI ()->logger->error ( 'eventUnauthorized::checkWechatIsExistByAppId取消授权，根据公众号appid查询失败=>', $AuthorizerAppid );
            return false;
        }

        //清除授权信息
        $this->clearAuth($wechatId);

        //解除绑定
        $data = array (
				'id'=> $wechatId,
				'is_bind' => 'n',
                'update_time'=>time()
        );
        $domainWechat = new Domain_Shenpu_Wechat ();

        return $domainWechat->updateWechatInfo( $data );
    }

    //是更新授权
    public function  eventUpdateauthorized(){
        return true;
    }

    //是授权成功通知
    public function  eventAuthorized(){

        return true;
    }
}
