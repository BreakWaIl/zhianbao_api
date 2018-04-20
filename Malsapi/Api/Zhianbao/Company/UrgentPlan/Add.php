<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_UrgentPlan_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'name' => array('name' => 'name', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '预案名称'),
                'content' => array('name' => 'content', 'type' => 'string', 'require' => true, 'desc' => '预案内容'),
            ),
        );
    }


    /**
     * 添加应急预案
     * #desc 用于添加应急预案
     * #return int code 操作码，0表示成功
     * #return int plan_id  预案ID
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

        $data = array(
            'company_id' => $this->companyId,
            'name' => $this->name,
            'content' => $this->content,
            'create_time' => time(),
            'last_modify' => time(),
        );
        $planDomain = new Domain_Zhianbao_UrgentPlan();
        $planId = $planDomain->addPlan($data);

        $rs['info']['plan_id'] = $planId;

        return $rs;
    }

}
