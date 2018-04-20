<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_ShenPu_Get extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
            ),
        );
    }


    /**
     * 获取公司的神铺信息
     * #desc 用于获取公司的神铺信息
     * #return int code 操作码，0表示成功
     * #return int status  状态 0:成功 1:失败
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

        $companyCustomerDomain = new Domain_Jiafubao_CompanyCustomer();
        $info = $companyCustomerDomain->getShopInfo($companyInfo['user_id']);
        $rs['info'] = $info;
        return $rs;
    }

}
