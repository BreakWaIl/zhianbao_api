<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_ImageCat_Add extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'companyId' => array('name' => 'company_id','type'=>'int','require'=> true,'desc'=> '公司ID'),
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
        //检测公司是否存在
        $companyDomain = new Domain_Zhianbao_Company();
        $companyInfo = $companyDomain->getBaseInfo($this->companyId);
        if(! $companyInfo){
            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }

        $data = array(
            'company_id' =>$this->companyId,
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
