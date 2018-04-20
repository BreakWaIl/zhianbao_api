<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_SafeSelf_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id','type'=>'string','require'=> true,'desc'=> '公司ID'),
                     'title' => array('name' => 'title','type'=>'string','require'=> false,'desc'=> '申报标题'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
   * 获取生产安全标准化模板列表
   * #desc 用于获取生产安全标准化模板列表
   * #return int code 操作码，0表示成功
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断公司是否存在
        $domainCompany = new Domain_Zhianbao_Company();
        $companyInfo = $domainCompany->getBaseInfo($this->companyId);
        if(empty($companyInfo)) {
            DI()->logger->debug('Company not exists', $this->companyId);

            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }

        $applyDomain = new Domain_Zhianbao_SafeApply();
        $filter = array('company_id' => $this->companyId);
        if(!empty($this->title)){
            $filter['apply_title LIKE ?'] = '%'.$this->title.'%';
        }

        $list = $applyDomain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $count = $applyDomain->getCount($filter);
        $rs['total'] = $count;
        $rs['list'] = $list;
        return $rs;
    }
	
}
