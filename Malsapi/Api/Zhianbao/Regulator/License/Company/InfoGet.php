<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_License_Company_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
                     'licenseId' => array('name' => 'license_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '证照ID'),
            ),
        );
    }

    /**
     * 获取企业证照详情
     * #desc 用于获取企业证照详情
     * #return int code 操作码，0表示成功
     * #return int company_id 公司ID
     * #return int type_id 类型ID
     * #return array img_url 图片
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

        //判断企业证照是否存在
        $licenseDomain = new Domain_Zhianbao_License();
        $info = $licenseDomain->getLicenseInfo($this->licenseId);
        if( !$info) {
            DI()->logger->debug('Company license not exist', $this->licenseId);

            $rs['code'] = 147;
            $rs['msg'] = T('Company license not exist');
            return $rs;
        }

        $rs['info'] = $info;

        return $rs;
    }
    
}
