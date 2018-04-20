<?php
class Domain_Zhianbao_SafeApply {
	var $model;

	public function __construct() {
		$this->model = new Model_Zhianbao_SafeApply ();
	}

	//获取申请详情
    public function getBaseInfo($applyId, $cols = '*'){
        $rs = array ();
        $id = intval ( $applyId );
        if ($id <= 0) {
            return $rs;
        }

        $rs = $this->model->get ( $id);

        if (! $rs){
            return false;
        }else{
            $rs['apply_time'] = $rs['apply_time'] == 0 ? '': date('Y-m-d H:i:s',$rs['apply_time']);
            $rs['review_time'] = $rs['review_time'] == 0 ? '': date('Y-m-d H:i:s',$rs['review_time']);
            $rs['create_time'] = $rs['create_time'] == 0 ? '': date('Y-m-d H:i:s',$rs['create_time']);
            $rs['last_modify'] = $rs['last_modify'] == 0 ? '': date('Y-m-d H:i:s',$rs['last_modify']);
            $rs['file_path'] = substr(strrchr($rs['file_path'], '-'), 1);
            //获取申请日志
            $applyLogModel = new Model_Zhianbao_SafeApplyLog();
            $filter = array('apply_id' => $rs['id'], 'company_id' => $rs['company_id']);
            $list = $applyLogModel->getAll('*',$filter);
            $rs['apply_log'] = $list;
        }
        return $rs;
    }
    //添加申请
    public function addApply($data){
        $filter = array('company_id' => $data['company_id']);
        $list = $this->model->getAll('*',$filter);
        // print_r($list);exit;
        if(empty($list)){
            $data['apply_theway'] = '首次申请';
        }else{
            foreach ($list as $key=>$value){
                if($value['status'] != 'finish'){
                    throw new LogicException ( T ( 'Please wait for review' ) , 119 );
                }
            }
            $data['apply_theway'] = '再次申请';
        }

        $file = $data['file_path'];
        unset($data['file_path']);

        $rs = $this->model->insert($data);
        if( !$rs){
            throw new LogicException ( T ( 'Apply failed' ) , 116 );
        }else{
            if(!empty($file)){
                $type = substr(strrchr($file, '.'), 1);
                $name = basename($file,".".$type);
                $current = file_get_contents($file);
               //$current = 'success';
                $file_url = '../file/' . time() .'-'.$name .'.'. $type;
                file_put_contents($file_url,$current);
                $file_data = array('file_path' => $file_url);
                $this->model->update($rs,$file_data);
            }
            //插入申报日志
            $applyLogModel = new Model_Zhianbao_SafeApplyLog();
            $log_data = array(
                'apply_id' => $rs,
                'company_id' => $data['company_id'],
                'user_id' => $data['user_id'],
                'user_name' => $data['user_name'],
                'content' => '申报创建成功',
                'create_time' => time(),
                'last_modify' => time(),
            );
            $log = $applyLogModel->insert($log_data);
            if(!$log){
                throw new LogicException ( T ( 'Apply failed' ) , 116 );
            }
        }
        return $rs;
    }
    //更新申请
    public function updateApply($data){
        $id = intval($data['apply_id']);
        unset($data['apply_id']);
        $rs = $this->model->update($id,$data);
        return $rs;
    }
    //获取列表
	public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
		$rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key=>$value){
            $rs[$key]['apply_time'] = $value['apply_time'] == 0 ? '': date('Y-m-d H:i:s',$value['apply_time']);
        }
		return $rs;
	}
	//获取数量
	public function getCount($filter) {
		return $this->model->getCount ( $filter );
	}

	//提交申报
    public function submitApply($companyId,$applyId,$userInfo){
        $data = array('status' => 'applying', 'apply_time' => time());
        $rs = $this->model->update($applyId,$data);
        if($rs){
            //添加申报日志
            $applyLogModel = new Model_Zhianbao_SafeApplyLog();
            $log_data = array(
                'apply_id' => $applyId,
                'company_id' => $companyId,
                'user_id' => $userInfo['id'],
                'user_name' => $userInfo['name'],
                'content' => '申报提交成功',
                'create_time' => time(),
                'last_modify' => time(),
            );
            $log = $applyLogModel->insert($log_data);
            if(!$log){
                throw new LogicException ( T ( 'Apply failed' ) , 116 );
            }
        }else{
            throw new LogicException ( T ( 'Apply failed' ) , 116 );
        }
        return $rs;
    }
    //获取申请详情
    public function getReviewInfo($regulatorId,$applyId){
        $rs  = $this->getBaseInfo($applyId);
        if (! $rs){
            return false;
        }else{
            $gradeModel = new Model_Zhianbao_SafeCompanyGrade();
            $filter = array('apply_id' => $applyId, 'company_id'=>$rs['company_id']);
            $info = $gradeModel->getByWhere($filter, 'mechanism,cert_bn,issue_time,end_time,next_apply_time');
            $rs['mechanism'] = $info['mechanism'];
            $rs['cert_bn'] = $info['cert_bn'];
            $rs['issue_time'] = date("Y-m-d H:i:s", $info['issue_time']);
            $rs['end_time'] = date("Y-m-d H:i:s", $info['end_time']);
            $rs['next_apply_time'] = date("Y-m-d H:i:s", $info['next_apply_time']);
        }
        return $rs;
    }
    //开始审核
    public function review($applyInfo){
        $data = array('status' => 'review', 'review_time' => time());
        $rs = $this->model->update($applyInfo['id'],$data);
        if($rs){
            //添加申报日志
            $applyLogModel = new Model_Zhianbao_SafeApplyLog();
            $log_data = array(
                'apply_id' => $applyInfo['id'],
                'company_id' => $applyInfo['company_id'],
                'user_id' =>$applyInfo['user_id'],
                'user_name' => $applyInfo['user_name'],
                'content' => '申报审核中',
                'create_time' => time(),
                'last_modify' => time(),
            );
            $log = $applyLogModel->insert($log_data);
            if(!$log){
                throw new LogicException ( T ( 'Update failed' ) , 104 );
            }
        }
        return $rs;
    }
    //初审
    public function firstReview($applyInfo,$reviewRemark){
        $update_data = array('status' => 'firstReview', 'review_time' => time());
        $rs = $this->model->update($applyInfo['id'],$update_data);
        if($rs){
            //添加申报日志
            $applyLogModel = new Model_Zhianbao_SafeApplyLog();
            $log_data = array(
                'apply_id' => $applyInfo['id'],
                'company_id' => $applyInfo['company_id'],
                'user_id' =>$applyInfo['user_id'],
                'user_name' => $applyInfo['user_name'],
                'content' => '申报初审完成：理由'.$reviewRemark,
                'create_time' => time(),
                'last_modify' => time(),
            );
            $log = $applyLogModel->insert($log_data);
            if(!$log){
                throw new LogicException ( T ( 'Update failed' ) , 104 );
            }
        }
    }
    //初审拒绝
    public function firstReject($applyInfo,$rejectRemark){
        $update_data = array('status' => 'firstReject', 'last_modify' => time(),);
        $rs = $this->model->update($applyInfo['id'],$update_data);
        if($rs){
            //添加申报日志
            $applyLogModel = new Model_Zhianbao_SafeApplyLog();
            $log_data = array(
                'apply_id' => $applyInfo['id'],
                'company_id' => $applyInfo['company_id'],
                'user_id' =>$applyInfo['user_id'],
                'user_name' => $applyInfo['user_name'],
                'content' => '申报初审拒绝，理由：'.$rejectRemark,
                'create_time' => time(),
                'last_modify' => time(),
            );
            $log = $applyLogModel->insert($log_data);
            if(!$log){
                throw new LogicException ( T ( 'Update failed' ) , 104 );
            }
        }
    }
    //复审
    public function finishReview($applyInfo,$data){
        $update_data = array('status' => 'finish', 'review_time' => time());
        $end_review_remark = $data['end_review_remark'];
        unset($data['end_review_remark']);
        $rs = $this->model->update($applyInfo['id'],$update_data);
        if($rs){
            //添加申报日志
            $applyLogModel = new Model_Zhianbao_SafeApplyLog();
            $log_data = array(
                'apply_id' => $applyInfo['id'],
                'company_id' => $applyInfo['company_id'],
                'user_id' =>$applyInfo['user_id'],
                'user_name' => $applyInfo['user_name'],
                'content' => '申报复审完成：理由'.$end_review_remark,
                'create_time' => time(),
                'last_modify' => time(),
            );
            $log = $applyLogModel->insert($log_data);
            if(!$log){
                throw new LogicException ( T ( 'Update failed' ) , 104 );
            }
            //添加公司等级
            $companyGradeModel = new Model_Zhianbao_SafeCompanyGrade();
            $grade_data = array(
                'apply_id' => $applyInfo['id'],
                'company_id' => $applyInfo['company_id'],
                'apply_grade' =>$applyInfo['apply_grade'],
                'mechanism' => $data['mechanism'],
                'issue_time' => $data['issue_time'],
                'complete_time' => time(),
                'end_time' => $data['end_time'],
                'apply_time' => strtotime($applyInfo['apply_time']),
                'next_apply_time' => time() + (3 * 365 * 86400),
                'status' => 'finish',
                'create_time' => time(),
                'last_modify' => time(),
                'cert_bn' => $data['cert_bn'],
            );
            $grade = $companyGradeModel->insert($grade_data);
            if(!$grade){
                throw new LogicException ( T ( 'Update failed' ) , 104 );
            }
            //公司等级日志
            $companyGradeLogModel = new Model_Zhianbao_SafeCompanyGradeLog();
            $grade_log_data = array(
                'apply_id' => $applyInfo['id'],
                'company_id' => $applyInfo['company_id'],
                'apply_grade' =>$applyInfo['apply_grade'],
                'mechanism' => $data['mechanism'],
                'issue_time' => $data['issue_time'],
                'complete_time' => time(),
                'end_time' => $data['end_time'],
                'apply_time' => strtotime($applyInfo['apply_time']),
                'next_apply_time' => time() + (3 * 365 * 86400),
                'status' => 'finish',
                'create_time' => time(),
                'last_modify' => time(),
                'cert_bn' => $data['cert_bn'],
            );
            $gradeLog = $companyGradeLogModel->insert($grade_log_data);
            if(!$gradeLog){
                throw new LogicException ( T ( 'Update failed' ) , 104 );
            }
        }
    }
    //复审拒绝
    public function EndReject($applyInfo,$endRejectRemark){
        $update_data = array('status' => 'endReject', 'last_modify' => time(),);
        $rs = $this->model->update($applyInfo['id'],$update_data);
        if($rs){
            //添加申报日志
            $applyLogModel = new Model_Zhianbao_SafeApplyLog();
            $log_data = array(
                'apply_id' => $applyInfo['id'],
                'company_id' => $applyInfo['company_id'],
                'user_id' =>$applyInfo['user_id'],
                'user_name' => $applyInfo['user_name'],
                'content' => '申报复审拒绝，理由：'.$endRejectRemark,
                'create_time' => time(),
                'last_modify' => time(),
            );
            $log = $applyLogModel->insert($log_data);
            if(!$log){
                throw new LogicException ( T ( 'Update failed' ) , 104 );
            }
        }
    }
}
