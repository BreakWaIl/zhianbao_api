<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_CheckPenalty_Repeal extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'penaltyId' => array('name' => 'penalty_id','type'=>'int','require'=> true,'desc'=> '处罚ID'),
            ),
		);
 	}
  
  /**
   * 作废处罚记录
   * #desc 用于作废处罚记录
   * #return int code 操作码，0表示成功
   * #return int status 结果 0 成功 1 失败
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
        if($planInfo['is_repeal'] == 'y'){
            $rs['code'] = 140;
            $rs['msg'] = T('Penalty have been repeal');
            return $rs;
        }

        $data = array(
            'is_repeal' => 'y',
            'repeal_time' => time(),
        );
        $status = $penaltyDomain->repealCheckPenalty($this->penaltyId,$data);
        if($status){
            $status = 0;
        }else{
            $status = 1;
        }
        $rs['status'] = $status;
        return $rs;
    }
	
}
