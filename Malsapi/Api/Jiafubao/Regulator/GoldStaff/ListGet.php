<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Regulator_GoldStaff_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
                     'years' => array('name' => 'years','type'=>'int','require'=> false,'desc'=> '申请年份'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
   * 获取金牌家政员列表
   * #desc 用于获取金牌家政员列表
   * #return int code 操作码，0表示成功
   * #return string name 员工姓名
   * #return string trades 从事工种
   * #return string experience 从事家政服务时间
   * #return string birthday 出生年份
   * #return int mobile 手机号
   * #return string skill_level 职业技能等级
   * #return string company_name 家政公司名称
   * #return string years 申请年份
 */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //检测监管者是否存在
        $regulatorDomain = new Domain_Zhianbao_Regulator();
        $regulatorInfo = $regulatorDomain->getBaseInfo($this->regulatorId);
        if(! $regulatorInfo){
            $rs['code'] = 118;
            $rs['msg'] = T('Regulator not exists');
            return $rs;
        }

//        $companyIds = $regulatorDomain->getCompanyIds($this->regulatorId);
//        $filter = array('company_id' => $companyIds);
//        if(!empty($this->companyId)){
//            $filter['company_id'] = $this->companyId;
//        }
        $filter = array('regulator_id' => $this->regulatorId);
        if(!empty($this->years)){
            $filter['years'] = $this->years;
        }
        $goldStaffDomain = new Domain_Jiafubao_GoldStaff();
        $list = $goldStaffDomain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $total = $goldStaffDomain->getCount($filter);

        $rs['count'] = $total;
        $rs['list'] = $list;
        return $rs;
    }
	
}
