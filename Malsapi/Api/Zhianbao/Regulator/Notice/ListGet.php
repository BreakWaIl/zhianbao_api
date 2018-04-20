<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Notice_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '监管者ID'),
                     'title' => array('name' => 'title', 'type' => 'string', 'require' => false, 'desc' => '通知标题'),
                     'type' => array('name' => 'type',  'type' => 'enum', 'range'=>array('y','n'), 'require' => false, 'desc' => '是否发布:y 已发布 n 未发布'),
                     'isSign' => array('name' => 'is_sign',  'type' => 'enum', 'range'=>array('y','n'), 'require' => false, 'desc' => '是否签收: y 已签收 n 待签收'),
                     'beginTime' => array('name' => 'begin_create_time',  'type' => 'string', 'require' => false, 'desc' => '开始时间'),
                     'endTime' => array('name' => 'end_create_time',  'type' => 'string', 'require' => false, 'desc' => '结束时间'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
     * 获取发文通知列表
     * #desc 用于获取发文通知列表
     * #return int code 操作码，0表示成功
     * #return int id  通知ID
     * #return int company_id  公司ID
     * #return string title  通知标题
     * #return string content  通知内容
     * #return string is_release 是否发布：y 已发布 n 未发布
     * #return int create_time 创建时间
     * #return int last_modify  最后更新时间
     * #return int release_time 发布时间
     * #return string is_release 是否签收：y 已签收 n 未签收
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

        $filter = array();
        $filter['regulator_id'] = $this->regulatorId;
        if(!empty($this->type)){
            if($this->type == 'y'){
                $filter['is_release'] = 'y';
            }
            if($this->type == 'n'){
                $filter['is_release'] = 'n';
            }
        }
        if(!empty($this->isSign)){
            if($this->isSign == 'y'){
                $filter['is_sign'] = 'y';
            }
            if($this->isSign == 'n'){
                $filter['is_sign'] = 'n';
            }
        }
        if(!empty($this->title)){
            $filter['title LIKE ?'] = '%'.$this->title.'%';
        }
        if(!empty($this->beginTime) && !empty($this->endTime)){
            $filter['create_time > ?'] = strtotime($this->beginTime);
            $filter['create_time < ?'] = strtotime($this->endTime);
        }
        $domainNotice = new Domain_Zhianbao_Notice();

        $list = $domainNotice->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $total = $domainNotice->getCount($filter);

        $rs['list'] = $list;
        $rs['total'] = $total;

        return $rs;
    }
	
}
