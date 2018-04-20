<?php
class Domain_Jiafubao_SendStaff {
	public function __construct() {
		$this->model = new Model_Jiafubao_OrderSend();
	}
    //推荐家政员
    public function sendStaff($data,$staffInfo){
	    $orderModel = new Model_Jiafubao_Order();
        //判断是否已经推过
        $filter = array('staff_id' => $staffInfo['id'],'order_id'=> $data['order_id'],'status' => 'active');
        $sendInfo = $this->model->getByWhere($filter);
        if($sendInfo){
            throw new LogicException ( T ( 'Send staff fail' ), 230 );
        }
        $rs = $this->model->insert($data);
        if($rs){
            //修改订单推荐次数
            $orderData = array(
                'send_count' => new NotORM_Literal('send_count + 1 ')
            );
            $updateRs = $orderModel->update($data['order_id'],$orderData);
            if(! $updateRs){
                throw new LogicException ( T ( 'Send staff fail' ), 230 );
            }
            //添加分享的家政员
            $shareData = array(
                'company_id' => $data['company_id'],
                'send_company_id' => $data['send_company_id'],
                'send_company_name' => $data['send_company_name'],
                'send_company_mobile' => $data['send_company_mobile'],
                'staff_id' => $data['staff_id'],
                'name' => $staffInfo['name'],
                'sex' => $staffInfo['sex'],
                'native_place' => $staffInfo['native_place'],
                'birthday' => $staffInfo['birthday'],
                'create_time' => time(),
                'last_modify' => time(),
            );
            $shareRs = $this->addShareStaff($shareData);
            if(! $shareRs){
                throw new LogicException ( T ( 'Send staff fail' ), 230 );
            }
        }else{
            throw new LogicException ( T ( 'Send staff fail' ), 230 );
        }
        return $rs;
    }
    public function update($id,$data){
        $rs = $this->model->update($id,$data);
        return $rs;
    }
    public function getBaseInfo($id,$col = '*'){
        $rs = $this->model->get($id,$col);
        $rs['birthday'] = date('Y-m-d',$rs['birthday']);
        $rs['begin_time'] = date('Y-m-d',$rs['begin_time']);
        $rs['end_time'] = date('Y-m-d',$rs['end_time']);
        return $rs;
    }
    //获取列表
    public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
        $rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        return $rs;
    }
    //获取数量
    public function getCount($filter) {
        return $this->model->getCount ( $filter );
    }

    //为公司添加分享的家政员
    public function addShareStaff($data){
        $shareStaffModel = new Model_Jiafubao_CompanyShareHouseStaff();
        $filter = array('company_id' => $data['company_id'],'staff_id' => $data['staff_id']);
        $info = $shareStaffModel->getByWhere($filter);
        if($info){
            return $info['id'];
        }else{
            $rs = $shareStaffModel->insert($data);
        }
        return $rs;
    }
    //撤销推荐家政员
    public function cancelSendStaff($sendId,$companyId,$type){
        //查询推荐记录
        $sendInfo = $this->model->get($sendId);
        if(! $sendInfo){
            return true;
        }
        if($sendInfo['send_company_id'] != $companyId){
            throw new LogicException ( T ( 'Cancel send staff fail' ), 231 );
        }
        //删除推荐记录
        $deleteRs = $this->model->update($sendId,array('status' => 'delete','last_modify' => time()));
        if(! $deleteRs){
            throw new LogicException ( T ( 'Cancel send staff fail' ), 231 );
        }
        if($type == 'all'){
            //删除推荐的家政员
            $shareStaffModel = new Model_Jiafubao_CompanyShareHouseStaff();
            $shareFilter = array('staff_id' => $sendInfo['staff_id'],'company_id' => $sendInfo['company_id'],'send_company_id' => $sendInfo['send_company_id']);
            $deleteRs = $shareStaffModel->deleteByWhere($shareFilter);
            if(! $deleteRs){
                throw new LogicException ( T ( 'Cancel send staff fail' ), 231 );
            }
        }
        return true;
    }

}
