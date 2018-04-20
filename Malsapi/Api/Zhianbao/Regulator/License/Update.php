<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_License_Update extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '监管者ID'),
                     'typeId' => array('name' => 'type_id','type'=>'int', 'min' => 1, 'require'=> true,'desc'=> '类型ID'),
                     'name' => array('name' => 'name', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '类型名称'),
            ),
		);
 	}
  
  /**
   * 更新证照类型
   * #desc 用于更新证照类型
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

        //判断证照类型是否存在
        $licenseDomain = new Domain_Zhianbao_License();
        $licenseInfo = $licenseDomain->getBaseInfo($this->typeId);
        if(! $licenseInfo){
            DI()->logger->debug('Cert type not exist', $this->typeId);

            $rs['code'] = 136;
            $rs['msg'] = T('Cert type not exist');
            return $rs;
        }
        $data = array(
            'type_id' => $this->typeId,
            'name' => $this->name,
            'last_modify' => time()
        );
        $updateRs = $licenseDomain->updateLicenseType($data);
        if($updateRs){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info']['status'] = $status;
        return $rs;
    }
	
}
