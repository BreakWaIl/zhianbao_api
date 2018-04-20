<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Regulator_Staff_Cert_Skill_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'require' => false, 'desc' => '公司ID'),
                     'staffId' => array('name' => 'staff_id', 'type' => 'int', 'require' => false, 'desc' => '家政员ID'),
                     'bn' => array('name' => 'bn', 'type'=>'string', 'require'=> false,'desc'=> '证书编号'),
                     'type' => array('name' => 'type', 'type'=>'enum','range'=>array('society','government'), 'require'=> false,'desc'=> '证书类型'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}

    /**
     * 获取职业技能证书列表
     * #desc 用于获取职业技能证书列表
     * #return int code 操作码，0表示成功
     * #return int id 证书ID
     * #return int company_id 公司ID
     * #return int staff_id 家政员ID
     * #return int level 证书等级 1 专项级 2 初级 3 中级 4 高级
     * #return string name 技能名称
     * #return string cert_bn 证书编号
     * #return array img_url 图片路径
     * #return string issued 发证机关
     * #return string occupation 职业
     * #return string theoretical_score 理论知识考核成绩
     * #return string operating_score 操作技能考核成绩
     * #return string evaluation_score 评定成绩
     * #return string train_organization 培训机构
     * #return int accreditation_time 认证时间
     * #return string status 状态: y 正常 n 作废
     * #return string is_default 是否默认使用 y 是 n 否
     * #return string remark 备注
     * #return int create_time 创建时间
     * #return int last_modify  最后更新时间
     * #return array staff_info 家政员信息
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
        $filter['status'] = 'y';
        if(!empty($this->companyId)){
            $filter['company_id'] = $this->companyId;
        }
        if(!empty($this->staffId)){
            $filter['staff_id'] = $this->staffId;
        }
        if(!empty($this->bn)){
            $filter['cert_bn LIKE ?'] = '%'.$this->bn.'%' ;
        }
        if(!empty($this->type)){
            $filter['type'] = $this->type;
        }

        $staffCertDomain = new Domain_Jiafubao_StaffSkillCert();
        $list = $staffCertDomain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $total = $staffCertDomain->getCount($filter);

        $rs['list'] = $list;
        $rs['total'] = $total;

        return $rs;
    }
	
}
