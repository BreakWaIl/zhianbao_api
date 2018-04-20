<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_ImageCat_ListGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '公司ID'),
            ),
		);
 	}
	
  
  /**
     * 获取图片分类列表
     * #desc 用于获取图片分类列表
     * #return int code 操作码，0表示成功
     * #return int id 分组ID
     * #return string name 分组名称
     * #return int name 分组名称
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

        $domain = new Domain_Zhianbao_ImageCat();
        $list = $domain->getCatListByRegulatorId($this->regulatorId);
        $rs['list'] = $list;

        return $rs;
    }
	
}
