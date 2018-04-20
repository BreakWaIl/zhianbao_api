<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Company_Image_Update extends PhalApi_Api
{

    public function getRules()
    {
        return array(
            'Go' => array(
                'imgIds' => array('name' => 'img_id', 'type' => 'array', 'format' => 'json', 'require' => true, 'desc' => '图片ID'),
                'imgCatId' => array('name' => 'img_cat_id', 'type' => 'int', 'min' => 0, 'require' => true, 'desc' => '分组ID'),
            ),
        );
    }


    /**
     * 更新店铺图片
     * #desc 用于更新店铺图片
     * #return int code 操作码，0表示成功
     * #return int img_id 图片ID
     */
    public function Go()
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //查找图片
        $domainImage = new Domain_Zhianbao_Image();
        foreach ($this->imgIds as $key => $value) {
            $filter = array(
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
        if($this->imgCatId  != 0 ) {
            //查询分组
            $domainCat = new Domain_Zhianbao_ImageCat();
            $catInfo = $domainCat->getBaseInfo($this->imgCatId);
            if (empty($catInfo)) {
                DI()->logger->debug('cat not found', $this->imgCatId);

                $rs['code'] = 109;
                $rs['msg'] = T('Cat not exists');
                return $rs;
            }
        }
        try {

            DI()->notorm->beginTransaction('db_api');
            $imgId = $domainImage->updateImage($this->imgIds, $this->imgCatId);
            DI()->notorm->commit('db_api');

        } catch (Exception $e) {

            DI()->notorm->rollback('db_api');
            $rs ['code'] = $e->getCode();
            $rs ['msg'] = $e->getMessage();
        }


        $rs['info']['img_id'] = $this->imgIds;
        return $rs;
    }
}
