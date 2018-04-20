<?php
class Domain_Zhianbao_Knowledge {
	var $model;

	public function __construct() {
		$this->model = new Model_Zhianbao_Knowledge ();
	}

    //获取文章详情
    public function getBaseInfo($articleId, $cols = '*') {
        $rs = array ();

        $articleId = intval ( $articleId );
        if ($articleId <= 0) {
            return $rs;
        }

        // 版本1：简单的获取
        $rs = $this->model->get($articleId);

        if (! $rs)
            return false;

        return $rs;
    }

    //添加文章
    public function addArticle($data){
        $rs = $this->model->insert($data);
        return $rs;
    }

    //更新文章
    public function updateArticle($data){
        $id = intval($data['article_id']);
        unset($data['article_id']);
        $rs = $this->model->update($id,$data);
        return $rs;
    }

    //删除文章
    public function deleteArticle($articleId){
        $rs = $this->model->delete($articleId);
        return $rs;
    }

	//获取文章列表
	public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
        $rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
		return $rs;
	}

	//获取数量
	public function getCount($filter) {
		return $this->model->getCount ( $filter );
	}

	public function getCompany($articleId,$regulatorId){
	    $filter = array('id' => $articleId, 'regulator_id' => $regulatorId['regulator_id']);
        $info =$this->model->getByWhere($filter, '*');
        $info['create_time'] = date('Y-m-d H:i:s',$info['create_time']);
        $info['last_modify'] = date('Y-m-d H:i:s',$info['last_modify']);
        return $info;
    }

}
