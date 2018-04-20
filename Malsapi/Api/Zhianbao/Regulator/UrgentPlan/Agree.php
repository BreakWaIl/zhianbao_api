<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_UrgentPlan_Agree extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
                     'planId' => array('name' => 'plan_id', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '预案ID'),
            ),
        );
    }
  
  /**
     * 同意审核应急预案
     * #desc 用于同意应急预案
     * #return int code 操作码，0表示成功
     * #return int status 状态 0 成功, 1 失败
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
        //判断是否已经作废
        if($planInfo['is_repeal'] == 'y'){
            DI()->logger->debug('Urgent plan have been repeal', $this->planId);

            $rs['code'] = 141;
            $rs['msg'] = T('Urgent plan have been repeal');
            return $rs;
        }
        if($planInfo['status'] == 'finish'){
            DI()->logger->debug('Apply is already finish', $this->planId);

            $rs['code'] = 128;
            $rs['msg'] = T('Apply is already finish');
            return $rs;
        }
        if($planInfo['status'] == 'failure'){
            DI()->logger->debug('Apply failed', $this->planId);

            $rs['code'] = 116;
            $rs['msg'] = T('Apply failed');
            return $rs;
        }

        $res = $planDomain->Agree($planInfo);
        if( $res){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['status'] = $status;

        return $rs;
    }
    
}
