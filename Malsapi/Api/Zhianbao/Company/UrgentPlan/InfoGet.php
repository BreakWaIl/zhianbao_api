<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_UrgentPlan_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'planId' => array('name' => 'plan_id', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '预案ID'),
            ),
        );
    }
  
  /**
     * 获取应急预案详情
     * #desc 用于获取应急预案详情
     * #return int code 操作码，0表示成功
  */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断公司是否存在
        $domainCompany = new Domain_Zhianbao_Company();
        $companyInfo = $domainCompany->getBaseInfo($this->companyId);
        if (empty($companyInfo)) {
            DI()->logger->debug('Company not exists', $this->companyId);

            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }

        //判断应急预案是否存在
        $planDomain = new Domain_Zhianbao_UrgentPlan();
        $planInfo = $planDomain->getBaseInfo($this->planId);
        if( !$planInfo) {
            DI()->logger->debug('Urgent plan not exist', $this->planId);

            $rs['code'] = 127;
            $rs['msg'] = T('Urgent plan not exist');
            return $rs;
        }

        $rs['info'] = $planInfo;

        return $rs;
    }
    
}
