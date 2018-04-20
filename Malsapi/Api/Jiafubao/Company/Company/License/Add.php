<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_Company_License_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'name' => array('name' => 'name', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '执照名称'),
                'licenseInfo' => array('name' => 'licenseInfo', 'type' => 'array','format'=>'json', 'require' => true, 'desc' => '执照图片:array(array(id,url)),array(id,url)'),
            ),
        );
    }


    /**
     * 添加公司执照
     * #desc 用于添加公司执照
     * #return int code 操作码，0表示成功
     * #return int license_id  执照ID
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

        $data = array(
            'company_id' => $this->companyId,
            'name' => $this->name,
            'img_url' => json_encode($this->licenseInfo),
            'create_time' => time(),
            'last_modify' => time(),
        );
        $licenseDomain = new Domain_Jiafubao_CompanyLicense();
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
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
