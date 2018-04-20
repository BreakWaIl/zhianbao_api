<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_StaffCheck_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '员工ID'),
                'imgUrl' => array('name' => 'img_url', 'type' => 'string', 'require' => true, 'desc' => '图片路径'),
                //'certInfo' => array('name' => 'cert_info', 'type' => 'array','format'=>'json', 'require' => true, 'desc' => '证书信息:array(array(type_id,array(id,url)),array(type_id,array(id,url))'),
            ),
        );
    }


    /**
     * 添加体检记录
     * #desc 用于添加体检记录
     * #return int code 操作码，0表示成功
     * #return int check_id  记录ID
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
        //判断员工是否存在
        $staffDomain = new Domain_Zhianbao_Staff();
        $staffInfo = $staffDomain->getBaseInfo($this->staffId);
        if(! $staffInfo){
            $rs['code'] = 126;
            $rs['msg'] = T('Staff not exists');
            return $rs;
        }

        $data = array(
            'company_id' => $this->companyId,
            'staff_id' => $this->staffId,
            'name' => $staffInfo['name'],
            'img_url' => $this->imgUrl,
            'create_time' => time(),
            'last_modify' => time(),
        );
        $checkDomain = new Domain_Zhianbao_StaffCheck();
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $checkId = $checkDomain->addCheck($data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }
        $rs['info']['check_id'] = $checkId;

        return $rs;
    }

}
