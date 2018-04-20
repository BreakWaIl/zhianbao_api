<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class  Api_Zhianbao_Regulator_Knowledge_Categroy_Update extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '监管者ID'),
                     'catId' => array('name' => 'categroy_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '分类ID'),
                     'catName' => array('name' => 'cat_name', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '分类名称'),
            ),
		);
 	}
	
  
  /**
     * 更新知识库分类
     * #desc 用于更新知识库分类
     * #return int code 操作码，0表示成功
     * #return int status 状态 0 成功, 1 失败
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
        //判断分类名称是否重复
        $domain = new Domain_Zhianbao_KnowledgeCategroy();
        $info = $domain->getName($this->regulatorId,$this->catName);
        if (!empty($info)) {
            DI()->logger->debug('Categroy name exists', $this->catName);

            $rs['code'] = 107;
            $rs['msg'] = T('Categroy name exists');
            return $rs;
        }

        $data = array(
            'cat_id' => $this->catId,
            'cat_name' => $this->catName,
            'last_modify' => time(),
        );

        $info = $domain->updateCat($data);
        if( $info){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info']['status'] = $status;

        return $rs;
    }
	
}
