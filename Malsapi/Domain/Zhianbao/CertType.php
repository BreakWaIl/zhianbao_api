<?php
class Domain_Zhianbao_CertType {
	var $model;

	public function __construct() {
		$this->model = new Model_Zhianbao_CertType ();
	}

	//获取详情
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
            $partList = array();
            //获取当前类型所属角色
            $partToCertTypeModel = new Model_Zhianbao_PartToCertType();
            $filter = array('regulator_id' => $rs['regulator_id'], 'type_id' => $id);
            $list = $partToCertTypeModel->getAll('*',$filter);
            foreach ($list as $key=>$value){
                $partList[] = $value['part_id'];
            }
            $rs['part_list'] = $partList;
        }

        return $rs;
    }
    //添加证件类型
    public function addCertType($data){
        $partIds = explode(',',$data['part_id']);
        unset($data['part_id']);
        $rs = $this->model->insert($data);
        if($rs){
            //添加证件类型和角色关系
            $partToCertTypeModel = new Model_Zhianbao_PartToCertType();
            foreach ($partIds as $key=>$value){
                $to_data = array(
                    'regulator_id' => $data['regulator_id'],
                    'part_id' => $value,
                    'type_id' => $rs,
                );
                $res = $partToCertTypeModel->insert($to_data);
                if(!$res){
                    throw new LogicException ( T ( 'Add failed' ), 102 );
                }
            }
        }
        return $rs;
    }
    //更新证件类型
    public function updateCertType($data){
        $id = intval($data['type_id']);
        $regulatorId = $data['regulator_id'];
        $partIds = explode(',',$data['part_id']);
        unset($data['type_id']);
        unset($data['part_id']);
        $rs = $this->model->update($id,$data);
        if($rs){
            //更新证件类型和角色关系
            $partToCertTypeModel = new Model_Zhianbao_PartToCertType();
            $filter = array('regulator_id' =>$regulatorId, 'type_id' => $id);
            $list = $partToCertTypeModel->getAll('*',$filter);
            if(!empty($list)){
                //删除类型下所有的角色
                foreach ($list as $key=>$value){
                    $res = $partToCertTypeModel->delete($value['id']);
                    if(!$res){
                        throw new LogicException ( T ( 'Update failed' ), 104 );
                    }
                }
                foreach ($partIds as $key=>$value){
                    $to_data = array(
                        'regulator_id' => $data['regulator_id'],
                        'part_id' => $value,
                        'type_id' => $id,
                    );
                    $res = $partToCertTypeModel->insert($to_data);
                    if(!$res){
                        throw new LogicException ( T ( 'Add failed' ), 102 );
                    }
                }
            } else{
                foreach ($partIds as $key=>$value){
                    $to_data = array(
                        'regulator_id' => $data['regulator_id'],
                        'part_id' => $value,
                        'type_id' => $id,
                    );
                    $res = $partToCertTypeModel->insert($to_data);
                    if(!$res){
                        throw new LogicException ( T ( 'Add failed' ), 102 );
                    }
                }
            }
        }

        return $rs;
    }
    //删除证件类型
    public function deleteCertType($regulatorId,$typeId){
        $rs = $this->model->delete($typeId);
        if($rs){
            //更新证件类型和角色关系
            $partToCertTypeModel = new Model_Zhianbao_PartToCertType();
            $filter = array('regulator_id' =>$regulatorId, 'type_id' => $typeId);
            $list = $partToCertTypeModel->getAll('*',$filter);
            if(!empty($list)){
                foreach ($list as $key=>$value){
                    $res = $partToCertTypeModel->delete($value['id']);
                    if(!$res){
                        throw new LogicException ( T ( 'Delete failed' ), 105 );
                    }
                }
            }
        }
        return $rs;
    }
    public function isUser($regulatorId,$typeId){
        $rs = array();
        $certModel = new Model_Zhianbao_Cert();
        $regulatorToCustomerModel = new Model_Zhianbao_RegulatorToCustomer();
        $filter = array('regulator_id' => $regulatorId);
        $list = $regulatorToCustomerModel->getAll('*',$filter);
        foreach ($list as $key=>$value){
            $to_filter = array('company_id' => $value['company_id'],'type_id' =>$typeId);
            $rs = $certModel->getAll('*', $to_filter);
            if(!empty($rs)){
                return $rs;
            }
        }
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

	//企业获取证书类型列表
    public function getAllCertType($filter,$staffInfo){
        $list = $this->model->getAll('*',$filter);
        //获取员工角色下的证件类型
        $partToCertTypeModel = new Model_Zhianbao_PartToCertType();
        $to_filter = array(
            'regulator_id' => $filter['regulator_id'],
            'part_id' => $staffInfo['part_id'],
        );
        $part_list = $partToCertTypeModel->getAll('*', $to_filter);
        $type_list = array();
        foreach ($list as $key=>$value){
            foreach ($part_list as $kk=>$vv){

                if($vv['type_id'] == $value['id']){
                    $type_list[] = $value;
                }
            }
        }
        return $type_list;
    }
}
