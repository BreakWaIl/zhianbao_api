<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Visa_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'projectId' => array('name' => 'project_id', 'type' => 'int', 'require' => false, 'desc' => '项目ID'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
   * 获取合同外签证列表
   * #desc 用于获取合同外签证列表
   * #return int code 操作码，0表示成功
   * #return int id 签证ID
   * #return int company_id 公司ID
   * #return int project_id 项目ID
   * #return string title 标题
   * #return array img_url 图片地址
   * #return int create_time 创建时间
   * #return int last_modify 最后更新时间
   * #return int operate_id 操作人ID
   * #return string project_name 项目名称
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

        $filter = array('company_id' => $this->companyId);
        if(!empty($this->projectId)){
            $filter['project_id'] = $this->projectId;
        }

        $visaDomain = new Domain_Building_Visa();
        $list = $visaDomain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $count = $visaDomain->getCount($filter);

        $rs['count'] = $count;
        $rs['list'] = $list;

        return $rs;
    }
	
}
