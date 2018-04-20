<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_UrgentPlan_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
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
        //检测监管者是否存在
        $regulatorDomain = new Domain_Zhianbao_Regulator();
        $regulatorInfo = $regulatorDomain->getBaseInfo($this->regulatorId);
        if(! $regulatorInfo){
            $rs['code'] = 118;
            $rs['msg'] = T('Regulator not exists');
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
