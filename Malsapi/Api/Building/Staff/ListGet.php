<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Staff_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'require' => false, 'desc' => '公司ID'),
                     'name' => array('name' => 'name', 'type'=>'string', 'require'=> false,'desc'=> '员工姓名'),
                     'mobile' => array('name' => 'staff_mobile', 'type'=>'string', 'require'=> false,'desc'=> '手机号'),
                     'cardID' => array('name' => 'card_id', 'type'=>'string', 'require'=> false,'desc'=> '身份证号'),
                     'catId' => array('name' => 'cat_id', 'type'=>'int', 'require'=> false,'desc'=> '班组ID'),
                     'projectId' => array('name' => 'project_id', 'type'=>'int', 'require'=> false,'desc'=> '项目ID'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}

  /**
     * 获取建筑员工列表
     * #desc 用于获取建筑员工列表
     * #return int code 操作码，0表示成功
     * #return int id 员工ID
     * #return int company_id 公司ID
     * #return string name 员工姓名
     * #return string birthday 出生日期
     * #return string sex 性别
     * #return int mobile 手机号
     * #return string cardID 身份证号码
     * #return int create_time 创建时间
     * #return int last_modify  最后更新时间
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
        $filter['company_id'] = $this->companyId;
        if(!empty($this->name)){
            $filter['name LIKE ?'] = '%'.$this->name.'%';
        }
        if(!empty($this->mobile)){
            $filter['mobile LIKE ?'] = '%'.$this->mobile.'%' ;
        }
        if(!empty($this->cardID)){
            $filter['cardID LIKE ?'] = $this->cardID.'%' ;
        }
        $staffDomain = new Domain_Building_Staff();
        if(!empty($this->catId)){
            //判断公司类别是否存在
            $catDomain = new Domain_Building_Cat();
            $catInfo = $catDomain->getBaseInfo($this->catId);
            if (empty($catInfo)) {
                $rs['code'] = 106;
                $rs['msg'] = T('Categroy not exists');
                return $rs;
            }
            $filter['cat_id'] = $this->catId;
            $ids = $staffDomain->checkCatIds($filter);
            unset($filter['cat_id']);
            $filter['id'] = $ids;
        }
//        print_r($filter);exit;
        if(!empty($this->projectId)){
            //判断公司项目是否存在
            $projectDomain = new Domain_Building_Project();
            $projectInfo = $projectDomain->getBaseInfo($this->projectId);
            if (empty($projectInfo)) {
                $rs['code'] = 192;
                $rs['msg'] = T('Project not exists');
                return $rs;
            }
            $project_filter['company_id'] = $this->companyId;
            $project_filter['project_id'] = $this->projectId;
            $ids = $staffDomain->checkProjectIds($project_filter);
            $filter['id'] = $ids;
        }

        $list = $staffDomain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $total = $staffDomain->getCount($filter);

        $rs['list'] = $list;
        $rs['total'] = $total;

        return $rs;
    }
	
}
