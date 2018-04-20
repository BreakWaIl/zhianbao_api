<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_CheckTrouble_InfoGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'troubleId' => array('name' => 'trouble_id','type'=>'int','require'=> true,'desc'=> '事故ID'),
            ),
		);
 	}

  
  /**
     * 获取事故记录详情
     * #desc 用于获取事故记录详情
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //查看隐患项目是否存在
        $troubleDomain = new Domain_Zhianbao_CheckTrouble();
        $troubleInfo = $troubleDomain->getBaseInfo($this->troubleId);
        if(! $troubleInfo){
            $rs['code'] = 121;
            $rs['msg'] = T('Trouble not exists');
            return $rs;
        }

        $rs['info'] = $troubleInfo;
        return $rs;
    }

}

