<?php
class Domain_Zhianbao_Cert {
	var $model;

	public function __construct() {
		$this->model = new Model_Zhianbao_Cert ();
	}

	//获取通知详情
    public function getBaseInfo($safeId, $cols = '*'){
        $rs = array ();
        $id = intval ( $safeId );
        if ($id <= 0) {
            return $rs;
        }

        $rs = $this->model->get ( $id);

        if (! $rs){
            return false;
        }else{
            $rs['img_url'] = json_decode($rs['img_url'],true);
            $companyModel = new Model_Zhianbao_Company();
            $companyInfo = $companyModel->get($rs['company_id']);
            $rs['company_name'] = $companyInfo['name'];
            $certTypeModel = new Model_Zhianbao_CertType();
            $to_filter = array('id' => $rs['type_id']);
            $typeInfo = $certTypeModel->getByWhere($to_filter,'name');
            $rs['type_name'] = $typeInfo['name'];
        }

        return $rs;
    }
    //添加证件
    public function addCert($data){
        $certInfo = $data['certInfo'];
        unset($data['certInfo']);
        foreach ($certInfo as $key=>$value){
            $add = array(
                'company_id'=> $data['company_id'],
                'staff_id'=> $data['staff_id'],
                'name'=> $data['name'],
                'type_id' => $value['type_id'],
                'img_url' => json_encode($value['img_url']),
                'create_time' => time(),
                'last_modify' => time(),
            );
            $rs = $this->model->insert($add);
        }
        if($rs){
            //更新员工证件更新时间
            $staffModel = new Model_Zhianbao_Staff();
            $update_data = array('cert_last_modify' => time());
            $res = $staffModel->update($data['staff_id'], $update_data);
            if(!$res){
                throw new LogicException ( T ( 'Add failed' ) , 102 );
            }
        }
        return $rs;
    }
    //更新证件
    public function updateCert($data,$staffId){
        $id = intval($data['safe_id']);
        unset($data['safe_id']);
        $rs = $this->model->update($id,$data);
        if($rs){
            //更新员工证件更新时间
            $staffModel = new Model_Zhianbao_Staff();
            $update_data = array('cert_last_modify' => time());
            $res = $staffModel->update($staffId, $update_data);
            if(!$res){
                throw new LogicException ( T ( 'Update failed' ) , 104 );
            }
        }
        return $rs;
    }
    //删除证件
    public function deleteCert($safeId){
        $rs = $this->model->delete($safeId);
        return $rs;
    }
    //获取列表
	public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = ''){
		$rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
        $certTypeModel = new Model_Zhianbao_CertType();
        $companyModel = new Model_Zhianbao_Company();
        $staffModel = new Model_Zhianbao_Staff();
        $partToCertTypeModel = new Model_Zhianbao_PartToCertType();
        foreach ($rs as $key=>$value){
            $staffInfo = $staffModel->get($value['staff_id']);
            $to_del_filter = array('type_id' => $value['type_id'],'part_id' => $staffInfo['part_id']);
            $info = $partToCertTypeModel->getByWhere($to_del_filter,'*');
            if(empty($info)){
                $this->model->delete($value['id']);
            }

            $to_filter = array('id' => $value['type_id']);
            $typeInfo = $certTypeModel->getByWhere($to_filter,'name');
            $rs[$key]['type_name'] = $typeInfo['name'];
            $companyInfo = $companyModel->get($value['company_id']);
            $rs[$key]['company_name'] = $companyInfo['name'];
        }
		return $rs;
	}
	//获取数量
	public function getCount($filter) {
		return $this->model->getCount ( $filter );
	}

	//删除员工证件信息
    public function deleteStaffCert($staffInfo){
        $filter = array('company_id' => $staffInfo['company_id'], 'staff_id' => $staffInfo['id']);
        $list = $this->model->getAll('*', $filter);
        if(!empty($list)){
            foreach ($list as $key=>$value){
                $res = $this->model->delete($value['id']);
                if( !$res){
                    throw new LogicException ( T ( 'Update failed' ) , 104 );
                }
            }
        }

    }
}
