<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Cert_Type_Update extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '监管者ID'),
                     'typeId' => array('name' => 'type_id','type'=>'int', 'min' => 1, 'require'=> true,'desc'=> '类型ID'),
                     'partId' => array('name' => 'part_id', 'type' => 'string', 'require' => true, 'desc' => '角色ID,多个用“,”隔开'),
                     'name' => array('name' => 'name', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '证书名称'),
            ),
		);
 	}
  
  /**
   * 更新员工证书类型
   * #desc 用于更新员工证书类型
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

        //判断证书是否存在
        $certTypeDomain = new Domain_Zhianbao_CertType();
        $certTypeInfo = $certTypeDomain->getBaseInfo($this->typeId);
        if(! $certTypeInfo){
            DI()->logger->debug('Cert type not exist', $this->typeId);

            $rs['code'] = 136;
            $rs['msg'] = T('Cert type not exist');
            return $rs;
        }

        $data = array(
            'regulator_id' => $this->regulatorId,
            'type_id' => $this->typeId,
            'part_id' => $this->partId,
            'name' => $this->name,
            'last_modify' => time(),
        );

        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $updateRs = $certTypeDomain->updateCertType($data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
            return $rs;
        }
        if($updateRs){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info']['status'] = $status;
        return $rs;
    }
	
}
