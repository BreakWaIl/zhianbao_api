<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_CheckPenalty_Update extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
                     'penaltyId' => array('name' => 'penalty_id','type'=>'int','require'=> true,'desc'=> '处罚ID'),
                     'title' => array('name' => 'title','type'=>'string','require'=> true,'desc'=> '事故标题'),
                     'content' => array('name' => 'content', 'type'=>'string',  'require'=> false,'desc'=> '事故内容'),
                     'amount' => array('name' => 'amount', 'type'=>'float',  'require'=> true,'desc'=> '处罚金额'),
                     'reason' => array('name' => 'reason', 'type'=>'string',  'require'=> true,'desc'=> '处罚原因'),
                     'apartment' => array('name' => 'apartment', 'type'=>'string',  'require'=> true,'desc'=> '处罚部门'),
                     'mark' => array('name' => 'mark', 'type'=>'string',  'require'=> false,'desc'=> '备注'),
            ),
		);
 	}
  
  /**
   * 更新处罚记录
   * #desc 用于更新处罚记录
   * #return int code 操作码，0表示成功
   * #return int id  客户id
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
            'title' => $this->title,
            'content' => $this->content,
            'amount' => $this->amount,
            'reason' => $this->reason,
            'apartment' => $this->apartment,
            'mark' => $this->mark,
            'last_modify' => time(),
            'operat_id' => $this->regulatorId,
        );
        $status = $penaltyDomain->updateCheckPenalty($this->penaltyId,$data);
        if($status){
            $status = 0;
        }else{
            $status = 1;
        }
        $rs['status'] = $status;
        return $rs;
    }
	
}
