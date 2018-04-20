<?php
/**
 * 本地调度器 Task_Runner_Dispatch
 * 
 * - 本地内部调度
 * 
 */

class Task_Runner_Dispatch extends Task_Runner {

    protected function youGo($task) {
        DI()->pay = new Pay_Lite();
        $params = $task['params'];
        $params['service'] = $task['service'];
        $params['sign'] = $this->encryptAppKey($params);
        DI()->request = new PhalApi_Request($params);
        DI()->response = new PhalApi_Response_Json();
        $phalapi = new PhalApi();
        $rs = $phalapi->response();
        $apiRs = $rs->getResult();

        if ($apiRs['ret'] != 200 || $apiRs['data']['code'] != 0 ) {
            DI()->logger->debug('task local go fail', 
                array('task' => $task, 'rs' => $apiRs));

            return FALSE;
        }

        return $apiRs;
    }

    public	function encryptAppKey($params) {
		//echo  $this->assemble ( $params ) ."<br /><br /><br /><br /><br />";
		$sign_token = DI ()->config->get ( 'app.api.sign_token' );
		return strtoupper ( md5 ( strtoupper ( md5 ( $this->assemble ( $params ) ) ) . $sign_token) );
	}
    public function assemble($params) {
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
    }

}

