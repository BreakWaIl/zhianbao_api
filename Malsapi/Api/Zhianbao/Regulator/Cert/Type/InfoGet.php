<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Cert_Type_InfoGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '监管者ID'),
                     'typeId' => array('name' => 'type_id','type'=>'int', 'min' => 1, 'require'=> true,'desc'=> '类型ID'),
            ),
		);
 	}

  
  /**
     * 获取员工证书类型详情
     * #desc 用于获取员工证书类型详情
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

        $rs['info'] = $certTypeInfo;
        return $rs;
    }

}

