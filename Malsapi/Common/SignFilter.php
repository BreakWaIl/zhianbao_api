<?php
/**
 * Common_SignFilter 拦截器
 *
 * @package     PhalApi\Filter
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-10-23
 */
class Common_SignFilter implements PhalApi_Filter {
	protected $signName;
	public function __construct($signName = 'sign') {
		$this->signName = $signName;
	}
	public function check() {
		$allParams = DI ()->request->getAll ();
		if (empty ( $allParams )) {
			return;
		}
		
		$sign = isset ( $allParams [$this->signName] ) ? $allParams [$this->signName] : '';
		unset ( $allParams [$this->signName] );
		
		$expectSign = $this->encryptAppKey ( $allParams );
		
		if ($expectSign != $sign) {
			DI ()->logger->debug ( 'Wrong Sign', array (
					'needSign' => $expectSign 
			) );
			throw new PhalApi_Exception_BadRequest ( T ( 'wrong sign' ), 6 );
		}
		
	} 
	function encryptAppKey($params) {
		//error_log( $this->assemble ( $params )."\n",3,'./test.txt');
		return strtoupper ( md5 ( strtoupper ( md5 ( $this->assemble ( $params ) ) ) . DI ()->config->get ( 'app.api.sign_token' ) ) );
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
