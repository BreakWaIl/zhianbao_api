<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_Complaint_Add extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'regulatorId' => array('name' => 'regulator_id','type'=>'int','require'=> true,'desc'=> '监管者ID'),
                'companyId' => array('name' => 'company_id','type'=>'int','require'=> true,'desc'=> '公司ID'),
                'title' => array('name' => 'title', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '标题'),
                'content' => array('name' => 'content', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '内容'),
                'type' => array('name' => 'type', 'type' => 'enum', 'range' => array('suggest','complaint'), 'require' => true, 'desc' => '类型'),
            ),
        );
    }


    /**
     * 添加投诉建议
     * #desc 用于添加投诉建议
     * #return int code 操作码，0表示成功
     * #return int notice_id  通知ID
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //检测监管者是否存在
        $regulatorDomain = new Domain_Zhianbao_Regulator();
        $regulatorInfo = $regulatorDomain->getBaseInfo($this->regulatorId);
        if(! $regulatorInfo){
            $rs['code'] = 118;
            $rs['msg'] = T('Regulator not exists');
            return $rs;
        }
        //检测公司是否存在
        $companyDomain = new Domain_Zhianbao_Company();
        $companyInfo = $companyDomain->getBaseInfo($this->companyId);
        if(! $companyInfo){
            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }
        $data = array(
            'regulator_id' => $this->regulatorId,
            'company_id' => $this->companyId,
            'title' => $this->title,
            'content' => $this->content,
            'type' => $this->type,
            'create_time' => time()
        );
        $domain = new Domain_Zhianbao_Complaint();



        try {
            $id = $domain->addComplaint($data);
        } catch ( Exception $e ) {

            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
            return $rs;
        }





        $rs['info']['complaint_id'] = $id;

        return $rs;
    }

}
