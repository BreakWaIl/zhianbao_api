<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Complaint_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '监管者ID'),
                     'type' => array('name' => 'type','type'=>'enum','range' => array('suggest','complaint'), 'require'=> false,'desc'=> '类型'),
                     'getType' => array('name' => 'getType','type'=>'enum','range' => array('all','wechat'), 'require'=> true,'desc'=> '获取类型'),
                     'page' => array('name' => 'page', 'type' => 'int', 'min' => 1, 'default'=>1,'require' => true, 'desc' => '页码'),
                     'pageSize' => array('name' => 'page_size', 'type' => 'int', 'min' => 1,'default'=>20, 'require' => true, 'desc' => '每页显示'),
                     'orderby' => array('name' => 'orderby','type' => 'enum','range'=>array('id:asc','id:desc'), 'default'=>'id:asc','require' => true, 'desc' => '排序方式'),
            ),
		);
 	}
  
  /**
     * 获取投诉建议列表
     * #desc 用于获取投诉建议列表
     * #return int code 操作码，0表示成功
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

        $complaintDomain = new Domain_Zhianbao_Complaint();
        $filter = array('regulator_id' => $this->regulatorId);
        if(isset($this->type)){
            $filter['type'] = $this->type;
        }
        if($this->getType == 'wechat'){
            $agentInfo = DI ()->cookie->get('zab_agent');
            $agentInfo = json_decode($agentInfo,true);
            if(isset($agentInfo)){
                $filter['openid'] = $agentInfo['openid'];
            }else{
                $rs['code'] = 149;
                $rs['msg'] = T('Get openid failed');
                return $rs;
            }
        }
        $list = $complaintDomain->getAllByPage($filter,$this->page,$this->pageSize,$this->orderby);
        $total = $complaintDomain->getCount($filter);

        $rs['list'] = $list;
        $rs['total'] = $total;

        return $rs;
    }
	
}
