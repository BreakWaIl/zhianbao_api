<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_HiddType_InfoGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'typeId' => array('name' => 'type_id','type'=>'int','require'=> true,'desc'=> '隐患类型ID'),
            ),
		);
 	}

  
  /**
     * 获取隐患类型详情
     * #desc 用于获取隐患类型详情
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //查看隐患类型是否存在
        $hiddTypeDomain = new Domain_Zhianbao_HiddType();
        $typeInfo = $hiddTypeDomain->getBaseInfo($this->typeId);
        if(! $typeInfo){
            $rs['code'] = 103;
            $rs['msg'] = T('Hidd type not exists');
            return $rs;
        }

        $rs['info'] = $typeInfo;
        return $rs;
    }

}

