<?php
class Domain_Zhianbao_Image {
	var $model;

	public function __construct() {
		$this->model = new Model_Zhianbao_Image ();
	}
    public function getImgById($imgId){
	    return $this->model->get($imgId);
    }
	//获取数量
	public function getCount($filter) {
		return $this->model->getCount ( $filter );
	}
	//添加图片
	public function addImage($data){
		$img_source = $data['img_source'];
		foreach($data['img_content'] as $key => $value ) {
			if ($img_source == 1) {
				$isImg = $this->isImg($value);
				if(! $isImg){
					throw new LogicException ( T ( 'Remote picture not found' ), 130 );
				}
				$data['img_url'] = $value;
			} else {

				if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $value, $result)){
					$type = $result[2];
					$img_url = '../images/' . time() . mt_rand(1000, 9999) .'.'. $type;
					file_put_contents($img_url, base64_decode(str_replace($result[1], '', $value)));
				}
//				$qcloudcos = new Qcloudcos_Qcloud();
//				$data['img_url'] = $qcloudcos->uploadImg($img_url,$type);
//				if(empty($data['img_url'])){
//					throw new LogicException ( T ( 'Image upload failed' ), 131 );
//				}
                $data['img_url'] = $img_url;
				$data['local_img_url'] = $img_url;
			}
			unset($data['img_source']);
			unset($data['img_content']);
			$rs = $this->model->insert($data);
			if(! $rs){
				throw new LogicException ( T ( 'Image upload failed' ), 131 );
			}
		}
		return $rs;
	}
	//判断远程图片是否存在
	public function isImg($url){
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		//不下载
		curl_setopt($ch, CURLOPT_NOBODY, 1);
		//设置超时
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 3);
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);
		curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($http_code == 200) {
			return true;
		}
		return false;
	}
	public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = '') {
		$rs = $this->model->getAll ( 'id,img_url', $filter, $page, $page_size, $orderby );
		foreach ($rs as $key => $value){
		    $rs[$key]['img_url'] = DI ()->config->get ( 'app.api_root' ).str_replace('../','/',$value['img_url']);
        }
		return $rs;
	}
	//查找图片
	public function getImage($filter){
		$rs = $this->model->getByWhere($filter);
		return $rs;
	}
	//删除图片
	public function deleteImage($imgIds){
		foreach ($imgIds as $key => $value) {
			$data = array('is_del'=>'y');
			$rs = $this->model->update($value,$data);
			if(! $rs){
				throw new LogicException ( T ( 'Delete failed' ), 133 );
			}
		}
		return $rs;
	}
	//更新图片分类
    public function updateImage($imgIds,$imgCatId){
        $filter = array(
            'id' => $imgIds
        );
        $data = array(
            'img_cat_id' => $imgCatId
        );
        $rs = $this->model->updateByWhere($filter,$data);
        return $rs;
    }

}
