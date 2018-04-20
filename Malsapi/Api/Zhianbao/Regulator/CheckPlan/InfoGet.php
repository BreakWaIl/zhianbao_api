<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_CheckPlan_InfoGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'planId' => array('name' => 'plan_id','type'=>'int','require'=> true,'desc'=> '检查计划ID'),
            ),
		);
 	}

  
  /**
     * 获取检查计划详情
     * #desc 用于获取检查计划详情
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //查看隐患项目是否存在
        $planDomain = new Domain_Zhianbao_CheckPlan();
        $planInfo = $planDomain->getBaseInfo($this->planId);
        if(! $planInfo){
            $rs['code'] = 110;
            $rs['msg'] = T('Check plan not exists');
            return $rs;
        }

        $rs['info'] = $planInfo;
        return $rs;
    }

}

