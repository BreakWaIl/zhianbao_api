<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_License_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '监管者ID'),
                'name' => array('name' => 'name', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '类型名称'),
            ),
        );
    }


    /**
     * 添加证照类型
     * #desc 用于添加证照类型
     * #return int code 操作码，0表示成功
     * #return int type_id  类型ID
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

        $data = array(
            'regulator_id' => $this->regulatorId,
            'name' => $this->name,
            'create_time' => time(),
            'last_modify' => time(),
        );
        $licenseDomain = new Domain_Zhianbao_License();
        $tyId = $licenseDomain->addLicenseType($data);

        $rs['info']['type_id'] = $tyId;

        return $rs;
    }

}
