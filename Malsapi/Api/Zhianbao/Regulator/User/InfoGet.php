<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_User_InfoGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id', 'type' => 'string', 'require' => true, 'desc' => '监管ID'),
            ),
		);
 	}

  
  /**
     * 监管者资料获取
     * #desc 用于监管者的获取
     * #return int user_id 商户ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //检测监管者是否存在
        $regulatorDomain = new Domain_Zhianbao_Regulator();
        $info = $regulatorDomain->getBaseInfo($this->regulatorId);
        if(empty($info)){
            $rs['code'] = 118;
            $rs['msg'] = T('Regulator not found');
            return $rs;
        }
        $rs['info'] = $info;

        return $rs;
    }

}

