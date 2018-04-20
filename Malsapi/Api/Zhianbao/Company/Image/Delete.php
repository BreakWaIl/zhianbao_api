<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_Image_Delete extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id','type'=>'int','require'=> true,'desc'=> '公司ID'),
                     'imgIds' => array('name' => 'img_id', 'type' => 'array', 'format' => 'json', 'require' => true, 'desc' => '图片ID'),
            ),
		);
 	}
	
  
  /**
     * 删除图片
     * #desc 用于删除图片
     * #return int code 操作码，0表示成功
     * #return int img_it 图片ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //查找图片
        $domainImage = new Domain_Zhianbao_Image();
        foreach ($this->imgIds as $key => $value) {
            $filter = array(
                'company_id' => $this->companyId,
                'id' => $value
            );
            $info = $domainImage->getImage($filter);

            if (empty($info)) {
                DI()->logger->debug('Image not exis', $this->imgIds);

                $rs['code'] = 119;
                $rs['msg'] = T('Image not exists');
                return $rs;
            }
        }
        try {

            DI ()->notorm->beginTransaction ( 'db_api' );
            $imgId = $domainImage->deleteImage($this->imgIds);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }

        
        $rs['info']['img_id'] = $this->imgIds;

        return $rs;
    }
	
}
