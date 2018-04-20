<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Organization_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
                     'layoutId' => array('name' => 'layout_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '结构ID'),
            ),
        );
    }

    /**
     * 获取企业安全组织结构详情
     * #desc 用于获取企业安全组织结构详情
     * #return int code 操作码，0表示成功
     * #return int company_id 公司ID
     * #return string company_name 公司名称
     * #return string name 名称
     * #return string content 安全组织结构
     * #return int create_time 创建时间
     * #return int last_modify  最后更新时间
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

        //判断组织结构是否存在
        $organizationDomain = new Domain_Zhianbao_Organization();
        $info = $organizationDomain->getBaseInfo($this->layoutId);
        if( !$info) {
            DI()->logger->debug('Security organization structure not exist', $this->layoutId);

            $rs['code'] = 145;
            $rs['msg'] = T('Security organization structure not exist');
            return $rs;
        }
        $rs['info'] = $info;

        return $rs;
    }
    
}
