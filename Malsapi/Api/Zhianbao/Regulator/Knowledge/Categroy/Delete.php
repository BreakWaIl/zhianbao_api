<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Knowledge_Categroy_Delete extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '监管者ID'),
                     'catId' => array('name' => 'categroy_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '分类ID'),
            ),
		);
 	}
	
  
  /**
     * 删除知识库分类
     * #desc 用于删除当前知识库分类
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
        //判断是否在使用
        $isUSer = $domain->isUser($this->regulatorId,$this->catId);
        if(!empty($isUSer)) {
            DI()->logger->debug('Being used', $this->catId);

            $rs['code'] = 137;
            $rs['msg'] = T('Being used');
            return $rs;
        }
        $res = $domain->deleteCat($this->catId);
        if( $res){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info'] = $status;

        return $rs;
    }
	
}
