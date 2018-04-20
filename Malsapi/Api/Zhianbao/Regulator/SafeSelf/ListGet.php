<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_SafeSelf_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
                     //'type' => array('name' => 'type', 'type' => 'string', 'require' => false, 'desc' => '类型'),
                     'status' => array('name' => 'status',  'type' => 'enum', 'range'=>array('wait','waitReview','finish','all'), 'default'=>'all', 'require' => false, 'desc' => 'wait 待审核 waitReview 待复核 finish 已完成 all 全部'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}

    /**
     * 获取申报安全生产申报列表
     * #desc 用于获取申报安全生产申报列表
     * #return int code 操作码，0表示成功
     * #return int id 申报ID
     * #return int company_id 公司ID
     * #return int user_id 申报人ID
     * #return string company_name 公司名称
     * #return string user_name 申报人名称
     * #return string apply_title 申报标题
     * #return int template_id 模板ID
     * #return string self_content 申报内容
     * #return int apply_grade 申报等级
     * #return int apply_time 申报时间
     * #return int review_time 审核时间
     * #return string apply_theway 申报方式
     * #return string file_path 文件路径
     * #return string status 状态：wait 等待 applying 申请中 review 审核中 firstReview 初审 finish 审核完成
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
//        if(!empty($this->title)){
//            $filter['apply_title LIKE ?'] = '%'.$this->title.'%';
//        }
        //全部
        if($this->status == 'all'){
            $filter['status !=  ?'] = 'wait';
        }
        //待审核
        if($this->status == 'wait'){
            $filter['status'] = array('applying','review');
        }
        //待复核
        if($this->status == 'waitReview'){
            $filter['status'] = 'firstReview';
        }
        //已完成
        if($this->status == 'finish'){
            $filter['status'] = array('finish','firstReject','endReject');
        }

        $applyDomain = new Domain_Zhianbao_SafeApply();
        $list = $applyDomain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $total = $applyDomain->getCount($filter);

        $rs['count'] = $total;
        $rs['list'] = $list;
        return $rs;
    }
	
}
