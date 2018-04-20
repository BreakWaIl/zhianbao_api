<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Regulator_Staff_HealthCard_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'require' => false, 'desc' => '公司ID'),
                     'staffId' => array('name' => 'staff_id', 'type' => 'int', 'require' => false, 'desc' => '家政员ID'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
     * 获取健康卡列表
     * #desc 用于获取健康卡列表
     * #return int code 操作码，0表示成功
     * #return int id 健康卡ID
     * #return int company_id 公司ID
     * #return int staff_id 家政员ID
     * #return int company_name 公司名称
     * #return int staff_name 家政员名称
     * #return array img_url 图片
     * #return string card_bn 健康卡编号
     * #return int send_time 发放时间
     * #return int end_time 截至有效期
     * #return int create_time 创建时间
     * #return int last_modify  最后更新时间
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

        $companyIds = $regulatorDomain->getCompanyIds($this->regulatorId);
        $filter = array('company_id' => $companyIds);
        if(!empty($this->companyId)){
            $filter['company_id'] = $this->companyId;
        }
        if(!empty($this->staffId)){
            $filter['staff_id'] = $this->staffId;
        }

        $healthCardDomain = new Domain_Jiafubao_StaffHealthCard();
        $list = $healthCardDomain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $total = $healthCardDomain->getCount($filter);

        $rs['list'] = $list;
        $rs['total'] = $total;

        return $rs;
    }
	
}
