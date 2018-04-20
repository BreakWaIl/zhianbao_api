<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Regulator_Company_License_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
                     'licenseId' => array('name' => 'license_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '执照ID'),
            ),
        );
    }
  
  /**
     * 获取公司执照详情
     * #desc 用于获取公司执照详情
     * #return int code 操作码，0表示成功
   * #return int id 执照ID
   * #return int company_id 公司ID
   * #return string company_name 公司名称
   * #return string name 证书名称
   * #return string img_url 图片路径
   * #return int create_time 创建时间
   * #return int last_modify 最后更新时间
  */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //检测监管者是否存在
        $regulatorDomain = new Domain_Zhianbao_Regulator();
        $regulatorInfo = $regulatorDomain->getBaseInfo($this->regulatorId);
        if(! $regulatorInfo){
            $rs['code'] = 118;
            $rs['msg'] = T('Regulator not exists');
            return $rs;
        }

        //判断公司执照是否存在
        $licenseDomain = new Domain_Jiafubao_CompanyLicense();
        $licenseInfo = $licenseDomain->getBaseInfo($this->licenseId);
        if( !$licenseInfo) {
            DI()->logger->debug('License not found', $this->licenseId);

            $rs['code'] = 156;
            $rs['msg'] = T('License not exists');
            return $rs;
        }

        $rs['info'] = $licenseInfo;

        return $rs;
    }
    
}
