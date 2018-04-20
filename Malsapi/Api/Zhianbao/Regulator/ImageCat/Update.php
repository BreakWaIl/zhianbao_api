<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_ImageCat_Update extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'imgCatId' => array('name' => 'img_cat_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '分组ID'),
                     'imgCatName' => array('name' => 'img_cat_name', 'type' => 'string', 'min' => 1, 'require' => true,  'desc' => '分类名称'),
            ),
		);
 	}
	
  
  /**
     * 更新图片分类
     * #desc 用于更新图片分类
     * #return int code 操作码，0表示成功
     * #return int status 0:更新成功 1:更新失败
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //查询分组
        $domainCat = new Domain_Zhianbao_ImageCat();
        $catInfo = $domainCat->getBaseInfo($this->imgCatId);
        if (empty($catInfo)) {
            DI()->logger->debug('cat not found', $this->imgCatId);

            $rs['code'] = 109;
            $rs['msg'] = T('Cat not exists');
            return $rs;
        }

        $data = array(
            'name' =>$this->imgCatName,
            'last_modify' => time()
        );
        $result = $domainCat->updateImgCat($this->imgCatId,$data);
        if(! $result){
            $rs['code'] = 108;
            $rs['msg'] = T('Update failed');
            return $rs;
        }else{
            $rs['status'] = 0;
        }


        return $rs;
    }
	
}
