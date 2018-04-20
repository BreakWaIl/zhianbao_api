<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class  Api_Building_Staff_Update extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
//                     'catId' => array('name' => 'cat_id', 'type' => 'array', 'format'=>'json', 'require' => true, 'desc' => '类别ID'),
                     'staffId' => array('name' => 'staff_id', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '员工ID'),
                     'name' => array('name' => 'name', 'type'=>'string', 'min' => 1, 'require'=> true,'desc'=> '员工姓名'),
                     'birthday' => array('name' => 'birthday', 'type'=>'string', 'min' => 1,  'require'=> true,'desc'=> '出生日期'),
                     'avatar' => array('name' => 'avatar', 'type' => 'array', 'format'=>'json', 'require' => false, 'desc' => '员工照片'),
                     'sex' => array('name' => 'sex', 'type'=>'enum','range' => array('boy','girl'), 'default' => 'boy', 'require'=> true,'desc'=> '员工性别'),
                     'mobile' => array('name' => 'mobile', 'type'=>'string','max' => 11, 'min' => 0,  'require'=> false,'desc'=> '联系方式'),
                     'cardID' => array('name' => 'card_id', 'type'=>'string', 'min' => 15, 'max' => 18, 'require'=> true,'desc'=> '身份证号码'),
                     'nation' => array('name' => 'nation', 'type' => 'string',  'require' => false, 'desc' => '民族'),
                     'marriage' => array('name' => 'marriage', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '婚姻情况'),
                     'education' => array('name' => 'education', 'type' => 'string', 'require' => false, 'desc' => '文化程度'),
                     'industry' => array('name' => 'industry', 'type' => 'array', 'format'=>'json', 'min' => 1, 'require'=> true,'desc'=> '专业'),
                     'nativePlace' => array('name' => 'native_place', 'type'=>'int', 'min' => 1, 'require'=> true, 'desc' => '籍贯'),
                     'nativePlaceDistrict' => array('name' => 'native_place_district', 'type' => 'array', 'format'=>'json', 'require' => true, 'desc' => '户籍地区'),
                     'nativePlaceAddress' => array('name' => 'native_place_address', 'type'=>'string', 'min' => 1, 'require'=> true, 'desc' => '户籍详细地址'),
                     'nowDistrict' => array('name' => 'now_district', 'type' => 'array', 'format'=>'json', 'require' => false, 'desc' => '现居住省市区'),
                     'nowAddress' => array('name' => 'now_address','type'=>'string', 'require'=> false,'desc'=> '现居住地址'),
                     'operateId' => array('name' => 'operate_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '操作人ID'),
            ),
		);
 	}
	
  
  /**
     * 更新建筑员工信息
     * #desc 用于更新建筑员工信息
     * #return int code 操作码，0表示成功
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
        //判断员工是否存在
        $staffDomain = new Domain_Building_Staff();
        $staffInfo = $staffDomain->getBaseInfo($this->staffId);
        if( !$staffInfo) {
            $rs['code'] = 126;
            $rs['msg'] = T('Staff not exists');
            return $rs;
        }
//        //判断公司类别是否存在
//        $staffDomain = new Domain_Building_Staff();
//        $catInfo = $staffDomain->checkCatId($this->catId);
//        if (empty($catInfo)) {
//            $rs['code'] = 106;
//            $rs['msg'] = T('Categroy not exists');
//            return $rs;
//        }

        $data = array(
            'staff_id' => $this->staffId,
//            'cat_id' => json_encode($this->catId),
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
            'last_modify' => time(),
            'operate_id' => $this->operateId,
        );
        if($data['avatar'] == 'false'){
            $data['avatar'] = '';
        }
//print_r($data);exit;
        if($staffInfo['cardID'] != $data['cardID']){
            //检测身份证号码是否存在
            $filter = array('cardID' => $data['cardID'],'company_id' => $this->companyId);
            $cardInfo = $staffDomain->checkCardID( $filter );
            if (!empty($cardInfo)) {
                $rs['code'] = 155;
                $rs['msg'] = T('ID card exist');
                return $rs;
            }
        }
        $res = $staffDomain->updateStaff($data);
        if( $res){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info']['status'] = $status;

        return $rs;
    }
	
}
