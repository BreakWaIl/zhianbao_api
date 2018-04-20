<?php
class Domain_Zhianbao_ImageCat {
	var $model;

	public function __construct() {
		$this->model = new Model_Zhianbao_ImageCat ();
	}

	//获取数量
	public function getCount($filter) {
		return $this->model->getCount ( $filter );
	}
	//添加图片分组
	public function addImageCat($data){
		return $this->model->insert($data);
	}
	//删除图片分组
	public function deleteImgCat($catId){
		//删除图片分组
		$rs = $this->model->delete($catId);
		if(! $rs){
			throw new LogicException ( T ( 'Delete failed' ), 133 );
		}
		//更新当前图片分组下面的图片至未分组
		$filter = array(
			'img_cat_id' => $catId
		);
		$imgModel = new Model_Zhianbao_Image();
		$imgModel->updateByWhere($filter,array('img_cat_id'=>0));
		return true;
	}
	//查询分类信息
	public function getBaseInfo($catId){
		$rs = $this->model->get($catId);
		return $rs;
	}
	//更新分组
	public function updateImgCat($catId,$data){
		$rs = $this->model->update($catId,$data);
		return $rs;
	}
	//获取分组列表
	public function getCatList($companyId){
		$filter = array(
			'company_id' => $companyId
		);
		$rs = $this->model->getAll('*',$filter);
		$imgModel = new Model_Zhianbao_Image();
		foreach ($rs as $key => $value){
			$filter = array(
				'img_cat_id' => $value['id'],
                'company_id' => $companyId,
                'is_del' => 'n'
			);
			$count = $imgModel->getCount($filter);
			$rs[$key]['count'] = $count;
		}
		//获取未分组图片数量
		$filter = array(
			'img_cat_id' => 0,
            'company_id' => $companyId,
            'is_del' => 'n'
		);
		$memoCount = $imgModel->getCount($filter);
		$memo[] = array(
		    'id' => 0,
			'company_id' => $companyId,
			'name' => '未分组',
			'count' => $memoCount
		);
		$rs = array_merge($memo,$rs);
		return $rs;
	}
    //获取分组列表
    public function getCatListByRegulatorId($regulatorId){
        $filter = array(
            'regulator_id' => $regulatorId
        );
        $rs = $this->model->getAll('*',$filter);
        $imgModel = new Model_Zhianbao_Image();
        foreach ($rs as $key => $value){
            $filter = array(
                'img_cat_id' => $value['id'],
                'regulator_id' => $regulatorId,
                'is_del' => 'n'
            );
            $count = $imgModel->getCount($filter);
            $rs[$key]['count'] = $count;
        }
        //获取未分组图片数量
        $filter = array(
            'img_cat_id' => 0,
            'regulator_id' => $regulatorId,
            'is_del' => 'n'
        );
        $memoCount = $imgModel->getCount($filter);
        $memo[] = array(
            'id' => 0,
            'regulator_id' => $regulatorId,
            'name' => '未分组',
            'count' => $memoCount
        );
        $rs = array_merge($memo,$rs);
        return $rs;
    }

}
