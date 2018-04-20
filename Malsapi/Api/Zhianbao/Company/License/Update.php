<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class  Api_Zhianbao_Company_License_Update extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'licenseId' => array('name' => 'license_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '证照ID'),
                     'typeId' => array('name' => 'type_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '类型ID'),
                     'imgUrl' => array('name' => 'img_url', 'type' => 'array', 'format'=>'json', 'require' => true, 'desc' => '图片路径'),
            ),
		);
 	}
	
  
  /**
     * 更新企业证照
     * #desc 用于更新企业证照
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
        //判断企业证照是否存在
        $licenseDomain = new Domain_Zhianbao_License();
        $info = $licenseDomain->getLicenseInfo($this->licenseId);
        if( !$info) {
            DI()->logger->debug('Company license not exist', $this->licenseId);

            $rs['code'] = 147;
            $rs['msg'] = T('Company license not exist');
            return $rs;
        }

        $data = array(
            'license_id' => $this->licenseId,
            'type_id' => $this->typeId,
            'img_url' => json_encode($this->imgUrl),
            'last_modify' => time(),
        );

        $res = $licenseDomain->updateLicense($data);
        if( $res){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info']['status'] = $status;

        return $rs;
    }
	
}
