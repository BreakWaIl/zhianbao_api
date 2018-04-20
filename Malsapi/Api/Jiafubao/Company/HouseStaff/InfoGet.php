<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_HouseStaff_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'staffId' => array('name' => 'staff_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '员工ID'),
            ),
        );
    }
  
  /**
     * 获取家政员工详情
     * #desc 用于获取家政员工详情
     * #return int code 操作码，0表示成功
   * #return int id 员工ID
   * #return int company_id 公司ID
   * #return string name 员工姓名
   * #return string birthday 出生日期
   * #return array avatar 员工照片
   * #return string sex 性别
   * #return int mobile 手机号
   * #return string address 地址
   * #return string cardID 身份证号码
   * #return array crime_experience 犯罪经历
   * #return string learn_experience 学习经历
   * #return string work_experience 工作经历
   * #return string society_experience 社会经历
   * #return int create_time 创建时间
   * #return int last_modify  最后更新时间
   * #return string online 是否线上预约：y 是 n 否
   * #return string nation 民族
   * #return string education  文化程度
   * #return string marriage 婚姻情况
   * #return string industry 专业
   * #return int native_place 籍贯
   * #return array native_place_district 户籍地区
   * #return string native_place_address 户籍详细地址
   * #return array now_district 现居住省市区
   * #return array staff_demand 业务需求
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

        //判断家政人员是否存在
        $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
        $staffInfo = $houseStaffDomain->getBaseInfo($this->staffId);
        if( !$staffInfo) {
            $rs['code'] = 126;
            $rs['msg'] = T('Staff not exists');
            return $rs;
        }

        $rs['info'] = $staffInfo;

        return $rs;
    }
    
}
