<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_CheckPlan_Get extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id','type'=>'int','require'=> true,'desc'=> '公司ID'),
            ),
		);
 	}

  
  /**
     * 获取公司上次检查计划详情
     * #desc 用于获取公司上次检查计划详情
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //检测公司是否存在
        $companyDomain = new Domain_Zhianbao_Company();
        $companyInfo = $companyDomain->getBaseInfo($this->companyId);
        if(! $companyInfo){
            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }
        $planDomain = new Domain_Zhianbao_CheckPlan();
        $planInfo = $planDomain->getLastCheck($this->companyId);
        $rs['info'] = $planInfo;
        return $rs;
    }

}

