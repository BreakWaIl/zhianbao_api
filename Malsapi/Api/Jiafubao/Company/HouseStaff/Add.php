<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_HouseStaff_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'name' => array('name' => 'name', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '员工姓名'),
                'mobile' => array('name' => 'mobile', 'type'=>'string','max' => 11, 'min' => 11,  'require'=> true,'desc'=> '联系方式'),
                'cardID' => array('name' => 'card_id', 'type'=>'string', 'min' => 15, 'max' => 18, 'require'=> true,'desc'=> '身份证号码'),
                'cardImgP' => array('name' => 'cardImgP', 'type' => 'array', 'format'=>'json', 'require' => false, 'desc' => '身份证正面照片'),
                'cardImgN' => array('name' => 'cardImgN', 'type' => 'array', 'format'=>'json', 'require' => false, 'desc' => '身份证反面照片'),
                'birthday' => array('name' => 'birthday', 'type'=>'string', 'min' => 1,  'require'=> true,'desc'=> '出生日期'),
                'sex' => array('name' => 'sex', 'type'=>'enum','range' => array('boy','girl'), 'default' => 'girl', 'require'=> true,'desc'=> '员工性别'),
                'avatar' => array('name' => 'avatar', 'type' => 'array', 'format'=>'json', 'require' => false, 'desc' => '员工照片'),
                'nation' => array('name' => 'nation', 'type' => 'string', 'min' => 1, 'require' => false, 'desc' => '民族'),
                'industry' => array('name' => 'industry', 'type'=>'string', 'require'=> false,'desc'=> '专业'),
                'nativePlace' => array('name' => 'native_place', 'type'=>'int', 'min' => 0, 'require'=> false, 'desc' => '籍贯'),
                'nativePlaceDistrict' => array('name' => 'native_place_district', 'type' => 'array', 'format'=>'json', 'require' => false, 'desc' => '户籍地区'),
                'nativePlaceAddress' => array('name' => 'native_place_address', 'type'=>'string', 'require'=> false, 'desc' => '户籍详细地址'),
                'isDormitory' => array('name' => 'is_dormitory', 'type'=>'enum','range' => array('y','n','unknown'), 'default' => 'unknown', 'require'=> true,'desc'=> '是否住店:y 是, n 否, unknown 未知'),
                'nowDistrict' => array('name' => 'now_district', 'type' => 'array', 'format'=>'json', 'require' => false, 'desc' => '现居住省市区'),
                'address' => array('name' => 'address','type'=>'string', 'require'=> false,'desc'=> '现居住详细地址'),
                'education' => array('name' => 'education', 'type' => 'string', 'min' => 1, 'require' => false, 'desc' => '文化程度'),
                'marriage' => array('name' => 'marriage', 'type' => 'string', 'min' => 1, 'require' => false, 'desc' => '婚姻情况'),
                'crimeExperience' => array('name' => 'crime_experience', 'type' => 'array', 'format'=>'json', 'require' => false, 'desc' => '犯罪经历'),
                'demand' => array('name' => 'demand', 'type'=>'array', 'format'=>'json', 'require'=> false,'desc'=> '工作范围'),
                'salary' => array('name' => 'salary', 'type'=>'string', 'require'=> false,'desc'=> '期望薪酬'),
                'goodCuisine' => array('name' => 'good_cuisine', 'type' => 'array', 'format'=>'json', 'min' => 1, 'require'=> false,'desc'=> '擅长菜系'),
                'cookTaste' => array('name' => 'cook_taste', 'type'=>'string', 'require'=> false,'desc'=> '做饭口味'),
                'isHome' => array('name' => 'is_home', 'type'=>'enum','range' => array('y','n'), 'default' => 'n', 'require'=> false,'desc'=> '是否住家:y 住家 n 不住家'),
                'workTime' => array('name' => 'work_time', 'type' => 'array', 'format'=>'json', 'require' => false, 'desc' => '是否全职、兼职（时间段）'),
                'workPicture' => array('name' => 'work_picture', 'type' => 'array', 'format'=>'json', 'require' => false, 'desc' => '工作照'),
                'workExperience' => array('name' => 'work_experience', 'type' => 'string', 'require' => false, 'desc' => '家政经验'),
                'height' => array('name' => 'height', 'type'=>'string',   'require'=> false,'desc'=> '身高'),
                'weight' => array('name' => 'weight', 'type'=>'string',   'require'=> false,'desc'=> '体重'),
            ),
        );
    }


    /**
     * 添加家政员工信息
     * #desc 用于添加家政员工信息
     * #return int code 操作码，0表示成功
     * #return int staff_id  家政员工ID
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
        //检测身份证号码是否存在
        $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
        $cardInfo = $houseStaffDomain->hashCardID($this->companyId,$this->cardID);
        if (!empty($cardInfo)) {
            $rs['code'] = 155;
            $rs['msg'] = T('ID card exist');
            return $rs;
        }
        if(!empty($this->nativePlace)){
            $native_place = $this->nativePlace;
        }else{
            $native_place = 0;
        }
        $data = array(
            'company_id' => $this->companyId,
            'name' => $this->name,
            'birthday' => strtotime($this->birthday),
            'sex' => $this->sex,
            'avatar' => json_encode($this->avatar),
            'mobile' => $this->mobile,
            'cardID' => $this->cardID,
            'crime_experience' => json_encode($this->crimeExperience),
            'is_check' => 'n',
            'create_time' => time(),
            'last_modify' => time(),
            'nation' => $this->nation,
            'education' => $this->education,
            'marriage' => $this->marriage,
            'industry' => $this->industry,
            'native_place' => $native_place,
            'native_place_address' => $this->nativePlaceAddress,
            'is_dormitory' => $this->isDormitory,
            'now_district' => json_encode($this->nowDistrict),
            'address' => $this->address,
            'idcard_p' => json_encode($this->cardImgP),
            'idcard_n' => json_encode($this->cardImgN),
            'work_experience' => $this->workExperience,
            'height' => $this->height,
            'weight' => $this->weight,
        );
        //业务需求
        $demand_data = array(
            'company_id' => $this->companyId,
            'demand' => json_encode($this->demand),
            'expected_salary' => $this->salary,
            'good_cuisine' => json_encode($this->goodCuisine),
            'cook_taste' => $this->cookTaste,
            'is_home' => $this->isHome,
            'work_time' => json_encode($this->workTime),
            'create_time' => time(),
            'last_modify' => time(),
            'work_picture' => json_encode($this->workPicture),
        );
        if(empty($this->nowDistrict['province'])){
            $data['now_district'] = '';
        }
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $staffId = $houseStaffDomain->addHouseStaff($data,$demand_data);
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
