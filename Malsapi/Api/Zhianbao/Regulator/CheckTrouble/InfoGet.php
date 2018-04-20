<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_CheckTrouble_InfoGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'troubleId' => array('name' => 'trouble_id','type'=>'int','require'=> true,'desc'=> '事故ID'),
            ),
		);
 	}

  
  /**
     * 获取事故详情
     * #desc 用于获取事故详情
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //查看隐患项目是否存在
        $planDomain = new Domain_Zhianbao_CheckTrouble();
        $planInfo = $planDomain->getBaseInfo($this->troubleId);
        if(! $planInfo){
            $rs['code'] = 122;
            $rs['msg'] = T('Report not exists');
            return $rs;
        }

        $rs['info'] = $planInfo;
        return $rs;
    }

}

