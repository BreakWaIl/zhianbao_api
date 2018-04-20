<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Knowledge_Categroy_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '监管者ID'),
                     'catId' => array('name' => 'categroy_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '分类ID'),
            ),
        );
    }
  
  /**
     * 获取知识库分类信息
     * #desc 用于获取当前知识库分类详情
     * #return int code 操作码，0表示成功
     * #return int id 分类ID
     * #return int company_id 公司ID
     * #return string cat_name  分类内容
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

        //判断分类是否存在
        $domain = new Domain_Zhianbao_KnowledgeCategroy();
        $info = $domain->getBaseInfo($this->catId);
        if( !$info) {
            DI()->logger->debug('Categroy not found', $this->catId);

            $rs['code'] = 106;
            $rs['msg'] = T('Categroy not exists');
            return $rs;
        }

        $rs['info'] = $info;

        return $rs;
    }
    
}
