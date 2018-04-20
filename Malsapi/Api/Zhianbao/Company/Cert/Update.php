<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class  Api_Zhianbao_Company_Cert_Update extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'safeId' => array('name' => 'safe_id', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '人员证书ID'),
                     'typeId' => array('name' => 'type_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '类型ID'),
                     'imgUrl' => array('name' => 'img_url', 'type' => 'array', 'format'=>'json', 'require' => true, 'desc' => '图片路径'),
            ),
		);
 	}
	
  
  /**
     * 更新人员证件
     * #desc 用于更新人员证件
     * #return int code 操作码，0表示成功
     * #return int status 状态 0 成功, 1 失败
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断公司是否存在
        $domainCompany = new Domain_Zhianbao_Company();
        $companyInfo = $domainCompany->getBaseInfo($this->companyId);
        if (empty($companyInfo)) {
            DI()->logger->debug('Company not exists', $this->companyId);

            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }
        //判断人员证件是否存在
        $certDomain = new Domain_Zhianbao_Cert();
        $info = $certDomain->getBaseInfo($this->safeId);
        if( !$info) {
            DI()->logger->debug('Safe cert not found', $this->safeId);

            $rs['code'] = 115;
            $rs['msg'] = T('Safe cert not exists');
            return $rs;
        }

        $data = array(
            'safe_id' => $this->safeId,
            'type_id' => $this->typeId,
            'img_url' => json_encode($this->imgUrl),
            'last_modify' => time(),
        );

        $res = $certDomain->updateCert($data,$info['staff_id']);
        if( $res){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info']['status'] = $status;

        return $rs;
    }
	
}
