<?php
/**
 * Common_SignFilter 拦截器
 *
 * @package     PhalApi\Filter
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-10-23
 */
class SignFilter implements MalsapiFilter {
	
	public function filter($service, array &$params) {
		$params['service'] = $service;
		$expectSign = $this->encryptAppKey ( $params );
		$params['sign'] = $expectSign;
		
		//unset($params['service']);
	}
	function encryptAppKey($params) {
		$params['service'] = $params['service'] .'.Go';
		//echo  $this->assemble ( $params ) ."<br /><br /><br /><br /><br />";
		$sign_token = DI ()->config->get ( 'app.api.sign_token' );
		return strtoupper ( md5 ( strtoupper ( md5 ( $this->assemble ( $params ) ) ) . $sign_token) );
	}
	function assemble($params) {
		if (! is_array ( $params ))
			return null;
		ksort ( $params, SORT_STRING );
		$sign = '';
		foreach ( $params as $key => $val ) {
			if (is_null ( $val ))
				continue;
			if (is_bool ( $val ))
				$val = ($val) ? 1 : 0;
			$sign .= $key . (is_array ( $val ) ? $this->assemble ( $val ) : $val);
		}
		return $sign;
	} // End Function
}
