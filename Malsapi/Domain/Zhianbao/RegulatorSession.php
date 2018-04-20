<?php
class Domain_Zhianbao_RegulatorSession {
    var $model;
    var $loginTime = 86400;

    public function __construct() {
        $this->model = new Model_Zhianbao_RegulatorSession();
    }

    /**
     * 
     * 每次登陆创建新session，线删除所有session
     * @param int $userId
     * @param array $sessionData
     */
    public function makeSession($userId,$sessionData = array()){

        $this->deleteSession($userId);

        $session = $this->genSessionId();
        $data = array(
			'regulator_id' => $userId,
			'session' => $session,
            'data'=>json_encode($sessionData),
			'create_time' => time(),
        );

        $this->model->insert($data);

        return $session;
    }

    public function checkExistLogin($userId){

    }




    private function genSessionId() {
        return md5(uniqid('', true).PhalApi_Tool::getClientIp().microtime(true).mt_rand(0,9999));
    }

    public function deleteSession($userId) {
        $filter = array('regulator_id'=>$userId);
        $this->model->deleteByWhere($filter);
    }

    public function checkSession($session){
        $filter = array('session'=>$session);
        $sessionRow = $this->model->getByWhere($filter);

        if(!empty($sessionRow)){
            $expireTime = intval($sessionRow['create_time']) + DI ()->config->get ( 'app.login.user_session_time' );
            if( $expireTime < time() ){

                $this->deleteSession($sessionRow['regulator_id']);
                return false;
            }else{

                if(!empty($sessionRow['data'])){
                    $sessionRow['data'] = json_decode($sessionRow['data'],true);
                }
                return $sessionRow;
            }

        }else{
            return false;
        }
    }


}
