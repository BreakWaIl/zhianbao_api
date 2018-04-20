<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_UrgentPlan_Repeal extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'planId' => array('name' => 'plan_id', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '预案登记ID'),
            ),
		);
 	}
  
  /**
   * 作废应急预案登记
   * #desc 用于作废应急预案登记
   * #return int code 操作码，0表示成功
   * #return int status 结果 0 成功 1 失败
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断应急预案是否存在
        $planDomain = new Domain_Zhianbao_UrgentPlan();
        $planInfo = $planDomain->getBaseInfo($this->planId);
        if( !$planInfo) {
            DI()->logger->debug('Urgent plan not exist', $this->planId);

            $rs['code'] = 127;
            $rs['msg'] = T('Urgent plan not exist');
            return $rs;
        }
        //判断是否已经作废
        if($planInfo['is_repeal'] == 'y'){
            DI()->logger->debug('Urgent plan have been repeal', $this->planId);

            $rs['code'] = 141;
            $rs['msg'] = T('Urgent plan have been repeal');
            return $rs;
        }
        //判断是否申请审核
        if($planInfo['status'] != 'wait'){
            DI()->logger->debug('Apply is already finish', $this->planId);

            $rs['code'] = 128;
            $rs['msg'] = T('Apply is already finish');
            return $rs;
        }

        $data = array(
            'is_repeal' => 'y',
            'repeal_time' => time(),
        );
        $status = $planDomain->repealUrgentPlan($this->planId,$data);
        if($status){
            $status = 0;
        }else{
            $status = 1;
        }
        $rs['status'] = $status;
        return $rs;
    }
	
}
