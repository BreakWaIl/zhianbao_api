<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_CheckTrouble_Delete extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'troubleId' => array('name' => 'trouble_id','type'=>'int','require'=> true,'desc'=> '事故ID'),
            ),
		);
 	}
  
  /**
   * 删除事故记录
   * #desc 用于删除事故记录
   * #return int code 操作码，0表示成功
   * #return int status  0:成功 1:失败
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $planDomain = new Domain_Zhianbao_CheckTrouble();
        $delRs = $planDomain->delCheckTrouble($this->troubleId);
        if($delRs){
            $status = 0;
        }else{
            $status = 1;
        }
        $rs['status'] = $status;
        return $rs;
    }
	
}
