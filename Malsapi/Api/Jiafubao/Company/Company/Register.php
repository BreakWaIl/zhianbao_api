<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_Company_Register extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'userId' => array('name' => 'user_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
                'name' => array('name' => 'name', 'type' => 'string', 'require' => false, 'desc' => '公司名称'),
                'mobile' => array('name' => 'mobile', 'type'=>'string', 'min' => 11, 'max' => 11,  'require'=> true,'desc'=> '联系方式'),
                'province' => array('name' => 'province','type'=>'int', 'min'=>'1', 'require'=> true,'desc'=> '省份'),
                'city' => array('name' => 'city', 'type'=>'int', 'min'=>'1','require'=> true,'desc'=> '城市'),
                'district' => array('name' => 'district', 'type' => 'int', 'min'=>'1', 'require' => true, 'desc' => '区县'),
            ),
        );
    }


    /**
     * 注册公司
     * #desc 用于注册公司
     * #return int code 操作码，0表示成功
     * #return int company_id  公司ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $data = array(
            'user_id' => $this->userId,
            'name' => $this->name,
            'mobile' => $this->mobile,
            'create_time' => time(),
            'last_modify' => time(),
            'type'=> 'community',
            'source' => 'jfb',
            'province' => $this->province,
            'city' => $this->city,
            'district' => $this->district,
        );
        if(empty($data['name'])){
            $data['name'] = '家政公司';
        }
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $companyDomain = new Domain_Zhianbao_Company();
            $companyId = $companyDomain->register($data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        $rs['company_id'] = $companyId;

        return $rs;
    }

}
