<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Staff_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'name' => array('name' => 'name', 'type'=>'string', 'min' => 0, 'require'=> false,'desc'=> '家政员姓名'),
                     'mobile' => array('name' => 'mobile', 'type'=>'string', 'min' => 0, 'require'=> false,'desc'=> '手机号'),
                     'online' => array('name' => 'online', 'type'=>'enum', 'range' => array('y','n'), 'require'=> false,'desc'=> '是否可线上选择'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
     * 获取平台家政员工列表
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
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $filter = array();
        if(!empty($this->name)){
            $filter['name LIKE ?'] = '%'.$this->name.'%' ;
        }
        if(!empty($this->mobile)){
            $filter['mobile LIKE ?'] = '%'.$this->mobile.'%' ;
        }
        if(isset($this->online)){
            $filter['online'] = $this->online;
        }

        $houseStaffDomain = new Domain_Jiafubao_HouseStaff();
        $list = $houseStaffDomain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $total = $houseStaffDomain->getCount($filter);

        $rs['list'] = $list;
        $rs['total'] = $total;

        return $rs;
    }
	
}
