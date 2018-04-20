<?php
class Domain_Zhianbao_KnowledgeCategroy {
	var $model;

	public function __construct() {
		$this->model = new Model_Zhianbao_KnowledgeCategroy ();
	}

    //获取分类详情
    public function getBaseInfo($catId, $cols = '*') {
        $rs = array ();

        $catId = intval ( $catId );
        if ($catId <= 0) {
            return $rs;
        }

        // 版本1：简单的获取
        $rs = $this->model->get($catId);

        if (! $rs)
            return false;

        return $rs;
    }

    //添加分类
    public function addCat($data){
        $rs = $this->model->insert($data);
        return $rs;
    }

    //更新分类
    public function updateCat($data){
        $id = intval($data['cat_id']);
        unset($data['cat_id']);
        $rs = $this->model->update($id,$data);
        return $rs;
    }

    //删除分类
    public function deleteCat($catId){
        $rs = $this->model->delete($catId);
        return $rs;
    }

	//获取分类列表
	public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
        $knowledgeModel = new Model_Zhianbao_Knowledge();
        $rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key=>$value){
            $to_filter = array( 'regulator_id'=> $value['regulator_id'],'cat_id' => $value['id']);
            $total = $knowledgeModel->getCount($to_filter);
            $rs[$key]['know_count'] = $total;
        }
		return $rs;
	}

	//获取数量
	public function getCount($filter) {
		return $this->model->getCount ( $filter );
	}

	public function getName($regulatorId,$catName){
	    $filter = array('regulator_id' => $regulatorId, 'cat_name'=> $catName);
	    $rs = $this->model->getByWhere($filter, '*');
        return $rs;
    }

    //获取分类下的文章
    public function isUser($regulatorId,$catId){
        $knowModel = new Model_Zhianbao_Knowledge();
        $filter = array('regulator_id' => $regulatorId, 'cat_id' => $catId);
        $rs = $knowModel->getByWhere($filter, '*');
        return $rs;
    }
    //获取知识库分类
    public function getAllCat($filter,$catName){
        $rs = array();
        $regTocustomerModel = new Model_Zhianbao_RegulatorToCustomer();
        $info = $regTocustomerModel->getByWhere($filter, '*');
        if(!empty($info)){
            $to_filter = array('regulator_id' => $info['regulator_id']);
            if(!empty($catName)){
                $to_filter['cat_name LIKE ?'] = '%'.$catName.'%';
            }
            $rs = $this->model->getAll('*', $to_filter);
        }
        return $rs;
    }
    //
    public function getRegulator($companyId){
        $regTocustomerModel = new Model_Zhianbao_RegulatorToCustomer();
        $filter = array('company_id' => $companyId);
        $rs = $regTocustomerModel->getByWhere($filter,'*');
        return $rs;
    }

    //首页知识库统计
    public function getKnowCat($filter){
        $regTocustomerModel = new Model_Zhianbao_RegulatorToCustomer();
        $knowModel = new Model_Zhianbao_Knowledge();
        $regInfo = $regTocustomerModel->getByWhere($filter, '*');
        $to_filter = array('regulator_id' => $regInfo['regulator_id']);
        $list = $this->model->getAll('*', $to_filter);
        foreach ($list as $key=>$value){
            $cat_filter = array('regulator_id' => $regInfo['regulator_id'],'cat_id' => $value['id']);
            $know_list = $knowModel->getAll('*',$cat_filter,1,3,'id:desc');
            $list[$key]['know_list'] = $know_list;
        }
        return $list;
    }
}
