<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_DashBoard_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'userId' => array('name' => 'user_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
            ),
        );
    }
  
  /**
     * 获取首页概况
     * #desc 用于获取当前发文通知信息详情
     * #return int code 操作码，0表示成功
     * #return int user_info 用户信息
     * #return array data_info 统计数据
     * #return int noticeTotal 待签收发文通知数
     * #return int staffTotal 待上传人员
     * #return int troubleTotal 待处罚记录
     * #return array checkPlanInfo 隐患排除与整改
     * #return int safeSelf 待上级复核自评数
  */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断公司是否存在
        $domainCompany = new Domain_Zhianbao_Company();
        $companyInfo = $domainCompany->getBaseInfo($this->companyId);
        $companyInfo['create_time'] = date("Y-m-d H:i:s", $companyInfo['create_time']);
        if (empty($companyInfo)) {
            DI()->logger->debug('Company not exists', $this->companyId);

            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }
        //判断用户是否存在
        $domainUser = new Domain_Zhianbao_User();
        $userInfo = $domainUser->getBaseInfo($this->userId);
        $userInfo['last_visit_time'] = date("Y-m-d H:i:s", $userInfo['last_visit_time']);
        if (empty($userInfo)) {
            DI()->logger->debug('Company not exists', $this->userId);

            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }

        $dashBoardDomain = new Domain_Zhianbao_DashBoard();
        $info = $dashBoardDomain->getBaseInfo($this->companyId);
        $rs['info']['user_info'] = $userInfo;
        $rs['info']['data_info'] = $info;

        return $rs;
    }
    
}
