<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_DashBoard_InfoGet extends PhalApi_Api {
    
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
     * #desc 用于获取首页概况
     * #return int code 操作码，0表示成功
     * #return int user_info 用户信息
     * #return int keepCardTotal 待上传的家政证
     * #return int healthCardTotal 待上传的健康卡
     * #return int certTotal 待上传的专项技能卡
     * #return int waitInsuranceTotal 待上传的保险记录
     * #return int orderTotal 市场订单数
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
//        //判断用户是否存在
//        $domainUser = new Domain_Zhianbao_User();
//        $userInfo = $domainUser->getBaseInfo($this->userId);
//        if (empty($userInfo)) {
//            DI()->logger->debug('User not found', $this->userId);
//
//            $rs['code'] = 112;
//            $rs['msg'] = T('User not exists');
//            return $rs;
//        }
        //获取用户
        $domain = new Domain_Jiafubao_User();
        $userInfo = $domain->getBaseByUserId($this->userId);
        if( empty($userInfo)){
            $rs['code'] = 112;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }

        $dashBoardDomain = new Domain_Jiafubao_DashBoard();
        $info = $dashBoardDomain->storeDashBoard($this->companyId);
        $userInfo['create_time'] = date("Y-m-d H:i:s", $userInfo['create_time']);
        $rs['info']['user_info'] = $userInfo;
        $rs['info']['data_info'] = $info;

        return $rs;
    }
    
}
