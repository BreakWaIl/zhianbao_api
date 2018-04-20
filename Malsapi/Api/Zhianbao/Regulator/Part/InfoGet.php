<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Part_InfoGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '监管者ID'),
                     'partId' => array('name' => 'part_id','type'=>'int', 'min' => 1, 'require'=> true,'desc'=> '角色ID'),
            ),
		);
 	}

  
  /**
     * 获取人员角色类型
     * #desc 用于获取人员角色类型
     * #return int code 操作码，0表示成功
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

        //判断角色类型是否存在
        $partDomain = new Domain_Zhianbao_Part();
        $partInfo = $partDomain->getBaseInfo($this->partId);
        if(! $partInfo){
            DI()->logger->debug('Role not exist', $this->partId);

            $rs['code'] = 150;
            $rs['msg'] = T('Role not exist');
            return $rs;
        }

        $rs['info'] = $partInfo;
        return $rs;
    }

}

