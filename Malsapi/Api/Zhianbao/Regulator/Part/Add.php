<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Part_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '监管者ID'),
                'name' => array('name' => 'name', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '角色名称'),
            ),
        );
    }


    /**
     * 添加员工角色类型
     * #desc 用于添加员工角色类型
     * #return int code 操作码，0表示成功
     * #return int part_id  角色ID
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
        //检测角色是否已存在
        $partDomain = new Domain_Zhianbao_Part();
        $info = $partDomain->isUser($this->regulatorId,$this->name);
        if(!empty($info)){
            DI()->logger->debug('Being used', $this->name);

            $rs['code'] = 137;
            $rs['msg'] = T('Being used');
            return $rs;
        }

        $data = array(
            'regulator_id' => $this->regulatorId,
            'name' => $this->name,
            'create_time' => time(),
            'last_modify' => time(),
        );
        $id = $partDomain->addPart($data);

        $rs['info']['part_id'] = $id;

        return $rs;
    }

}
