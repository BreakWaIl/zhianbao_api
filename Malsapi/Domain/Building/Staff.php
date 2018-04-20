<?php
class Domain_Building_Staff {
	var $model;

	public function __construct() {
		$this->model = new Model_Building_Staff ();
	}

    //获取详情
    public function getBaseInfo($staffId, $cols = '*') {
        $rs = array ();

        $id = intval ( $staffId );
        if ($id <= 0) {
            return $rs;
        }

        // 版本1：简单的获取
        $rs = $this->model->get($id);
        if (! $rs){
            return false;
        }else{
            //头像
            if($rs['avatar'] == 'null'){
                $rs['avatar'] = '';
            }else{
                $rs['avatar'] = json_decode($rs['avatar'], true);
            }
            $rs['industry'] = json_decode($rs['industry'], true);
            $rs['cat_id'] = json_decode($rs['cat_id'], true);
            $birthday = $this->unixtime_to_date($rs['birthday']);
            $rs['birthday'] = $birthday;
            $domainArea = new Domain_Area();
            //籍贯
            $rs['native_place_name'] = $domainArea->getAreaNameById($rs['native_place']);
            //拼接户籍省市区
            $rs['native_place_district'] = json_decode($rs['native_place_district'], true);
            $province = $domainArea->getAreaNameById($rs['native_place_district']['province']);
            $city = $domainArea->getAreaNameById($rs['native_place_district']['city']);
            $district = $domainArea->getAreaNameById($rs['native_place_district']['district']);
            $rs['native_place_district_name'] = $province.$city.$district;
            //拼接现居住省市区
            $rs['now_district'] = json_decode($rs['now_district'], true);
            if($rs['now_district']['province'] != 0 && $rs['now_district']['city'] != 0 && $rs['now_district']['district'] != 0){
                $now_province = $domainArea->getAreaNameById($rs['now_district']['province']);
                $now_city = $domainArea->getAreaNameById($rs['now_district']['city']);
                $now_district = $domainArea->getAreaNameById($rs['now_district']['district']);
                $rs['now_district_name'] = $now_province.$now_city.$now_district;
            } else{
                $rs['now_district']['province'] = '';
                $rs['now_district']['city'] = '';
                $rs['now_district']['district'] = '';
                $rs['now_district_name'] = '';
            }
            $rs['create_time'] = date("Y-m-d H:i:s", $rs['create_time']);
            $rs['last_modify'] = date("Y-m-d H:i:s", $rs['last_modify']);

            //获取公司信息
            $companyModel = new Model_Zhianbao_Company();
            $companyInfo = $companyModel->get($rs['company_id']);
            $rs['company_name'] = $companyInfo['name'];
            //获取班组信息
            $staffToCatModel = new Model_Building_StaffToCat();
            $filter = array('company_id'=>$rs['company_id'], 'staff_id'=>$rs['id']);
            $cat_list = $staffToCatModel->getAll('',$filter);
            $catIds = array();
            foreach ($cat_list as $key=>$value){
                $catIds[] = $value['cat_id'];
            }
            $catModel = new Model_Building_Cat();
            foreach ($catIds as $key=>$value){
                $filter = array('id' => $value);
                $info = $catModel->getByWhere($filter,'*');
                if( $info){
                    $rs['cat_name'][$key]['id'] = $value;
                    $rs['cat_name'][$key]['name'] = $info['name'];
                }
            }

        }

        return $rs;
    }
    //添加员工
    public function addStaff($data,$projectInfo){
        $projectToStaffModel = new Model_Building_ProjectToStaff();
        $project_id = $data['project_id'];unset($data['project_id']);
        //检测身份证号码是否存在
        $filter = array('cardID' => $data['cardID'],'company_id' => $data['company_id']);
        $info = $this->checkCardID($filter);
        $catIds = $data['cat_id'];unset($data['cat_id']);
        //添加员工
        if(empty($info)){
            $birthday = $this->date_to_unixtime($data['birthday']);
            $data['birthday'] = $birthday;
            $res = $this->model->insert($data);
            if( !$res){
                throw new LogicException (T('Add failed'), 102);
            }
            $staff_id = $res;
        }else{
            $staff_id = $info['id'];
        }

        //检测该员工是否已在项目中
        $filter = array('project_id' => $project_id, 'staff_id'=> $info['id']);
        $to_staff = $projectToStaffModel->getByWhere($filter,'*');
        if(empty($to_staff)){
            //添加员工项目关系
            $to_data = array(
                'company_id' => $data['company_id'],
                'project_id' => $project_id,
                'staff_id' => $staff_id,
                'join_time' => time(),
                'create_time' => time(),
                'last_modify' => time(),
                'operate_id' => $data['operate_id'],
            );
            $add = $projectToStaffModel->insert($to_data);
            if( !$add){
                throw new LogicException (T('Add failed'), 102);
            }
        }else{
            //判断工人状态
            if($to_staff['status'] == 'y'){
                throw new LogicException (T('Staff exist in the project'), 202);
            }
            if($to_staff['status'] == 'n'){
                //更新员工项目关系
                $update_data = array(
                    'status' => 'y',
                    'join_time' => time(),
                    'last_modify' => time(),
                    'operate_id' => $data['operate_id'],
                );
                $update = $projectToStaffModel->update($to_staff['id'],$update_data);
                if( !$update){
                    throw new LogicException (T('Add failed'), 102);
                }
            }
        }
        //插入项目日志
        $logModel = new Model_Building_ProjectChangeLog();
        $log_data = array(
            'company_id' => $data['company_id'],
            'project_id' => $project_id,
            'project_info' => json_encode($projectInfo),
            'content' => '分配工人【'.$data['name'].'】进场',
            'operate_id' => $data['operate_id'],
            'create_time' => time(),
        );
        $rs = $logModel->insert($log_data);
        if( !$rs){
            throw new LogicException (T('Add failed'), 102);
        }
        //添加员工和项目班组的关系
        $staffToCatModel = new Model_Building_StaffToCat();
        foreach ($catIds as $key=>$value){
            //获取员工和项目的关系
            $to_filter = array('company_id' => $data['company_id'],'project_id' => $project_id,'staff_id' => $staff_id,'cat_id' => $value,);
            $to_info = $staffToCatModel->getByWhere($to_filter,'*');
            if(empty($to_info)){
                $to_data = array(
                    'company_id' => $data['company_id'],
                    'staff_id' => $staff_id,
                    'project_id' => $project_id,
                    'cat_id' => $value,
                    'create_time' => time(),
                    'last_modify' => time(),
                    'operate_id' => $data['operate_id'],
                );
                $to = $staffToCatModel->insert($to_data);
                if( !$to){
                    throw new LogicException (T('Add failed'), 102);
                }
            }
        }

        return $rs;
    }
    //时间戳转日期
    function unixtime_to_date($unixtime, $timezone = 'PRC') {
        $datetime = new DateTime("@$unixtime");
        $datetime->setTimezone(new DateTimeZone($timezone));
        return $datetime->format("Y-m-d");
    }
    //日期转时间戳
    function date_to_unixtime($date, $timezone = 'PRC') {
        $datetime= new DateTime($date, new DateTimeZone($timezone));
        return $datetime->format('U');
    }
    //检测身份证号码
    public function checkCardID($filter){
        $rs = $this->model->getByWhere($filter,'*');
        return $rs;
    }
    //检测类别
    public function checkCatId( $catIds ){
        $rs = true;
        $catModel = new Model_Building_Cat();
        foreach ($catIds as $key=>$value){
            $filter = array('id' => $value);
            $info = $catModel->getByWhere($filter,'*');
            if( !$info){
                return false;
            }
        }
        return $rs;
    }
    //更新员工
    public function updateStaff($data){
        $id = intval($data['staff_id']);
        unset($data['staff_id']);
        $birthday = $this->date_to_unixtime($data['birthday']);
        $data['birthday'] = $birthday;
        $rs = $this->model->update($id,$data);
        return $rs;
    }
    //删除员工
    public function delStaff($id){
        $rs = $this->model->delete($id);
        return $rs;
    }
    //获取列表
    public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = '') {
        $companyModel = new Model_Zhianbao_Company();
        $rs = $this->model->getAll ( 'id,cat_id,company_id,name,sex,birthday,mobile,cardID,create_time,last_modify', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key=>$value){
            $rs[$key]['birthday'] = date("Y-m-d",$value['birthday']);
            $companyInfo = $companyModel->get($value['company_id']);
            $rs[$key]['company_name'] = $companyInfo['name'];
        }
        return $rs;
    }
    //获取数量
    public function getCount($filter) {
        return $this->model->getCount ( $filter );
    }
    //获取班组下的员工
    public function checkCatIds($filter){
        $ids = array();
        $staffToCatModel = new Model_Building_StaffToCat();
        $rs = $staffToCatModel->getAll ( '*', $filter);
        foreach ($rs as $key=>$value){
            $ids[] = $value['staff_id'];
        }
       return $ids;
    }
    //获取项目下的员工
    public function checkProjectIds($filter){
        $list = array();
        $projectToStaffModel = new Model_Building_ProjectToStaff();
        $rs = $projectToStaffModel->getAll ( '*', $filter);
        foreach ($rs as $key=>$value){
            $list[] = $value['staff_id'];
        }
        $ids = array_unique($list);
        return $ids;
    }

}
