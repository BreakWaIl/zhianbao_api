<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_UrgentReport_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'name' => array('name' => 'name', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '演练名称'),
                'content' => array('name' => 'content', 'type' => 'string', 'require' => true, 'desc' => '演练内容'),
                'number' => array('name' => 'number', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '人员数量'),
                'result' => array('name' => 'result', 'type' => 'string', 'require' => true, 'desc' => '演练结果'),
            ),
        );
    }


    /**
     * 添加应急演练
     * #desc 用于添加应急演练
     * #return int code 操作码，0表示成功
     * #return int report_id  演练ID
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
            'number' => $this->number,
            'result' => $this->result,
            'create_time' => time(),
            'last_modify' => time(),
        );
        $reportDomain = new Domain_Zhianbao_UrgentReport();
        $reportId = $reportDomain->addReport($data);

        $rs['info']['report_id'] = $reportId;

        return $rs;
    }

}
