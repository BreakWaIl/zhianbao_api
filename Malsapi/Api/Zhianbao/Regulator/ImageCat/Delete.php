<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_ImageCat_Delete extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'imgCatId' => array('name' => 'img_cat_id', 'type' => 'int', 'min' => 1, 'require' => true,  'desc' => '分组ID'),
            ),
		);
 	}
	
  
  /**
     * 删除图片分类
     * #desc 用于添加图片分类
     * #return int code 操作码，0表示成功
     * #return int img_cat_id 图片ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $domain = new Domain_Zhianbao_ImageCat();

        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $imgId = $domain->deleteImgCat($this->imgCatId);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }


        $rs['info']['img_cat_id'] = $this->imgCatId;

        return $rs;
    }
	
}
