<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Jiafubao_Company_HealthCard_Update extends PhalApi_Api {

    public function getRules() {
        return array (
            'Go' => array(
                'companyId' => array('name' => 'company_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '公司ID'),
                'healthId' => array('name' => 'health_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '健康卡ID'),
//                'condition' => array('name' => 'condition', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '健康情况'),
                'sendTime' => array('name' => 'send_time', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '发放日期'),
                'endTime' => array('name' => 'end_time', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '截至有效期'),
                'imgUrl' => array('name' => 'img_url', 'type' => 'array', 'format'=>'json', 'require' => true, 'desc' => '图片路径'),
            ),
        );
    }


    /**
     * 更新健康卡
     * #desc 用于更新健康卡
     * #return int code 操作码，0表示成功
     * #return int status 0 成功 1 失败
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //判断公司是否存在
        $domainCompany = new Domain_Zhianbao_Company();
        $companyInfo = $domainCompany->getBaseInfo($this->companyId);
        if (empty($companyInfo)) {
            DI()->logger->debug('Company not exists', $this->companyId);

            $rs['code'] = 100;
            $rs['msg'] = T('Company not exists');
            return $rs;
        }
        //判断健康卡是否存在
        $healthCardDomain = new Domain_Jiafubao_StaffHealthCard();
        $healthCardInfo = $healthCardDomain->getBaseInfo($this->healthId);
        if( !$healthCardInfo) {
            $rs['code'] = 152;
            $rs['msg'] = T('Health card not exist');
            return $rs;
        }
        //判断家康卡是否作废
        if($healthCardInfo['status'] == 'n'){
            $rs['code'] = 167;
            $rs['msg'] = T('Health card have been repeal');

            return $rs;
        }
        $data = array(
            'health_id' => $this->healthId,
//            'health_level' => $this->condition,
            'send_time' => strtotime($this->sendTime),
            'end_time' => strtotime($this->endTime),
            'img_url' => json_encode($this->imgUrl),
            'is_check' => 'n',
            'last_modify' => time(),
        );
        try {
            DI ()->notorm->beginTransaction ( 'db_api' );
            $res = $healthCardDomain->updateHealthCard($data,$healthCardInfo);
            DI ()->notorm->commit( 'db_api' );

        } catch ( Exception $e ) {

            DI ()->notorm->rollback ( 'db_api' );
            $rs ['code'] = $e->getCode ();
            $rs ['msg'] = $e->getMessage ();
        }
        if($res){
            $status = 0;
        }else{
            $status = 1;
        }

        $rs['info']['status'] = $status;

        return $rs;
    }

}
