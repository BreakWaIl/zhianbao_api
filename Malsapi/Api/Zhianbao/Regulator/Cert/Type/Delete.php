<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Cert_Type_Delete extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '监管者ID'),
                     'typeId' => array('name' => 'type_id','type'=>'int', 'min' => 1, 'require'=> true,'desc'=> '类型ID'),
            ),
		);
 	}
  
  /**
   * 删除员工证书类型
   * #desc 用于员工证书类型
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

        //检测该类型是否正在使用
        $info = $certTypeDomain->isUser($this->regulatorId,$this->typeId);
        if(!empty($info)){
            DI()->logger->debug('Being used', $this->typeId);

            $rs['code'] = 137;
            $rs['msg'] = T('Being used');
            return $rs;
        }

        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $delRs = $certTypeDomain->deleteCertType($this->regulatorId,$this->typeId);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
            return $rs;
        }
        if($delRs){
            $status = 0;
        }else{
            $status = 1;
        }
        $rs['status'] = $status;
        return $rs;
    }
	
}
