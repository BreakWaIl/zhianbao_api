<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Construction_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'labelId' => array('name' => 'label_id', 'type' => 'int', 'require' => false, 'desc' => '标签ID'),
                     'recorder' => array('name' => 'recorder', 'type' => 'string', 'require' => false, 'desc' => '记录人'),
                     'projectId' => array('name' => 'project_id', 'type' => 'int', 'require' => false, 'desc' => '项目ID'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
   * 获取施工日志列表
   * #desc 用于获取施工日志列表
   * #return int code 操作码，0表示成功
   * #return int id 日志ID
   * #return int company_id 公司ID
   * #return string label_id 标签ID
   * #return string dateTime 记录日期
   * #return array day 白天信息
   * #return array night 晚上信息
   * #return string production_record 生产情况记录
   * #return string safety_work_record 技术质量安全工作记录
   * #return string contract_work_record 合同外工作量记录
   * #return string leader 负责人
   * #return string recorder 记录人
   * #return int operate_id 操作人ID
   * #return int create_time 创建时间
   * #return int last_modify 最后更新时间
   * #return array label_info 标签信息
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
        $constructionDomain = new Domain_Building_Construction();
        //标签搜索
        if(!empty($this->labelId)){
            $filter['label_id'] = $this->labelId;
        }
        //记录人搜索
        if(!empty($this->recorder)){
            $id = $constructionDomain->subInfo($companyInfo,$this->recorder);
            $filter['recorder'] = $id;
        }
        //项目搜索
        if(!empty($this->projectId)){
            $filter['project_id'] = $this->projectId;
        }

        $list = $constructionDomain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $count = $constructionDomain->getCount($filter);

        $rs['count'] = $count;
        $rs['list'] = $list;

        return $rs;
    }
	
}
