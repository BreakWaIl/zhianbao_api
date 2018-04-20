<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Knowledge_Categroy_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '监管者ID'),
                'catName' => array('name' => 'cat_name', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '分类名称'),
            ),
        );
    }


    /**
     * 添加知识库分类
     * #desc 用于添加知识库分类
     * #return int code 操作码，0表示成功
     * #return int categroy_id  通知ID
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

        //判断分类名称是否重复
        $domain = new Domain_Zhianbao_KnowledgeCategroy();
        $info = $domain->getName($this->regulatorId,$this->catName);
        if (!empty($info)) {
            DI()->logger->debug('Name exists', $this->catName);

            $rs['code'] = 107;
            $rs['msg'] = T('Name exists');
            return $rs;
        }

        $data = array(
            'regulator_id' => $this->regulatorId,
            'cat_name' => $this->catName,
            'create_time' => time(),
            'last_modify' => time(),
        );
        $noticeId = $domain->addCat($data);

        $rs['info']['notice_id'] = $noticeId;

        return $rs;
    }

}
