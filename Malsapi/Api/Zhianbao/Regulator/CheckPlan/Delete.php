<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_CheckPlan_Delete extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'planId' => array('name' => 'plan_id','type'=>'int','require'=> true,'desc'=> '检查计划ID'),
            ),
		);
 	}
  
  /**
   * 删除检查计划
   * #desc 用于删除检查计划
   * #return int code 操作码，0表示成功
   * #return int status  0:成功 1:失败
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $planDomain = new Domain_Zhianbao_CheckPlan();
        $delRs = $planDomain->delCheckPlan($this->planId);
        if($delRs){
            $status = 0;
        }else{
            $status = 1;
        }
        $rs['status'] = $status;
        return $rs;
    }
	
}
