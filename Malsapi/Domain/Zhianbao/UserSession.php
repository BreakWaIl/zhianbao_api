<?php
class Domain_Zhianbao_UserSession {
    var $model;
    var $loginTime = 86400;

    public function __construct() {
        $this->model = new Model_Zhianbao_UserSession();
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
			'user_id' => $userId,
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
        $filter = array('user_id'=>$userId);
        $this->model->deleteByWhere($filter);
    }

    public function checkSession($session){
        $companyModel = new Model_Zhianbao_Company();
        $filter = array('session'=>$session);
        $sessionRow = $this->model->getByWhere($filter);

        if(!empty($sessionRow)){
            $expireTime = intval($sessionRow['create_time']) + DI ()->config->get ( 'app.login.user_session_time' );
            if( $expireTime < time() ){

                $this->deleteSession($sessionRow['user_id']);
                return false;
            }else{

                if(!empty($sessionRow['data'])){
                    $sessionRow['data'] = json_decode($sessionRow['data'],true);
                }
                $userDomain = new Domain_Zhianbao_User();
                $userInfo = $userDomain->getBaseInfo($sessionRow['user_id'],'parent_id');
                $sessionRow['parent_id'] = $userInfo['parent_id'];
                //子账号登录
                if($userInfo['parent_id'] == 0){
                    $companyFilter = array('user_id' => $sessionRow['user_id']);
                    $companyInfo = $companyModel->getByWhere($companyFilter);
                    $sessionRow['company_id'] = $companyInfo['id'];
                }else{
                    $companyFilter = array('user_id' => $sessionRow['parent_id']);
                    $companyInfo = $companyModel->getByWhere($companyFilter);
                    $sessionRow['company_id'] = $companyInfo['id'];
                }
                return $sessionRow;
            }

        }else{
            return false;
        }
    }
    //判断接口权限
    public function checkApiAuth($userId,$service){
        $userModel = new Model_Zhianbao_User();
        $groupModel = new Model_Zhianbao_UserGroup();
        $authRoleModel = new Model_Jiafubao_UserAuthRole();
        $userInfo = $userModel->get($userId);
        if($userInfo['group_id'] == 0){
            return true;
        }
        $groupInfo = $groupModel->get($userInfo['group_id']);
        if($groupInfo) {
           // $authArray = explode(',', $groupInfo['role']);
            $authArray = json_decode($groupInfo['role'],true);
            $authFilter = array('id' => $authArray,'parent_id' => 0);
            $authList = $authRoleModel->getAll('*',$authFilter);
            foreach ($authList as $key => $value){
                //       $firstAuth['info'] = $value;
                $apiAuth = explode(',',$value['api_name']);
                if(in_array($service,$apiAuth)){
                    return true;
                }
            }
        }
        return false;
    }

}
