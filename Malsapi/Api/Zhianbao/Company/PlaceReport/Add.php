<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_PlaceReport_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'name' => array('name' => 'name', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '人员名称'),
                'imgUrl' => array('name' => 'img_url', 'type' => 'string', 'require' => true, 'desc' => '图片路径'),
            ),
        );
    }


    /**
     * 添加检测报告
     * #desc 用于添加检测报告
     * #return int code 操作码，0表示成功
     * #return int report_id  检测ID
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
            'img_url' => $this->imgUrl,
            'create_time' => time(),
            'last_modify' => time(),
        );
        $reportDomain = new Domain_Zhianbao_PlaceReport();
        $reportId = $reportDomain->addReport($data);

        $rs['info']['report_id'] = $reportId;

        return $rs;
    }

}
