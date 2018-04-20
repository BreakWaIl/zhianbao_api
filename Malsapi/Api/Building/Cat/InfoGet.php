<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Cat_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'catId' => array('name' => 'cat_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司类别ID'),
            ),
        );
    }

  /**
   * 获取公司类别详情
   * #desc 用于获取公司类别详情
   * #return int code 操作码，0表示成功
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
        //判断公司类别是否存在
        $catDomain = new Domain_Building_Cat();
        $catInfo = $catDomain->getBaseInfo($this->catId);
        if (empty($catInfo)) {
            $rs['code'] = 106;
            $rs['msg'] = T('Categroy not exists');
            return $rs;
        }

        $rs['info'] = $catInfo;

        return $rs;
    }
    
}
