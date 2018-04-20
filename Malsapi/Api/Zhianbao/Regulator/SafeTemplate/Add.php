<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_SafeTemplate_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '监管者ID'),
                'name' => array('name' => 'name', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '模板名称'),
                'content' => array('name' => 'content', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '模板内容'),
            ),
        );
    }


    /**
     * 添加生产安全标准化模板
     * #desc 用于添加生产安全标准化模板
     * #return int code 操作码，0表示成功
     * #return int template_id  模板ID
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
            'content' => $this->content,
            'create_time' => time(),
            'last_modify' => time(),
        );
        $domain = new Domain_Zhianbao_SafeTemplate();
        $id = $domain->addTemplate($data);

        $rs['info']['template_id'] = $id;

        return $rs;
    }

}
