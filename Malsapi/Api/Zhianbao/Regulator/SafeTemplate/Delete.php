<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_SafeTemplate_Delete extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '监管者ID'),
                     'templateId' => array('name' => 'template_id','type'=>'int', 'min' => 1, 'require'=> true,'desc'=> '模板ID'),
            ),
		);
 	}
  
  /**
   * 删除生产安全标准化模板
   * #desc 用于删除生产安全标准化模板
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

        //判断模板是否存在
        $templateDomain = new Domain_Zhianbao_SafeTemplate();
        $templateInfo = $templateDomain->getBaseInfo($this->templateId);
        if(! $templateInfo){
            DI()->logger->debug('Template not found', $this->templateId);

            $rs['code'] = 113;
            $rs['msg'] = T('Template not exists');
            return $rs;
        }
        //检测该模板是否正在使用
        $info = $templateDomain->isUser($this->regulatorId,$this->templateId);
        if(!empty($info)){
            DI()->logger->debug('Being used', $this->typeId);

            $rs['code'] = 137;
            $rs['msg'] = T('Being used');
            return $rs;
        }


        $delRs = $templateDomain->deleteTemplate($this->templateId);
        if($delRs){
            $status = 0;
        }else{
            $status = 1;
        }
        $rs['status'] = $status;
        return $rs;
    }
	
}
