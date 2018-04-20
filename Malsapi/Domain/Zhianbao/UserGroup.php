<?php
class Domain_Zhianbao_UserGroup {
	var $model;

	public function __construct() {
		$this->model = new Model_Zhianbao_UserGroup ();
	}

	//获取通知详情
    public function getBaseInfo($groupId, $cols = '*'){
       $rs = $this->model->get($groupId,$cols);
       $rs['role_list'] = $this->getAuthRole($groupId);
        return $rs;
    }
    //获取列表
	public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
		$rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
		return $rs;
	}
	//获取数量
	public function getCount($filter) {
		return $this->model->getCount ( $filter );
	}

	//添加用户组
    public function addUserGroup($data){
	    $rs = $this->model->insert($data);
	    return $rs;
    }
    //删除用户组
    public function delUserGroup($groupId){
        //判断该分组下是否有用户
        $userModel = new Model_Zhianbao_User();
        $filter = array('group_id' => $groupId);
        $user = $userModel->getByWhere($filter);
        if($user){
            return false;
        }
        //删除用户组
        $rs = $this->model->delete($groupId);
        return $rs;
    }
    //更新用户组
    public function updateUserGroup($groupId,$data){
        $rs = $this->model->update($groupId,$data);
        return $rs;
    }
    //获取用户组的权限
    public function getAuthRole($groupId)
    {
        //获取权限
        $firstAuth = array();
        $groupModel = new Model_Zhianbao_UserGroup();
        $userAuthModel = new Model_Jiafubao_UserAuthRole();
        $groupInfo = $groupModel->get($groupId);
        if ($groupInfo) {
            //$authArray = explode(',', $groupInfo['role']);
            $authArray = json_decode($groupInfo['role'],true);
            $authFilter = array('id' => $authArray, 'parent_id' => 0);
            $authList = $userAuthModel->getAll('*', $authFilter);
            foreach ($authList as $key => $value) {
                //       $firstAuth['info'] = $value;
                $childAuthFilter = array('id' => $authArray, 'parent_id' => $value['id']);
                $childAuthList = $userAuthModel->getAll('*', $childAuthFilter);
                foreach ($childAuthList as $k => $v) {
                    $actionAuthFilter = array('id' => $authArray, 'parent_id' => $v['id']);
                    $actionList = $userAuthModel->getAll('*', $actionAuthFilter);
                    $v['child'] = $actionList;
                    $value['child'][] = $v;
                }
                $firstAuth[] = $value;
            }
        }
       return $firstAuth;
    }


}
