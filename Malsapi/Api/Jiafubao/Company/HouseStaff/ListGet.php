<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_HouseStaff_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'name' => array('name' => 'name', 'type'=>'string', 'min' => 0, 'require'=> false,'desc'=> '家政员姓名'),
                     'mobile' => array('name' => 'mobile', 'type'=>'string', 'min' => 0, 'require'=> false,'desc'=> '手机号'),
                     'isCheck' => array('name' => 'is_check', 'type'=>'enum', 'range' => array('n','y'), 'require'=> false,'desc'=> '是否审核'),
                     'canWork' => array('name' => 'can_work', 'type'=>'enum', 'range' => array('n','y'), 'require'=> false,'desc'=> '是否可以工作'),
                     'isPost' => array('name' => 'is_post', 'type'=>'enum', 'range' => array('n','y','all'),'default'=>'all', 'require'=> false,'desc'=> '是否上岗：y 是 n 否 all 全部'),
                     'beginTime' => array('name' => 'begin_time', 'type'=>'int', 'require'=> false,'desc'=> '开始出生日期'),
                     'endTime' => array('name' => 'end_time', 'type'=>'int', 'require'=> false,'desc'=> '结束出生日期'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
                     'timeSort' => array('name' => 'time_sort','type' => 'enum','range'=>array('y','n'), 'default'=>'n','require' => false, 'desc' => '是否按最后更新时间排序'),
            ),
		);
 	}

  /**
     * 获取家政员工列表
     * #desc 用于获取家政员工列表
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
     * #return string learn_experience 学习经历
     * #return string work_experience 工作经历
     * #return string society_experience 社会经历
     * #return string crime_experience 犯罪经历
     * #return int create_time 创建时间
     * #return int last_modify  最后更新时间
     * #return string online 是否线上预约：y 是 n 否
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

        $filter = array();
        $filter['company_id'] = $this->companyId;
        if(!empty($this->name)){
            $filter['name LIKE ?'] = '%'.$this->name.'%' ;
        }
        if(!empty($this->mobile)){
            $filter['mobile LIKE ?'] = '%'.$this->mobile.'%' ;
        }
        if(isset($this->isCheck)){
            $filter['is_check'] = $this->isCheck;
        }
        if(isset($this->canWork) && $this->canWork == 'y'){
            $filter['can_work'] = $this->canWork;
        }
        if($this->isPost != 'all'){
            $filter['is_post'] = $this->isPost;
        }
        if(!empty($this->beginTime)){
            $new_time = date("Y") - ($this->beginTime).'/1/1';
            $filter['birthday >= ?'] = strtotime($new_time);
        }
        if(!empty($this->endTime)){
            $old_time = date("Y") - ($this->endTime).'/1/1';
            $filter['birthday >= ?'] = strtotime($old_time);
        }
        if(!empty($this->beginTime) && !empty($this->endTime)){
            if($this->beginTime <= $this->endTime){
                $new_time = date("Y") - ($this->beginTime).'/12/31';
                $old_time = date("Y") - ($this->endTime).'/1/1';
                $filter['birthday >= ?'] = strtotime($old_time);
                $filter['birthday <= ?'] = strtotime($new_time);
            }
        }

        $houseStaffDomain = new Domain_Jiafubao_CompanyHouseStaff();
        $list = $houseStaffDomain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $total = $houseStaffDomain->getCount($filter);
//        $total = $list['total'];
//        unset($list['total']);
        unset($filter['can_work']);
        if($this->timeSort == 'y'){
            $list = $houseStaffDomain->timeSort($list);
        }
        $rs['list'] = $list;
        $rs['total'] = $total;

        return $rs;
    }

}
