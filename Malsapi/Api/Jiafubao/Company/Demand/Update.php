<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_Demand_Update extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '家政员ID'),
                'demand' => array('name' => 'demand', 'type'=>'array', 'format'=>'json', 'require'=> true,'desc'=> '工作范围'),
                'salary' => array('name' => 'salary', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '期望薪酬'),
                'goodCuisine' => array('name' => 'good_cuisine', 'type' => 'array', 'format'=>'json', 'min' => 1, 'require'=> true,'desc'=> '擅长菜系'),
                'cookTaste' => array('name' => 'cook_taste', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '做饭口味'),
                'isHome' => array('name' => 'is_home', 'type'=>'enum','range' => array('y','n'), 'default' => 'n', 'require'=> true,'desc'=> '是否住家:y 住家 n 不住家'),
                'workTime' => array('name' => 'work_time', 'type' => 'array', 'format'=>'json', 'require' => true, 'desc' => '是否全职、兼职（时间段）'),
            ),
        );
    }


    /**
     * 添加家政员业务需求信息
     * #desc 用于添加家政员业务需求信息
     * #return int code 操作码，0表示成功
     * #return int demand_id  需求ID
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
            'demand' => json_encode($this->demand),
            'expected_salary' => $this->salary,
            'good_cuisine' => json_encode($this->goodCuisine),
            'cook_taste' => $this->cookTaste,
            'is_home' => $this->isHome,
            'work_time' => json_encode($this->workTime),
            'create_time' => time(),
            'last_modify' => time(),
        );
        $demandDomain = new Domain_Jiafubao_StaffDemand();
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $id = $demandDomain->updateDemand($data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        $rs['info']['demand_id'] = $id;

        return $rs;
    }

}
