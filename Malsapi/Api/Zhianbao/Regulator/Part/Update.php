<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Part_Update extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '监管者ID'),
                     'partId' => array('name' => 'part_id','type'=>'int', 'min' => 1, 'require'=> true,'desc'=> '角色ID'),
                     'name' => array('name' => 'name', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '角色名称'),
            ),
		);
 	}
  
  /**
   * 更新员工角色类型
   * #desc 用于更新员工角色类型
   * #return int code 操作码，0表示成功
   * #return int status 状态 0 成功, 1 失败
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

        //检测角色是否已存在
        $partDomain = new Domain_Zhianbao_Part();
        $info = $partDomain->isUser($this->regulatorId,$this->name);
        if(!empty($info)){
            DI()->logger->debug('Being used', $this->name);

            $rs['code'] = 137;
            $rs['msg'] = T('Being used');
            return $rs;
        }

        $data = array(
            'part_id' => $this->partId,
            'name' => $this->name,
            'last_modify' => time()
        );
        $updateRs = $partDomain->updatePart($data);
        if($updateRs){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info']['status'] = $status;
        return $rs;
    }
	
}
