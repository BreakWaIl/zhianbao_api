<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Label_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                     'labelId' => array('name' => 'label_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '标签ID'),
            ),
        );
    }

  /**
   * 获取公司标签详情
   * #desc 用于获取公司标签详情
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
        //判断标签是否存在
        $labelDomain = new Domain_Building_Label();
        $labelInfo = $labelDomain->getBaseInfo($this->labelId);
        if (empty($labelInfo)) {
            $rs['code'] = 203;
            $rs['msg'] = T('Label not exists');
            return $rs;
        }
        $rs['info'] = $labelInfo;

        return $rs;
    }
    
}
