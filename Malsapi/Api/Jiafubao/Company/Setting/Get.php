<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_Setting_Get extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'keys' => array('name' => 'keys', 'type' => 'array', 'format' => 'json','require' => true, 'desc' => '要获取的keys'),
            ),
        );
    }


    /**
     * 获取公司配置
     * #desc 用于获取公司配置
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

        $setDomain = new Domain_Jiafubao_CompanySetting();
        foreach ($this->keys as $key => $value) {
            $v = $setDomain->get($this->companyId, $value);
            $info[] = array(
                'key' => $value,
                'value' => $v
            );
        }
        $rs['info'] = $info;
        return $rs;
    }

}
