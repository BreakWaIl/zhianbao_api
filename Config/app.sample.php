<?php
/**
 * 请在下面放置任何您需要的应用配置
 */
return array (
		
		/**
		 * 应用接口层的统一参数
		 */
		'apiCommonRules' => array (
				'sign' => array (
						'name' => 'sign',
						'require' => false 
				) 
		),
    'login' => array (
        'user_session_time' => 86400,
        'customer_session_time' => 86400 * 3650,//10年
    ),
    'shenpuApi' => array (
        'get_api_url' => 'zabapi.mshenpu.com'
    ),
    'api_root' =>'http://192.168.100.156/zhianbao_api',
    'sms' => array (
        'qcloud_sms_appid' => '1400013841',
        'qcloud_sms_appkey' => 'f4516f241ab96f8c25c0020975f4f855',
        'diyang_sms_yx_user' => 'miaoshenyx',
        'diyang_sms_yx_pwd' => 'miaoshenyx',
        'diyang_sms_yzm_user' => 'miaoshenyzm',
        'diyang_sms_yzm_pwd' => 'miaoshenyzm',
        'diyang_sms_wl_user' => 'miaoshenwl',
        'diyang_sms_wl_pwd' => 'miaoshenwl',
    ),
		'api' => array (
				'sign_token' => 'BaNyx2U8j1'
		),
    'componentWechat' => array (
        'get_component_token_url' => 'https://api.weixin.qq.com/cgi-bin/component/api_component_token',
        'get_component_preauthcode_url' => 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token=%s',
        'get_component_auth_url' => 'https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=%s&pre_auth_code=%s&redirect_uri=%s',
        'get_authorizer_access_token_url'=>'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=%s',
        'get_authorizer_refresh_token_url'=>'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=%s',
        'get_authorizer_info_url'=>'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token=%s'
    ),
    'wechat' => array (
        'get_access_token_url' => 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s',
        'get_component_token_url' => 'https://api.weixin.qq.com/cgi-bin/component/api_component_token',
        'get_component_preauthcode_url' => 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token=%s',
        'get_component_auth_url' => 'https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=%s&pre_auth_code=%s&redirect_uri=%s',
        'get_authorizer_access_token_url'=>'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=%s',
        'get_authorizer_refresh_token_url'=>'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=%s',
        'get_authorizer_info_url'=>'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token=%s',
        'get_web_authorizer_code_url' => 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=STATE&component_appid=%s#wechat_redirect',
        'get_web_base_authorizer_code_url' => 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_base&state=STATE&component_appid=%s#wechat_redirect',
        'get_web_authorizer_change_token_url' => 'https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=%s&code=%s&grant_type=authorization_code&component_appid=%s&component_access_token=%s',
        'get_web_authorizer_user_info_url' => 'https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=zh_CN',
        'mp_appid'=>'',
        'mp_appsecret'=>'',
        'mp_token'=>'',
        'mp_key'=>'',
    ),
    'Pay' => array(

        'wechat' => array(
            'appid' => 'wxd9f15546184ef6c4',
            'mchid' => '1368554702',
            'appsecret' => '776419cef6cc67013a1adab1463598c9',
            'key' => 'FHUbJa3lhOlihF1aJnLgQ35HVdL8qDvW'
        ),
    ),
    'shenpu' => array(
        'get_api_url' => 'http://192.168.100.156/shenpu_api/private/',
    ),



);
