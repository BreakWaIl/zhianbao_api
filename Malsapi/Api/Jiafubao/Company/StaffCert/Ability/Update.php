<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_StaffCert_Ability_Update extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'certId' => array('name' => 'cert_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '证书ID'),
                'imgUrl' => array('name' => 'img_url', 'type' => 'array','format'=>'json', 'require' => true, 'desc' => '证书图片:array(array(id,url)),array(id,url)'),
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
     * 更新专项能力证书
     * #desc 用于更新专项能力证书
     * #return int code 操作码，0表示成功
     * #return int status 0 成功 1 失败
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
        //判断技能证书是否存在
        $staffCertDomain = new Domain_Jiafubao_StaffAbilityCert();
        $certInfo = $staffCertDomain->getBaseInfo($this->certId);
        if( !$certInfo) {
            $rs['code'] = 157;
            $rs['msg'] = T('Skill cert not exists');
            return $rs;
        }
        //判断证书是否作废
        if($certInfo['status'] == 'n'){
            $rs['code'] = 166;
            $rs['msg'] = T('Cert have been repeal');
            return $rs;
        }
        $data = array(
            'cert_id' => $this->certId,
            'img_url' => json_encode($this->imgUrl),
            'cert_bn' => $this->certBn,
            'issued' => $this->issued,
            'train_course' => $this->trainCourse,
            'train_time' => strtotime($this->trainTime),
            'train_score' => $this->trainScore,
            'train_organization' => $this->trainOrganization,
            'remark' => $this->remark,
            'is_check' => 'n',
            'last_modify' => time(),
        );
        if($certInfo['type'] == 'society'){
            $data['train_periods'] = $this->trainPeriods;
        }else{
            $data['skill'] = $this->skill;
        }
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $certId = $staffCertDomain->updateAbilityCert($data,$certInfo);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }
        if( $certId){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info']['status'] = $status;

        return $rs;
    }

}
