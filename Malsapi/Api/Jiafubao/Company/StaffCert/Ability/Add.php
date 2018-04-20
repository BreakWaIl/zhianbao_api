<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_StaffCert_Ability_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家政员ID'),
                'imgUrl' => array('name' => 'img_url', 'type' => 'array','format'=>'json', 'require' => true, 'desc' => '证书图片:array(array(id,url)),array(id,url)'),
                'type' => array('name' => 'type', 'type' => 'enum', 'range' => array('society','government'), 'default' => 'society', 'require' => true, 'desc' => '证书类型:society 协会  government 政府'),
                'level' => array('name' => 'level', 'type' => 'enum', 'range' => array('1','2','3','4'), 'default' => '1', 'require' => true, 'desc' => '证书等级: 1 专项级 2 初级 3 中级 4 高级'),
                'certBn' => array('name' => 'cert_bn', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '证书编号'),
                'issued' => array('name' => 'issued', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '发证机关'),
                'trainCourse' => array('name' => 'train_course', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '培训内容'),
                'trainTime' => array('name' => 'train_time', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '培训时间'),
                'trainScore' => array('name' => 'train_score', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '培训成绩'),
                'trainOrganization' => array('name' => 'train_organization', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '培训机构'),
                'remark' => array('name' => 'remark', 'type' => 'string', 'require' => false, 'desc' => '备注'),
                'trainPeriods' => array('name' => 'train_periods', 'type' => 'string', 'min' => 1, 'require' => false, 'desc' => '培训课时'),
                'skill' => array('name' => 'skill', 'type' => 'string', 'min' => 1, 'require' => false, 'desc' => '所学技能'),
            ),
        );
    }


    /**
     * 添加专项能力证书
     * #desc 用于添加专项能力证书
     * #return int code 操作码，0表示成功
     * #return int cert_id  证书ID
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
        //判断家政人员是否存在
        $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
        $staffInfo = $houseStaffDomain->getBaseInfo($this->staffId);
        if( !$staffInfo) {
            DI()->logger->debug('Staff not exists', $this->staffId);

            $rs['code'] = 126;
            $rs['msg'] = T('Staff not exists');
            return $rs;
        }
        $data = array(
            'company_id' => $this->companyId,
            'staff_id' => $this->staffId,
            'type' => $this->type,
            'img_url' => json_encode($this->imgUrl),
            'level' => $this->level,
            'cert_bn' => $this->certBn,
            'issued' => $this->issued,
            'remark' => $this->remark,
            'train_course' => $this->trainCourse,
            'train_score' => $this->trainScore,
            'train_time' => strtotime($this->trainTime),
            'train_organization' => $this->trainOrganization,
            'create_time' => time(),
            'last_modify' => time(),
        );
        $staffCertDomain = new Domain_Jiafubao_StaffAbilityCert();
        //检测该等级证书是否存在
        $certInfo = $staffCertDomain->detectAbilityCert($data);
        if(!empty($certInfo)){
            $rs['code'] = 184;
            $rs['msg'] = T('Cert level exists');
            return $rs;
        }
        if($this->type == 'society'){
            $data['train_periods'] = $this->trainPeriods;
        }else{
            $data['skill'] = $this->skill;
        }
        $certId = 0;
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $certId = $staffCertDomain->addAbilityCert($data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        $rs['info']['cert_id'] = $certId;

        return $rs;
    }

}
