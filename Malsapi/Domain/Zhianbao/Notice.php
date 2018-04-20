<?php
class Domain_Zhianbao_Notice {
	var $model;

	public function __construct() {
		$this->model = new Model_Zhianbao_Notice ();
	}

	//获取通知详情
    public function getBaseInfo($noticeId, $cols = '*'){
        $rs = array ();
        $id = intval ( $noticeId );
        if ($id <= 0) {
            return $rs;
        }

        $rs = $this->model->get ( $id);

        if (! $rs){
            return false;
        }else{
            $rs['create_time'] = $rs['create_time'] == 0 ? '': date('Y-m-d H:i:s',$rs['create_time']);
            $rs['last_modify'] = $rs['last_modify'] == 0 ? '': date('Y-m-d H:i:s',$rs['last_modify']);
            $rs['release_time'] = $rs['release_time'] == 0 ? '': date('Y-m-d H:i:s',$rs['release_time']);
        }

        return $rs;
    }
    //添加发文通知
    public function addNotice($data){
        $rs = $this->model->insert($data);
        return $rs;
    }
    //更新发文通知
    public function updateNotice($data){
        $id = intval($data['notice_id']);
        unset($data['notice_id']);
        $rs = $this->model->update($id,$data);
        return $rs;
    }
    //获取通知列表
	public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
		$rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        $regToCustomerModel = new Model_Zhianbao_RegulatorToCustomer();
        $noticeToReleaseModel = new Model_Zhianbao_NoticeToRelease();
        $filter = array('regulator_id' => $filter['regulator_id']);
        $list = $regToCustomerModel->getAll('*', $filter);
        $total = COUNT($list);
        foreach ($rs as $key=>$value){
            $to_filter = array( 'regulator_id' => $value['regulator_id'], 'notice_id' => $value['id']);
            $list = $noticeToReleaseModel->getAll('*', $to_filter);
            $count = 0;
            if(!empty($list)){
                foreach ($list as $k=>$v){
                    if($v['is_sign'] == 'y'){
                        $count++;
                    }
                }
                if($count == $total){
                    //更新通知签收状态
                    $this->model->update($value['id'],$data = array('is_sign' => 'y'));
                }
            }
            $rs[$key]['total'] = $total;
            $rs[$key]['sign_count'] = $count;
        }
		return $rs;
	}
	//获取数量
	public function getCount($filter) {
		return $this->model->getCount ( $filter );
	}
	//发布通知
    public function release($regulatorId,$noticeId){
        $data = array('is_release' => 'y', 'release_time' => time());
        $rs = $this->model->update($noticeId,$data);
        if( $rs){
            //获取下属监管公司
            $regToCustomerModel = new Model_Zhianbao_RegulatorToCustomer();
            $noticeToReleaseModel = new Model_Zhianbao_NoticeToRelease();
            $filter = array('regulator_id' => $regulatorId);
            $list = $regToCustomerModel->getAll('*', $filter);
            //print_r($list);exit;
            foreach ($list as $key=>$value){
                //更新通知和发布关系
                $data = array(
                    'regulator_id' => $value['regulator_id'],
                    'company_id' => $value['company_id'],
                    'notice_id' => $noticeId,
                );
                $res = $noticeToReleaseModel->insert($data);
                if(!$res){
                    throw new LogicException ( T ( 'Release failed' ) , 111 );
                }
            }
        }

        return $rs;
    }

    public function getTitle($regulatorId,$title){
        $filter = array('regulator_id' => $regulatorId, 'title'=> $title);
        $rs = $this->model->getByWhere($filter, '*');
        return $rs;
    }
    //获取公司的监管ID
    public function getCompanyIds($companyId){
        $regulatorToCustomerModel = new Model_Zhianbao_RegulatorToCustomer();
        $filter = array('company_id' => $companyId);
        $regulatorIds = $regulatorToCustomerModel->getByWhere($filter,'*');
        return $regulatorIds;
    }
    //搜索
    public function searchType($filter,$companyId){
        $to_filter = array(
            'regulator_id' => $filter['regulator_id'],
            'company_id' => $companyId,
            'is_sign' => $filter['is_sign'],
        );
        $noticeToReleaseModel = new Model_Zhianbao_NoticeToRelease();
        $list = $noticeToReleaseModel->getAll('*', $to_filter);
        $customerIds = array();
        foreach ($list as $key => $value){
            $customerIds[]  = $value['notice_id'];
        }
        return $customerIds;
    }
    //获取通知列表
    public function getAllCompany($filter, $page = 1, $page_size = 20, $orderby = ''){
        $rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        $noticeToReleaseModel = new Model_Zhianbao_NoticeToRelease();
        foreach ($rs as $key=>$value){
            $to_filter = array('regulator_id' => $value['regulator_id'], 'notice_id' => $value['id']);
            $info = $noticeToReleaseModel->getByWhere($to_filter,'*');
            $rs[$key]['is_sign'] = $info['is_sign'];
            $rs[$key]['sign_time'] = $info['sign_time'] == 0 ? '': date('Y-m-d H:i:s',$info['sign_time']);
        }
        return $rs;
    }
    //获取数量
    public function getCountCompany($filter) {
        return $this->model->getCount ( $filter );
    }
    //获取发文通知详情
    public function getNoticeInfo($noticeId,$companyId){
        $rs = array();
        $noticeToReleaseModel = new Model_Zhianbao_NoticeToRelease();
        $filter = array('notice_id' => $noticeId, 'company_id' => $companyId);
        $signInfo = $noticeToReleaseModel->getByWhere($filter,'*');
        if(empty($signInfo)){
            return null;
        }else{
            $info = $this->model->get($noticeId);
            $rs['id'] = $info['id'];
            $rs['title'] = $info['title'];
            $rs['content'] = $info['content'];
            $rs['is_release'] = $info['is_release'];
            $rs['release_time'] = $info['release_time'] == 0 ? '': date('Y-m-d H:i:s',$info['release_time']);
            $rs['create_time'] = $info['create_time'] == 0 ? '': date('Y-m-d H:i:s',$info['create_time']);
            $rs['last_modify'] = $info['last_modify'] == 0 ? '': date('Y-m-d H:i:s',$info['last_modify']);
            $rs['is_sign'] = $signInfo['is_sign'];
            $rs['sign_time'] = $signInfo['sign_time'] == 0 ? '': date('Y-m-d H:i:s',$signInfo['sign_time']);
        }
        return $rs;
    }
    public function SignInfo($noticeId,$companyId){
        $noticeToReleaseModel = new Model_Zhianbao_NoticeToRelease();
        $filter = array('notice_id' => $noticeId, 'company_id' => $companyId);
        $info = $noticeToReleaseModel->getByWhere($filter, '*');
        return $info;
    }
    //签收发文通知
    public function Sign($noticeId,$companyId){
        $noticeToReleaseModel = new Model_Zhianbao_NoticeToRelease();
        $filter = array('notice_id' => $noticeId, 'company_id' => $companyId);
        $data = array('is_sign' => 'y', 'sign_time' =>time());
        $rs = $noticeToReleaseModel->updateByWhere($filter,$data);
        return $rs;
    }
}
