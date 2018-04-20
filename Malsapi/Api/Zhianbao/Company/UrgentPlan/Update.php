<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class  Api_Zhianbao_Company_UrgentPlan_Update extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'planId' => array('name' => 'plan_id', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '预案ID'),
                     'name' => array('name' => 'name', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '预案名称'),
                     'content' => array('name' => 'content', 'type' => 'string', 'require' => true, 'desc' => '预案内容'),
            ),
		);
 	}
	
  
  /**
     * 更新应急预案
     * #desc 用于应急预案记录
     * #return int code 操作码，0表示成功
     * #return int status 状态 0 成功, 1 失败
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

        //判断应急预案是否存在
        $planDomain = new Domain_Zhianbao_UrgentPlan();
        $planInfo = $planDomain->getBaseInfo($this->planId);
        if( !$planInfo) {
            DI()->logger->debug('Urgent plan not exist', $this->planId);

            $rs['code'] = 127;
            $rs['msg'] = T('Urgent plan not exist');
            return $rs;
        }
        //判断是否已经作废
        if($planInfo['is_repeal'] == 'y'){
            DI()->logger->debug('Urgent plan have been repeal', $this->planId);

            $rs['code'] = 141;
            $rs['msg'] = T('Urgent plan have been repeal');
            return $rs;
        }
        if($planInfo['status'] == 'finish'){
            DI()->logger->debug('Update failed', $this->planId);

            $rs['code'] = 104;
            $rs['msg'] = T('Update failed');
            return $rs;
        }

        $data = array(
            'plan_id' => $this->planId,
            'name' => $this->name,
            'content' => $this->content,
            'status' => 'wait',
            'last_modify' => time(),
        );
        $res = $planDomain->updatePlan($data);
        if( $res){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info']['status'] = $status;

        return $rs;
    }
	
}
