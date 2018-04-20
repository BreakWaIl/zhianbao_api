<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Regulator_GoldStaff_Config_Set extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
                     'configValue' => array('name' => 'config_value', 'type'=>'enum','range' => array('open','close'), 'default' => 'close', 'require'=> true,'desc'=> '申请状态：open 打开 close 关闭'),
            ),
        );
    }
  
  /**
     * 更新金牌家政员申请设置
     * #desc 用于更新金牌家政员申请设置
     * #return int code 操作码，0表示成功
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

        //获取配置详细
        $domainShopKV = new Domain_Zhianbao_RegulatorConfig($this->regulatorId);
        $info = $domainShopKV->set('GoldStaff',$this->configValue);
        if( empty($info)) {
            $status = true;
        }else{
            $status = false;
        }
        $rs['info'] = $status;

        return $rs;
    }
    
}
