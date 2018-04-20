<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_CheckPenalty_InfoGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'penaltyId' => array('name' => 'penalty_id','type'=>'int','require'=> true,'desc'=> '处罚ID'),
            ),
		);
 	}

  
  /**
     * 获取事故处罚详情
     * #desc 用于获取事故处罚详情
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //查看处罚记录是否存在
        $penaltyDomain = new Domain_Zhianbao_CheckPenalty();
        $planInfo = $penaltyDomain->getBaseInfo($this->penaltyId);
        if(! $planInfo){
            $rs['code'] = 123;
            $rs['msg'] = T('Penalty not exists');
            return $rs;
        }

        $rs['info'] = $planInfo;
        return $rs;
    }

}

