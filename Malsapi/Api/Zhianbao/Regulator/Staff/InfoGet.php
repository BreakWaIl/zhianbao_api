<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Staff_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
                     'staffId' => array('name' => 'staff_id','type'=>'int','require'=> true,'desc'=> '人员ID'),
            ),
        );
    }

    /**
     * 获取企业员工详情
     * #desc 用于获取企业员工详情
     * #return int code 操作码，0表示成功
     * #return int id 员工ID
     * #return int company_id 公司ID
     * #return string name 员工名称
     * #return string sex 性别
     * #return string mobile 手机号
     * #return int create_time 创建时间
     * #return int last_modify 最后更新时间
     * #return int birthday 出生日期
     * #return int is_career y 专职 n 兼职
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

        //判断员工是否存在
        $staffDomain = new Domain_Zhianbao_Staff();
        $staffInfo = $staffDomain->getBaseInfo($this->staffId);
        if( !$staffInfo) {
            DI()->logger->debug('Staff not exist', $this->staffId);

            $rs['code'] = 126;
            $rs['msg'] = T('Staff not exists');
            return $rs;
        }

        $rs['info'] = $staffInfo;

        return $rs;
    }
    
}
