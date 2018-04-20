<?php
class Domain_Zhianbao_CheckPlan {
	var $model;

	public function __construct() {
		$this->model = new Model_Zhianbao_CheckPlan ();
	}
    public function getCount($filter) {
        return $this->model->getCount ( $filter );
    }
    //获取详情
    public function getBaseInfo($id, $cols = '*') {
	    $resultModel = new Model_Zhianbao_CheckResult();
	    $filter = array(
	        'plan_id' => $id
        );
	    $result = $resultModel->getAll('*',$filter);
        $result = $this->sortResultByType($result);
        $rs = $this->model->get($id,$cols);
        $rs['result'] = $result;
        $rs['check_time'] = date('Y-m-d H:i:s',$rs['check_time']);
        return $rs;
    }
    //根据类型分类检查结果
    public function sortResultByType($result){
	    $hiddProjectModel = new Model_Zhianbao_HiddProject();
	    $hiddTypeModel  = new Model_Zhianbao_HiddType();
	    $return = array();
	    foreach ($result as $key => $value){
            $projectInfo = $hiddProjectModel->get($value['hidd_project_id']);
            $value['hidd_project_title'] = $projectInfo['title'];
            $value['hidd_project_content'] = $projectInfo['content'];
            $typeId = $projectInfo['type_id'];
            $typeInfo = $hiddTypeModel->get($typeId);
            $value['hidd_type_id'] = $typeId;
            $value['hidd_type_name'] = $typeInfo['name'];
            $return[$typeId]['project'][] = $value;
            $return[$typeId]['hidd_type_id'] = $typeId;
            $return[$typeId]['hidd_type_name'] = $typeInfo['name'];
        }
        sort($return);
        return $return;
    }
    //添加
    public function addCheckPlan($data){
	    $checkResultModel = new Model_Zhianbao_CheckResult();
	    $content = $data['content'];
	    $data['content'] = json_encode($data['content']);
        $CheckPlanId = $this->model->insert($data);
        if(! $CheckPlanId){
            throw new LogicException ( T ( 'Add failed' ), 102 );
        }
        //添加检查内容
        foreach ($content as $key => $value){
            $data = array(
                'company_id' => $data['company_id'],
                'plan_id' => $CheckPlanId,
                'hidd_project_id' => $value,
                'check_result' => 0,
                'create_time' => time()
            );
            $resultId = $checkResultModel -> insert($data);
            if(! $resultId){
                throw new LogicException ( T ( 'Add failed' ), 102 );
            }
        }
        return $CheckPlanId;
    }
    //删除
    public function delCheckPlan($id){
        $rs = $this->model->delete($id);
        return $rs;
    }
    //更新
    public function updateCheckPlan($id,$data){
        $rs = $this->model->update($id,$data);
        return $rs;
    }
    public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = '') {
        $rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        return $rs;
    }
    //检查
    public function doCheck($planId,$data){
        $reportModel = new Model_Zhianbao_CheckReport();
        $planInfo = $this->model->get($planId);
        if(! $planInfo){
            throw new LogicException ( T ( 'Update failed' ), 104 );
        }
        $checkResultModel = new Model_Zhianbao_CheckResult();
        //更新检查结果
        foreach ($data as $key => $value){
            $filter = array(
                'plan_id' => $planId,
                'hidd_project_id' => $value['project_id'],
            );
            $updateData = array(
                'check_result' => $value['status'],
                'message' => $value['message'],
                'last_modify' => time()
            );

            $resultInfo = $checkResultModel->getByWhere($filter);
            if($resultInfo['need_change'] == 1 && $value['status'] == 1){
                //当待整改&整改结果为1 改为已整改
                $updateData['need_change'] = 2;
            }
            if($value['status'] == 2){
                $updateData['need_change'] = 1;
            }

            $rs = $checkResultModel->updateByWhere($filter,$updateData);
            if(! $rs){
                throw new LogicException ( T ( 'Update failed' ), 104 );
            }
        }
        //更新检查计划为已检查过
        $planData = array('status' => 1,'last_modify' =>time());
        $rs = $this->model->update($planId,$planData);
        if(! $rs){
            throw new LogicException ( T ( 'Update failed' ), 104 );
        }
        //添加检查日志
        $reportData = array(
            'company_id' => $planInfo['company_id'],
            'plan_id' => $planId,
            'content' => json_encode($data),
            'create_time' => time()
        );
        $reportId = $reportModel->insert($reportData);
        if(! $reportId){
            throw new LogicException ( T ( 'Update failed' ), 104 );
        }
        return true;
    }
    //完成检查计划
    public function finishCheckPlan($planId,$summary){
        $checkResultModel = new Model_Zhianbao_CheckResult();
        $filter = array('plan_id' => $planId);
        $checkResultList = $checkResultModel->getAll('*',$filter);
        foreach ($checkResultList as $key => $value){
            if($value['check_result'] != 1){
                //包含未通过的项目
                throw new LogicException ( T ( 'Content unpass project' ), 114 );
            }
        }
        //更新检查计划为完成
        $updateData = array('status' => 2,'summary'=>$summary,'last_modify'=> time());
        $rs = $this->model->update($planId,$updateData);
        if(! $rs){
            throw new LogicException ( T ( 'Update failed' ), 104 );
        }
        return true;
    }
    //获取公司上次检查结果
    public function getLastCheck($companyId){
        $checkResultModel = new Model_Zhianbao_CheckResult();
        $filter = array('company_id' => $companyId);
        $planInfo = $this->model->getAll('*',$filter,1,1,'create_time:desc');
        if($planInfo){
            $planInfo = $planInfo[0];
            $hiddCountFilter = array('plan_id' => $planInfo['id'],'check_result' => 2);
            $hiddCount = $checkResultModel->getCount($hiddCountFilter);
            $changeCountFilter = array('plan_id' => $planInfo['id'],'need_change' => 2);
            $changeCount = $checkResultModel->getCount($changeCountFilter);
            $unChangeCountFilter = array('plan_id' => $planInfo['id'],'need_change' => 1);
            $unChangeCount = $checkResultModel->getCount($unChangeCountFilter);
            $checkTime = date('Y-m-d H:i:s',$planInfo['check_time']);
        }else{
            $hiddCount = '-';
            $changeCount = '-';
            $unChangeCount = '-';
            $checkTime = '-';
        }
        $rs['hidd_count'] = $hiddCount;
        $rs['change_count'] = $changeCount;
        $rs['unChange_count'] = $unChangeCount;
        $rs['check_time'] = $checkTime;
        return $rs;
    }
}
