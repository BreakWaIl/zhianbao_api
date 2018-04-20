<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_Information_ShenPu_Get extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'userId' => array('name' => 'user_id', 'type' => 'string', 'require' => true, 'desc' => '用户ID'),
                     'serviceType' => array('name' => 'service_type', 'type' => 'enum', 'range' => array('tempClean', 'quickClean', 'longHours', 'nanny', 'careElderly', 'careBaby', 'matron'), 'require' => true, 'desc' => '服务类型 tempClean:临时保洁,quickClean:宅速洁,longHours:长期钟点工,nanny:保姆,careElderly:看护老人,careBaby:育儿嫂,matron:月嫂'),
            ),
        );
    }
  
  /**
     * 获取公司钟点工价格标准
     * #desc 用于获取公司钟点工价格标准
     * #return int code 操作码，0表示成功
     * #return string status 状态:true 成功 false 失败
     * #return int user_id 用户ID
     * #return string part_work_charge 钟点工价格标准
  */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断公司是否存在
        $domain = new Domain_Jiafubao_User();
        $info = $domain->getBaseByUserId($this->userId);
        if( empty($info)){
            $rs['code'] = 112;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }

        $companyInformationDomain = new Domain_Jiafubao_CompanyInformation();
        $info = $companyInformationDomain->check($info['id']);
        if($info){
            $rs['info']['status'] = true;
            $rs['info']['part_work_charge'] = $info['part_work_charge'];
        }else{
            $rs['info']['status'] = false;
        }

        return $rs;
    }
    
}
