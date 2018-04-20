<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Building_Staff_CheckCardID extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'cardID' => array('name' => 'card_id', 'type'=>'string', 'min' => 15, 'max' => 18, 'require'=> true,'desc'=> '身份证号码'),
            ),
        );
    }


    /**
     * 检测员工是否存在
     * #desc 检测员工是否存在
     * #return int code 操作码，0表示成功
     * #return int true 不存在 false 已存在
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
        $filter = array(
            'company_id' => $this->companyId,
            'cardID' => $this->cardID,
        );
        $staffDomain = new Domain_Building_Staff();
        $info = $staffDomain->checkCardID($filter);
        if(!empty($info)){
            $rs['info'] = $staffDomain->getBaseInfo($info['id']);
        }else{
            $rs['info'] =  false;
        }

        return $rs;
    }

}
