<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_Staff_Add extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id','type'=>'int','require'=> true,'desc'=> '公司ID'),
                     'name' => array('name' => 'name','type'=>'string','require'=> true,'desc'=> '员工姓名'),
                     'sex' => array('name' => 'sex', 'type'=>'enum','range' => array('boy','girl'),  'require'=> true,'desc'=> '员工性别'),
                     'mobile' => array('name' => 'mobile', 'type'=>'string','max' => 11, 'min' => 11,  'require'=> true,'desc'=> '联系方式'),
                     'birthday' => array('name' => 'birthday', 'type'=>'string', 'min' => 1,  'require'=> true,'desc'=> '出生日期'),
                     'isCareer' => array('name' => 'is_career', 'type'=>'enum','range' => array('y','n'), 'default' => 'y', 'require'=> true,'desc'=> '是否专职:y 专职 n 兼职'),
                     'partId' => array('name' => 'part_id','type'=>'int','require'=> true,'desc'=> '员工角色'),
            ),
		);
 	}
  
  /**
   * 添加员工信息
   * #desc 用于添加员工信息
   * #return int code 操作码，0表示成功
   * #return int id  客户id
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //检测公司是否存在
        $companyDomain = new Domain_Zhianbao_Company();
        $companyInfo = $companyDomain->getBaseInfo($this->companyId);
        if(! $companyInfo){
            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }

        $staffDomain = new Domain_Zhianbao_Staff();
        $data = array(
            'company_id' => $this->companyId,
            'name' => $this->name,
            'sex' => $this->sex,
            'mobile' => $this->mobile,
            'create_time' => time(),
            'last_modify' => time(),
            'birthday' => strtotime($this->birthday),
            'is_career' => $this->isCareer,
            'part_id' => $this->partId,
        );
        $addRs = $staffDomain->addStaff($data);
        $rs['staff_id'] = $addRs;
        return $rs;
    }
	
}
