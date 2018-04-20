<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_Notice_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'type' => array('name' => 'type',  'type' => 'enum', 'range'=>array('y','n'), 'require' => false, 'desc' => '是否签收:y 已签收 n 未签收'),
                     'beginTime' => array('name' => 'begin_create_time',  'type' => 'string', 'require' => false, 'desc' => '开始时间'),
                     'endTime' => array('name' => 'end_create_time',  'type' => 'string', 'require' => false, 'desc' => '结束时间'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
     * 获取用户发文通知列表
     * #desc 用于获取用户发文通知列表
     * #return int code 操作码，0表示成功
     * #return int id  通知ID
     * #return int company_id  公司ID
     * #return string title  通知标题
     * #return string content  通知内容
     * #return string is_release 是否发布：y 已发布 n 未发布
     * #return int create_time 创建时间
     * #return int last_modify  最后更新时间
     * #return int release_time 发布时间
     * #return string is_sign 是否签收：y 已签收 n 未签收
     * #return int sign_time 签收时间
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
        $domainNotice = new Domain_Zhianbao_Notice();
        $regulatorIds = $domainNotice->getCompanyIds($this->companyId);

        $filter = array('regulator_id' => $regulatorIds['regulator_id']);
        $filter['is_release'] = 'y';
        if(!empty($this->type)){
            $filter['is_sign'] = $this->type;
            $to_filter = $domainNotice->searchType($filter,$this->companyId);
            unset($filter);
            $filter = array('id' => $to_filter);
        }
        if(!empty($this->beginTime) && !empty($this->endTime)){
            $filter['create_time > ?'] = strtotime($this->beginTime);
            $filter['create_time < ?'] = strtotime($this->endTime);
        }

        $list = $domainNotice->getAllCompany($filter,$this->page,$this->pageSize,$this->orderby);
        $total = $domainNotice->getCountCompany($filter);

        $rs['list'] = $list;
        $rs['total'] = $total;

        return $rs;
    }
	
}
