<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_ImageCat_Add extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '公司ID'),
                     'imgCatName' => array('name' => 'img_cat_name', 'type' => 'string', 'min' => 1, 'require' => true,  'desc' => '分类名称'),
            ),
		);
 	}
	
  
  /**
     * 添加图片分类
     * #desc 用于添加图片分类
     * #return int code 操作码，0表示成功
     * #return int img_cat_id 分组ID
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
            'regulator_id' =>$this->regulatorId,
            'name' =>$this->imgCatName,
            'create_time' => time(),
        );

        $domain = new Domain_Zhianbao_ImageCat();

        try {

            DI ()->notorm->beginTransaction ( 'db_api' );
            $imgId = $domain->addImageCat($data);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }


        $rs['info']['img_cat_id'] = $imgId;

        return $rs;
    }
	
}
