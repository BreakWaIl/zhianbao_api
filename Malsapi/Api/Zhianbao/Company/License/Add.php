<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_License_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'typeId' => array('name' => 'type_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '类型ID'),
                'imgUrl' => array('name' => 'img_url', 'type' => 'array', 'format'=>'json', 'require' => true, 'desc' => '图片路径'),
            ),
        );
    }


    /**
     * 添加企业证照
     * #desc 用于添加企业证照
     * #return int code 操作码，0表示成功
     * #return int license_id  证照ID
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
        //判断证照类型是否存在
        $licenseDomain = new Domain_Zhianbao_License();
        $licenseInfo = $licenseDomain->getBaseInfo($this->typeId);
        if(! $licenseInfo){
            DI()->logger->debug('Cert type not exist', $this->typeId);

            $rs['code'] = 136;
            $rs['msg'] = T('Cert type not exist');
            return $rs;
        }

        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $data = array(
                'company_id' => $this->companyId,
                'type_id' => $this->typeId,
                'img_url' => json_encode($this->imgUrl),
                'create_time' => time(),
                'last_modify' => time(),
            );
            $licenseId = $licenseDomain->addLicense($data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        $rs['info']['license_id'] = $licenseId;

        return $rs;
    }

}
