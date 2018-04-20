<?php
class Domain_Building_UserAuthRole {
    var $model;
    var $loginTime = 86400;

    public function __construct() {
        $this->model = new Model_Building_UserAuthRole();
    }

  public function getUserAuthRole(){
      $authList = $this->model->getAll('*',array('parent_id' => 0));
      foreach ($authList as $key => $value) {
          //       $firstAuth['info'] = $value;
          $childAuthFilter = array('parent_id' => $value['id']);
          $childAuthList = $this->model->getAll('*', $childAuthFilter);
          foreach ($childAuthList as $k => $v) {
              $actionAuthFilter = array('parent_id' => $v['id']);
              $actionList = $this->model->getAll('*', $actionAuthFilter);
              $v['child'] = $actionList;
              $value['child'][] = $v;
          }
          $firstAuth[] = $value;
      }
      return $firstAuth;
  }


}
