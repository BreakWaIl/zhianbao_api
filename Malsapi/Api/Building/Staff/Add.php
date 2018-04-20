<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Staff_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'projectId' => array('name' => 'project_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '项目ID'),
                'catId' => array('name' => 'cat_id', 'type' => 'array', 'format'=>'json', 'require' => true, 'desc' => '类别ID'),
                'name' => array('name' => 'name', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '员工姓名'),
                'birthday' => array('name' => 'birthday', 'type'=>'string', 'min' => 1,  'require'=> true,'desc'=> '出生日期'),
                'sex' => array('name' => 'sex', 'type'=>'enum','range' => array('boy','girl'), 'default' => 'boy', 'require'=> true,'desc'=> '员工性别'),
                'cardID' => array('name' => 'card_id', 'type'=>'string', 'min' => 15, 'max' => 18, 'require'=> true,'desc'=> '身份证号码'),
                'nation' => array('name' => 'nation', 'type' => 'string',  'require' => false, 'desc' => '民族'),
                'marriage' => array('name' => 'marriage', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '婚姻情况'),
                'education' => array('name' => 'education', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '文化程度'),
                'industry' => array('name' => 'industry', 'type' => 'array', 'format'=>'json', 'min' => 1, 'require'=> true,'desc'=> '专业'),
                'nativePlace' => array('name' => 'native_place', 'type'=>'int', 'min' => 1, 'require'=> true, 'desc' => '籍贯'),
                'nativePlaceDistrict' => array('name' => 'native_place_district', 'type' => 'array', 'format'=>'json', 'require' => true, 'desc' => '户籍地区'),
                'nativePlaceAddress' => array('name' => 'native_place_address', 'type'=>'string', 'min' => 1, 'require'=> true, 'desc' => '户籍详细地址'),
                'mobile' => array('name' => 'mobile', 'type'=>'string','max' => 11, 'min' => 0, 'require'=> false,'desc'=> '联系方式'),
                'avatar' => array('name' => 'avatar', 'type' => 'array', 'format'=>'json', 'require' => false, 'desc' => '员工照片'),
                'nowDistrict' => array('name' => 'now_district', 'type' => 'array', 'format'=>'json', 'require' => false, 'desc' => '现居住省市区'),
                'nowAddress' => array('name' => 'now_address','type'=>'string', 'require'=> false,'desc'=> '现居住地址'),
                'operateId' => array('name' => 'operate_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '操作人ID'),
            ),
        );
    }


    /**
     * 添加建筑员工信息
     * #desc 用于添加建筑员工信息
     * #return int code 操作码，0表示成功
     * #return int staff_id 员工ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断公司是否存在
        $domainCompany = new Domain_Zhianbao_Company();
        $companyInfo = $domainCompany->getBaseInfo($this->companyId);
        if (empty($companyInfo)) {
            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }
        //判断公司项目是否存在
        $projectDomain = new Domain_Building_Project();
        $projectInfo = $projectDomain->getBaseInfo($this->projectId);
        if (empty($projectInfo)) {
            $rs['code'] = 192;
            $rs['msg'] = T('Project not exists');
            return $rs;
        }
        //判断项目是否完成
        if($projectInfo['status'] == 'finish'){
            $rs['code'] = 211;
            $rs['msg'] = T('Project finish');
            return $rs;
        }
        //判断公司类别是否存在
        $staffDomain = new Domain_Building_Staff();
        $catInfo = $staffDomain->checkCatId($this->catId);
        if (empty($catInfo)) {
            $rs['code'] = 106;
            $rs['msg'] = T('Categroy not exists');
            return $rs;
        }
        //检测项目和分类关系
        $filter = array('company_id' => $this->companyId, 'project_id' => $this->projectId, 'cat_id' => $this->catId);
        $projectDomain = new Domain_Building_Project();
        $info = $projectDomain->checkProjectToCat($filter);
        if( !$info){
            $rs['code'] = 212;
            $rs['msg'] = T('No cat in the project');
            return $rs;
        }

        $data = array(
            'company_id' => $this->companyId,
            'project_id' => $this->projectId,
//            'cat_id' => json_encode($this->catId),
            'cat_id' => $this->catId,
            'name' => $this->name,
            'birthday' => $this->birthday,
            'sex' => $this->sex,
            'avatar' => json_encode($this->avatar),
            'mobile' => $this->mobile,
            'cardID' => $this->cardID,
            'nation' => $this->nation,
            'marriage' => $this->marriage,
            'education' => $this->education,
            'industry' => json_encode($this->industry),
            'native_place' => $this->nativePlace,
            'native_place_district' => json_encode($this->nativePlaceDistrict),
            'native_place_address' => $this->nativePlaceAddress,
            'now_district' => json_encode($this->nowDistrict),
            'now_address' => $this->nowAddress,
            'create_time' => time(),
            'last_modify' => time(),
            'operate_id' => $this->operateId,
        );
//print_r($data);exit;
        $staffId = 0;
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $staffId = $staffDomain->addStaff($data,$projectInfo);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        $rs['info']['staff_id'] = $staffId;

        return $rs;
    }

}
