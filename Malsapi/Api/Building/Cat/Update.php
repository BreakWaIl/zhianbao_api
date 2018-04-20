<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Cat_Update extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'catId' => array('name' => 'cat_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司类别ID'),
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
     * 更新公司类别信息
     * #desc 用于更新公司类别信息
     * #return int status 状态 0 成功, 1 失败
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
        //判断公司类别是否存在
        $catDomain = new Domain_Building_Cat();
        $catInfo = $catDomain->getBaseInfo($this->catId);
        if (empty($catInfo)) {
            $rs['code'] = 106;
            $rs['msg'] = T('Categroy not exists');
            return $rs;
        }
        if($catInfo['name'] != $this->name){
            //查询公司名是否注册
            $catInfo = $catDomain->getBaseInfoByName($this->companyId,$this->name);
            if (! empty($catInfo)) {
                DI()->logger->debug('Name exists', $this->name);

                $rs['code'] = 107;
                $rs['msg'] = T('Name exists');
                return $rs;
            }
        }
        if(!empty($this->zipCode)){
            if(!intval($this->zipCode)){
                $rs['code'] = 201;
                $rs['msg'] = T('Format error');
                return $rs;
            }
        }
        $data = array(
            'cat_id' => $this->catId,
            'name' => $this->name,
            'legal_person' => $this->legalPerson,
            'telephone' => $this->telephone,
            'zip_code' => $this->zipCode,
            'province' => $this->province,
            'city' => $this->city,
            'district' => $this->district,
            'address' => $this->address,
            'last_modify' => time(),
            'operate_id' => $this->operateId,
        );
        $res = $catDomain->update($data);
        if( $res){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info']['status'] = $status;

        return $rs;
    }

}

