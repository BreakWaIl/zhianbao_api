<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_Organization_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'name' => array('name' => 'name', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '名称'),
                'content' => array('name' => 'content', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '公司组织结构'),
            ),
        );
    }


    /**
     * 添加安全组织结构
     * #desc 用于添加安全组织结构
     * #return int code 操作码，0表示成功
     * #return int layout_id  结构ID
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
        $organizationDomain = new Domain_Zhianbao_Organization();
        $id = $organizationDomain->addOrganization($data);

        $rs['info']['layout_id'] = $id;

        return $rs;
    }

}
