<?php
class Domain_Jiafubao_CustomerSession {
    var $model;
    var $loginTime = 86400;

    public function __construct() {
        $this->model = new Model_Jiafubao_CustomerSession();
    }

    public function makeSession($customerId,$sessionData = array()){

//        $filter = array('customer_id'=>$customerId);
//        $sessionRow = $this->model->getByWhere($filter);
//
//        $isNewSession = false;
//        $session = '';
//        if(!empty($sessionRow)){
//            $expireTime = intval($sessionRow['create_time']) + DI ()->config->get ( 'app.login.session_time' );
//            if( $expireTime < time() ){
                $this->deleteSession($customerId);
//                $isNewSession = true;
//            }else{
//                $session = $sessionRow['session'];
//            }
//        }else{
//            $isNewSession = true;
//        }

//        if($isNewSession){
            $session = $this->genSessionId();
            $data = array(
			'customer_id' => $customerId,
			'session' => $session,
            'data'=>json_encode($sessionData),
			'create_time' => time()
            );

            $this->model->insert($data);
 //       }



        return $session;
    }


    private function genSessionId() {
        return md5(uniqid('', true).PhalApi_Tool::getClientIp().microtime(true).mt_rand(0,9999));
    }

    public function deleteSession($customerId) {
        $filter = array('customer_id'=> $customerId);
        $this->model->deleteByWhere($filter);
    }

    public function checkSession($session){

        $filter = array('session'=>$session);
        $sessionRow = $this->model->getByWhere($filter);

        if(!empty($sessionRow)){
            $expireTime = intval($sessionRow['create_time']) + DI ()->config->get ( 'app.login.customer_session_time' );
            if( $expireTime < time() ){
                $this->deleteSession($sessionRow['customer_id']);
                return false;
            }else{

                $sessionRow['data'] = $sessionRow;
                return $sessionRow;
            }

        }else{
            return false;
        }
    }


}
