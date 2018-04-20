<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Cat_Add extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'name' => array('name' => 'name', 'type' => 'string' , 'min' => 1, 'require' => true, 'desc' => '类别名称'),
                     'legalPerson' => array('name' => 'legal_person', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '联系人'),
                     'telephone' => array('name' => 'telephone', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '公司电话'),
                     'zipCode' => array('name' => 'zip_code', 'type' => 'string', 'require' => false, 'desc' => '邮编'),
                     'province' => array('name' => 'province', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '省份'),
                     'city' => array('name' => 'city', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '城市'),
                     'district' => array('name' => 'district', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '区县'),
                     'address' => array('name' => 'address', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '详细地址'),
                     'operateId' => array('name' => 'operate_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '操作人ID'),
            ),
		);
 	}

  
  /**
     * 添加公司类别
     * #desc 用于添加公司类别
     * #return int cat_id 类别ID
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
        //查询类别名称是否存在
        $catDomain = new Domain_Building_Cat();
        $catInfo = $catDomain->getBaseInfoByName($this->companyId,$this->name);
        if (! empty($catInfo)) {
            DI()->logger->debug('Name exists', $this->name);

            $rs['code'] = 107;
            $rs['msg'] = T('Name exists');
            return $rs;
        }
        if(!empty($this->zipCode)){
            if(!intval($this->zipCode)){
                $rs['code'] = 201;
                $rs['msg'] = T('Format error');
                return $rs;
            }
        }
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $data = array(
                'company_id' => $this->companyId,
                'name' => $this->name,
                'legal_person' => $this->legalPerson,
                'telephone' => $this->telephone,
                'zip_code' => $this->zipCode,
                'province' => $this->province,
                'city' => $this->city,
                'district' => $this->district,
                'address' => $this->address,
                'create_time' => time(),
                'last_modify' => time(),
                'operate_id' => $this->operateId,
            );
            $companyId = $catDomain->add($data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
            return $rs;
        }

        $rs['company_id'] = $companyId;

        return $rs;
    }

}

