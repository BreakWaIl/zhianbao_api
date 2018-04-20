<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_CheckTrouble_Update extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'troubleId' => array('name' => 'trouble_id','type'=>'int','require'=> true,'desc'=> '事故ID'),
                     'title' => array('name' => 'title','type'=>'string','require'=> true,'desc'=> '事故标题'),
                     'content' => array('name' => 'content', 'type'=>'string',  'require'=> true,'desc'=> '事故内容'),
            ),
		);
 	}
  
  /**
   * 更新事故
   * #desc 用于更新事故
   * #return int code 操作码，0表示成功
   * #return int id  客户id
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //查看事故是否存在
        $planDomain = new Domain_Zhianbao_CheckTrouble();
        $planInfo = $planDomain->getBaseInfo($this->troubleId);
        if(! $planInfo){
            $rs['code'] = 122;
            $rs['msg'] = T('Report not exists');
            return $rs;
        }

        $data = array(
            'title' => $this->title,
            'content' => $this->content,
            'last_modify' => time(),
        );
        $status = $planDomain->updateCheckTrouble($this->troubleId,$data);
        if($status){
            $status = 0;
        }else{
            $status = 1;
        }
        $rs['status'] = $status;
        return $rs;
    }
	
}
