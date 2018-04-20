<?php
class Domain_Building_Project {
    var $model;

    public function __construct() {
        $this->model = new Model_Building_Project();

    }
    //获取项目详情
    public function getBaseInfo($catId, $cols = '*') {
        $rs = array ();
        $id = intval ( $catId );
        if ($id <= 0) {
            return $rs;
        }

        $rs = $this->model->get ( $id);

        if( !$rs){
            return false;
        }else{
            $rs['sign_config'] = json_decode($rs['sign_config'],true);
            $domainArea = new Domain_Area();
            //拼接省市区
            $province = $domainArea->getAreaNameById($rs['province']);
            $city = $domainArea->getAreaNameById($rs['city']);
            $district = $domainArea->getAreaNameById($rs['district']);
            $rs['province_name'] = $province;
            $rs['city_name'] = $city;
            $rs['district_name'] = $district;
            $rs['create_time'] = date("Y-m-d H:i:s", $rs['create_time']);
            $rs['last_modify'] = date("Y-m-d H:i:s", $rs['last_modify']);
        }

        return $rs;
    }

    //添加项目
    public function add($data){
        $rs = $this->model->insert($data);
        if( !$rs){
            throw new LogicException (T('Add failed'), 102);
        }else{
            $logModel = new Model_Building_ProjectChangeLog();
            //插入日志
            $log_data = array(
                'company_id' => $data['company_id'],
                'project_id' => $rs,
                'project_info' => json_encode($data),
                'content' => '新建项目【'.$data['name'].'】',
                'operate_id' => $data['operate_id'],
                'create_time' => time(),
            );
            $log = $logModel->insert($log_data);
            if( !$log){
                throw new LogicException (T('Add failed'), 102);
            }
        }
        return $rs;
    }
    //更新项目
    public function update($data,$companyId){
        $id = intval($data['project_id']);
        unset($data['project_id']);
        $rs = $this->model->update($id,$data);
        if( $rs){
            $logModel = new Model_Building_ProjectChangeLog();
            //插入日志
            $log_data = array(
                'company_id' => $companyId,
                'project_id' => $id,
                'project_info' => json_encode($data),
                'content' => '更新项目【'.$data['name'].'】',
                'operate_id' => $data['operate_id'],
                'create_time' => time(),
            );
            $log = $logModel->insert($log_data);
            if( !$log){
                return false;
            }
        }else{
            return false;
        }
        return $rs;
    }
    //检测打卡时间
    public function checkConfig($config){
        //print_r($config);exit;
        $morning_startTime = $config['day_config']['startWork'];
        $morning_endTime = $config['day_config']['endWork'];
        $after_startTime = $config['after_config']['startWork'];
        $after_endTime = $config['after_config']['endWork'];
        $night_startTime = $config['night_config']['startWork'];
        $night_endTime = $config['night_config']['endWork'];
        if($morning_startTime < $morning_endTime){
            if($after_startTime > $morning_endTime){
                if($after_endTime > $after_startTime){
                    if($night_startTime > $after_endTime){
                        if($night_endTime > $night_startTime){
                            return true;
                        }
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    //结束项目
    public function finish($companyId,$projectId,$projectInfo,$operateId){
        $data = array('status' => 'finish', 'last_modify' => time(), 'operate_id' => $operateId);
        $rs = $this->model->update($projectId,$data);
        if( !$rs){
            return false;
        }else{
            $logModel = new Model_Building_ProjectChangeLog();
            //插入日志
            $log_data = array(
                'company_id' => $companyId,
                'project_id' => $projectId,
                'project_info' => json_encode($projectInfo),
                'content' => '项目【'.$projectInfo['name'].'】已完成',
                'operate_id' => $operateId,
                'create_time' => time(),
            );
            $log = $logModel->insert($log_data);
            if( !$log){
                return false;
            }
        }

        return $rs;
    }
    //获取项目列表
    public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = '',$sort){
        $companyModel = new Model_Zhianbao_Company();
//        $projectToStaffModel = new Model_Building_ProjectToStaff();
        $rs = $this->model->getAll ( 'id,company_id,name,status,create_time,last_modify', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key=>$value){
            $companyInfo = $companyModel->get($value['company_id']);
            $rs[$key]['company_name'] = $companyInfo['name'];
//            $to_filter = array('company_id' => $value['company_id'], 'project_id' => $value['id']);
//            $info = $projectToStaffModel->getByWhere($to_filter,'*');
//            if(!empty($info)){
//                $rs[$key]['isAdd'] = 'n';
//            }else{
//                $rs[$key]['isAdd'] = 'y';
//            }
        }

        if($sort == 'open'){
            $active_array = array();
            $finish_array = array();
            foreach ($rs as $kk=>$vv){
                if($vv['status'] == 'active'){
                    $active_array[] = $vv;
                }
                if($vv['status'] == 'finish'){
                    $finish_array[] = $vv;
                }
            }
            $rs = array_merge($active_array,$finish_array);
        }
        return $rs;
    }
    //获取数量
    public function getCount($filter) {
        return $this->model->getCount ( $filter );
    }

    /*
     * 项目和班组
     * */
    //添加项目和班组的关系
    public function addProjectToCatId($data, $projectInfo){
        $projectToCatIdModel = new Model_Building_ProjectToCat();
        $logModel = new Model_Building_ProjectChangeLog();
        $catModel = new Model_Building_Cat();
        $ids = explode(',',$data['cat_id']);
        unset($data['cat_id']);
        foreach ($ids as $key=>$value){
            //判断类别是否存在
            $filter = array('company_id' => $data['company_id'], 'id' => $value);
            $cat_info = $catModel->getByWhere($filter,'*');
            if(empty($cat_info)){
                throw new LogicException (T('Categroy not exists'), 106);
            }
            //判断是否存在关系
            $to_filter = array('company_id' => $data['company_id'],'project_id' => $data['project_id'], 'cat_id' => $value);
            $to_info = $projectToCatIdModel->getByWhere($to_filter,'*');
            if(empty($to_info)){
                //插入关系
                $data['cat_id'] = $value;
                $rs = $projectToCatIdModel->insert($data);
                if( !$rs){
                    throw new LogicException (T('Add failed'), 102);
                }
            }else{
                //判断班组是否退出项目
                if($to_info['status'] == 'n'){
                    $to_data = array('status' =>'y', 'join_time' =>time(), 'last_modify' =>time(),'operate_id' => $data['operate_id']);
                    $rs = $projectToCatIdModel->update($to_info['id'], $to_data);
                    if( !$rs){
                        throw new LogicException (T('Add failed'), 102);
                    }
                }else{
                    throw new LogicException (T('Add failed'), 102);
                }
            }

            //插入日志
            $log_data = array(
                'company_id' => $data['company_id'],
                'project_id' => $data['project_id'],
                'project_info' => json_encode($projectInfo),
                'content' => '添加班组【'.$cat_info['name'].'】加入项目',
                'operate_id' => $data['operate_id'],
                'create_time' => time(),
            );
            $log = $logModel->insert($log_data);
            if( !$log){
                throw new LogicException (T('Add failed'), 102);
            }
        }

        return $rs;
    }
    //获取项目和班组的关系
    public function hashProjectToCat($filter){
        $projectToCatModel = new Model_Building_ProjectToCat();
        $info = $projectToCatModel->getByWhere($filter,'*');
        if( !empty($info)){
            //判断班组是否退出项目
            if($info['status'] == 'y'){
                return false;
            }else{
                return true;
            }
        }else{
            return true;
        }
    }
    //班组退出项目
    public function exitCat($filter,$projectInfo,$catInfo,$operateId){
        $projectToCatModel = new Model_Building_ProjectToCat();
        $logModel = new Model_Building_ProjectChangeLog();
        $staffToCatModel = new Model_Building_StaffToCat();
        $projectToStaffModel = new Model_Building_ProjectToStaff();
        $staffModel = new Model_Building_Staff();
        $info = $projectToCatModel->getByWhere($filter,'*');
        if(!empty($info)){
            //判断班组状态
            if($info['status'] == 'n'){
                return false;
            }
            //更新班组状态
            $data = array('status' => 'n', 'exit_time' => time(), 'last_modify' =>time(),'operate_id' =>$operateId );
            $rs = $projectToCatModel->update($info['id'],$data);
            if( !$rs){
                return false;
            }
            //插入日志
            $log_data = array(
                'company_id' => $filter['company_id'],
                'project_id' => $filter['project_id'],
                'project_info' => json_encode($projectInfo),
                'content' => '班组【'.$catInfo['name'].'】退出项目',
                'operate_id' => $operateId,
                'create_time' => time(),
            );
            $log = $logModel->insert($log_data);
            if( !$log){
                throw new LogicException (T('Add failed'), 102);
            }

            //获取该项目班组下的员工
            $list = $staffToCatModel->getAll('*', $filter);
            foreach ($list as $key=>$value) {
                $staff_to_filter = array(
                    'company_id' => $filter['company_id'],
                    'project_id' => $filter['project_id'],
                    'staff_id' => $value['staff_id'],
                );
                //判断员工和项目的关系
                $info = $projectToStaffModel->getByWhere($staff_to_filter,'*');
                if(!empty($info)){
                    //获取员工名称
                    $staff_filter = array('company_id' => $filter['company_id'], 'id' => $value['staff_id']);
                    $staffInfo = $staffModel->getByWhere($staff_filter,'id,name');
                    //将班组下的员工退出项目
                    $data = array('status' => 'n', 'exit_time' => time(), 'last_modify' =>time(),'operate_id' =>$operateId );
                    $res = $projectToStaffModel->update($info['id'],$data);
                    if( !$res){
                        return false;
                    }else{
                        //插入日志
                        $log_data = array(
                            'company_id' => $filter['company_id'],
                            'project_id' => $filter['project_id'],
                            'project_info' => json_encode($projectInfo),
                            'content' => '员工【'.$staffInfo['name'].'】跟随班组【'.$catInfo['name'].'】退场',
                            'operate_id' => $operateId,
                            'create_time' => time(),
                        );
                        $log = $logModel->insert($log_data);
                        if( !$log){
                            return false;
                        }
                    }
                }
            }

        }

    }
    //获取项目所属的班组列表
    public function getAllCat($filter){
        $rs = array();
        $projectToCatIdModel = new Model_Building_ProjectToCat();
        $catModel = new Model_Building_Cat();
        //获取项目下的班组
        $cat_list = $projectToCatIdModel->getAll('*', $filter);
        foreach ($cat_list as $key=>$value){
            $cat_filter = array('id' => $value['cat_id'], 'company_id' => $filter['company_id']);
            $info = $catModel->getByWhere($cat_filter,'id,name');
            $rs[$key]['id'] = $info['id'];
            $rs[$key]['name'] = $info['name'];
            $rs[$key]['name'] = $info['name'];
            $rs[$key]['status'] = $value['status'];
            $rs[$key]['join_time'] = date("Y-m-d H:i:s", $value['join_time']);
            $rs[$key]['create_time'] = $value['create_time'];
            $rs[$key]['last_modify'] = $value['last_modify'];
        }
        return $rs;
    }
    public function getAllCatCount($filter){
        $projectToCatIdModel = new Model_Building_ProjectToCat();
        return $projectToCatIdModel->getCount($filter);
    }
    //检测班组和项目的关系
    public function checkProjectToCat($filter){
        $projectToCatIdModel = new Model_Building_ProjectToCat();
        $catIds = $filter['cat_id'];
        unset($filter['cat_id']);
        foreach ($catIds as $key=>$value){
            $filter['cat_id'] = $value;
            $info = $projectToCatIdModel->getByWhere($filter,'id,company_id,project_id,cat_id,status');
            if(empty($info)){
                return false;
            }
        }
        return true;
    }


    //员工退出项目
    public function exitProject($filter,$projectInfo,$staffInfo,$operateId){
        $projectToStaffModel = new Model_Building_ProjectToStaff();
        $logModel = new Model_Building_ProjectChangeLog();
        $staffIds = explode(',',$filter['staff_id']);
        unset($filter['staff_id']);
        foreach ($staffIds as $key => $value){
            $filter['staff_id'] = $value;
            $info = $projectToStaffModel->getByWhere($filter,'*');
            if(!empty($info)){
                //更新状态
                $data = array('status' => 'n', 'exit_time' => time(), 'last_modify' =>time(),'operate_id' =>$operateId );
                $res = $projectToStaffModel->update($info['id'],$data);
                if( !$res){
                    return false;
                }else{
                    //插入日志
                    $log_data = array(
                        'company_id' => $filter['company_id'],
                        'project_id' => $filter['project_id'],
                        'project_info' => json_encode($projectInfo),
                        'content' => '员工【'.$staffInfo['name'].'】退场',
                        'operate_id' => $operateId,
                        'create_time' => time(),
                    );
                    $log = $logModel->insert($log_data);
                    if( !$log){
                        return false;
                    }
                }
            }
        }
        return true;
    }
    //获取项目所属人员列表
    public function getAllStaff($filter, $page = 1, $page_size = 20, $orderby = ''){
        $companyModel = new Model_Zhianbao_Company();
        $staffDomain = new Domain_Building_Staff();
        $projectToStaffModel = new Model_Building_ProjectToStaff();
        $rs = $projectToStaffModel->getAll ( '*', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key=>$value){
            //获取公司信息
            $companyInfo = $companyModel->get($value['company_id']);
            $rs[$key]['company_name'] = $companyInfo['name'];
            //获取项目信息
            $projectInfo = $this->model->get($value['project_id']);
            $rs[$key]['project_name'] = $projectInfo['name'];
            //获取员工信息
            $staffInfo = $staffDomain->getBaseInfo($value['staff_id']);
            $rs[$key]['staff_name'] = $staffInfo['name'];
            //获取员工二维码
//            $qrCode = $this->createQrCode($value['project_id'],$value['staff_id']);
            $staffUrl = rawurlencode('http://zgbh5.mshenpu.com/codePage?projectId='.$value['project_id'].'&staffId='.$value['staff_id']);
            $qrCode = 'http://pan.baidu.com/share/qrcode?w=150&h=150&url='.$staffUrl;
            $rs[$key]['qrcode'] = $qrCode;
            $rs[$key]['join_time'] = $value['join_time'] == 0 ? '-': date("Y-m-d H:i:s",$value['join_time']);
            $rs[$key]['exit_time'] = $value['exit_time'] == 0 ? '-': date("Y-m-d H:i:s",$value['exit_time']);
        }
        return $rs;
    }
    //获取项目所属人员数量
    public function getCountStaff($filter) {
        $projectToStaffModel = new Model_Building_ProjectToStaff();
        $rs = $projectToStaffModel->getCount ( $filter );
        return $rs;
    }
    //获取员工二维码
    public function createQrCode($projectInfo, $staffInfo){
//        $staffUrl = rawurlencode('http://192.168.100.160:8094/codePage?projectId='.$projectInfo['id'].'&staffId='.$staffInfo['id']);
        $staffUrl = rawurlencode('http://zgbh5.mshenpu.com/codePage?projectId='.$projectInfo['id'].'&staffId='.$staffInfo['id']);
        $rs = $qrCode = 'http://pan.baidu.com/share/qrcode?w=150&h=150&url='.$staffUrl;
        //保存的图片名称
        $file_name = time().'.png';
        $file_name = iconv('UTF-8','GB2312//IGNORE',$file_name);

        $path = $this->down($file_name,$rs);
        if( !$path){
            return false;
        }
        $path['p_name'] = $projectInfo['name'];
        $path['s_name'] = $staffInfo['name'];

        return $path;
    }

    function down($file_name,$url){
        //按照年月日创建目录
        $save_dir = '../file/'.date("Y").'/'.date("m").'/'.date("d").'/';
        //判断给定文件名是否是一个目录
        if(!file_exists($save_dir)){
            if (!mkdir($save_dir,0777,true)) {
                return false;
            }
        }
        $ch = curl_init();
        $timeout = 30;
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        $img = curl_exec($ch);
        curl_close($ch);

        //文件大小
        $fp = fopen($save_dir.$file_name,'a');
        fwrite($fp,$img);
        fclose($fp);
        unset($img,$url);

        $img_url = $save_dir.$file_name;
        $imgModel = new Model_Zhianbao_Image ();
        $filter = array('company_id' => 0, 'img_cat_id' => 0, 'img_url' =>$img_url, 'local_img_url' =>$img_url, 'is_del' => 'n');
        $info = $imgModel->getByWhere($filter, '*');
        if( empty($info)){
            $base64_img = array();
            $image_info = getimagesize($img_url);
            $image_data = fread(fopen($img_url, 'r'), filesize($img_url));
            $base64_img[] = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
            $data = array(
                'company_id' => 0,
                'img_content' => $base64_img,
            );
            foreach($data['img_content'] as $key => $value ) {
                $data['img_url'] = $img_url;
                $data['local_img_url'] = $img_url;
                unset($data['img_content']);
                $res = $imgModel->insert($data);
                if(! $res){
                    return false;
                }
            }
            $imgInfo = $imgModel->get($res);
            $rs['path'] = DI ()->config->get ( 'app.api_root' ).str_replace('../','/',$imgInfo['img_url']);
            $rs['img_id'] = $res;
        }else{
            $rs['path'] = DI ()->config->get ( 'app.api_root' ).str_replace('../','/',$info['img_url']);
            $rs['img_id'] = $info['id'];
        }

        return $rs;
    }

    public function download($newName, $imgId){
        //定义文件下载
        $imgModel = new Model_Zhianbao_Image ();
        $imgInfo = $imgModel->get($imgId);
        $file_name = $newName.'.png';
        header('Pragma: public');
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.$file_name);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header( 'Content-Length: '.filesize($imgInfo['img_url']));
        ob_clean();
        flush();
        readfile($imgInfo['img_url']);
        exit;
    }

    /*
     * 项目日志
     * */
    //获取项目日志列表
    public function getAllLog($filter, $page = 1, $page_size = 20, $orderby = ''){
        $logModel = new Model_Building_ProjectChangeLog();
        $rs = $logModel->getAll ( '*', $filter, $page, $page_size, $orderby );
        foreach ($rs as $key=>$value){
            $rs[$key]['project_info'] = json_decode($value['project_info'],true);
        }
        return $rs;
    }
    //获取数量
    public function getCountLog($filter) {
        $logModel = new Model_Building_ProjectChangeLog();
        return $logModel->getCount ( $filter );
    }

    /*
     * 项目和管理员
    */
    //添加项目下的管理员
    public function addProjectSub($data){
        $projectToSubModel = new Model_Building_ProjectToSub();
        $filter = array('company_id'=> $data['company_id'], 'project_id'=> $data['project_id'],'sub_id'=> $data['sub_id']);
        $info = $projectToSubModel->getByWhere($filter,'*');
        if( !empty($info)){
            throw new LogicException ( T ( 'Add failed' ), 102 );
        }else{
            $rs = $projectToSubModel->insert($data);
        }
        return $rs;
    }
    //删除项目下的管理员
    public function deleteSub($filter){
        $projectToSubModel = new Model_Building_ProjectToSub();
        $info = $projectToSubModel->getByWhere($filter,'*');
        if(!empty($info)){
            $rs = $projectToSubModel->delete($info['id']);
            if( !$rs){
                return false;
            }
        }else{
            return false;
        }
    }
    //获取项目管理员列表
    public function getAllSub($filter, $page = 1, $page_size = 20, $orderby = ''){
        $rs = array();
        $projectToSubModel = new Model_Building_ProjectToSub();
        $userModel = new Model_Zhianbao_User();
        $userGroupModel = new Model_Building_UserGroup();
        $list = $projectToSubModel->getAll ( '*', $filter, $page, $page_size, $orderby );
//        print_r($list);exit;
        foreach ($list as $key=>$value){
            $info = $userModel->get($value['sub_id']);
            $groupInfo = $userGroupModel->get($info['group_id']);
            $rs[$key]['id'] = $info['id'];
            $rs[$key]['login_name'] = $info['login_name'];
            $rs[$key]['name'] = $info['name'];
            $rs[$key]['group_id'] = $info['group_id'];
            $rs[$key]['group_name'] = $groupInfo['name'];
            $rs[$key]['create_time'] = date("Y-m-d H:i:s",$info['create_time']);
            $rs[$key]['project_id'] = $value['project_id'];
            $rs[$key]['operate_id'] = $value['operate_id'];
        }
        return $rs;
    }
    //获取数量
    public function getCountSub($filter) {
        $projectToSubModel = new Model_Building_ProjectToSub();
        return $projectToSubModel->getCount ( $filter );
    }


}
