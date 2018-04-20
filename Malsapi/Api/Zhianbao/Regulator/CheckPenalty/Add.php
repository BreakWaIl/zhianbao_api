<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_CheckPenalty_Add extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
                     'troubleId' => array('name' => 'trouble_id','type'=>'int','require'=> true,'desc'=> '事故ID'),
                     'title' => array('name' => 'title','type'=>'string','require'=> true,'desc'=> '处罚标题'),
                     'content' => array('name' => 'content', 'type'=>'string',  'require'=> false,'desc'=> '处罚内容'),
                     'amount' => array('name' => 'amount', 'type'=>'float',  'require'=> true,'desc'=> '处罚金额'),
                     'reason' => array('name' => 'reason', 'type'=>'string',  'require'=> true,'desc'=> '处罚原因'),
                     'apartment' => array('name' => 'apartment', 'type'=>'string',  'require'=> true,'desc'=> '处罚部门'),
                     'mark' => array('name' => 'mark', 'type'=>'string',  'require'=> false,'desc'=> '备注'),
            ),
		);
 	}
  
  /**
   * 添加处罚记录
   * #desc 用于添加处罚记录
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
        //查看事故是否存在
        $planDomain = new Domain_Zhianbao_CheckTrouble();
        $planInfo = $planDomain->getBaseInfo($this->troubleId);
        if(! $planInfo){
            $rs['code'] = 122;
            $rs['msg'] = T('Report not exists');
            return $rs;
        }

        $penaltyDomain = new Domain_Zhianbao_CheckPenalty();
        $data = array(
            'trouble_id' => $this->troubleId,
            'title' => $this->title,
            'content' => $this->content,
            'amount' => $this->amount,
            'reason' => $this->reason,
            'apartment' => $this->apartment,
            'mark' => $this->mark,
            'create_time' => time(),
            'last_modify' => time(),
            'operat_id' => $this->regulatorId,
        );

        $addRs = $penaltyDomain->addCheckPenalty($data);
        $rs['check_penalty_id'] = $addRs;
        return $rs;
    }
	
}
