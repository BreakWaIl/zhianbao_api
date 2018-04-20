<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_UrgentReport_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
                     'reportId' => array('name' => 'report_id', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '演练ID'),
            ),
        );
    }

    /**
     * 获取应急演练详情
     * #desc 用于获取应急演练详情
     * #return int code 操作码，0表示成功
     * #return int id 演练ID
     * #return int company_id 公司ID
     * #return string name 演练名称
     * #return string content 演练内容
     * #return string number 人员数量
     * #return string result 演练结果
     * #return int create_time 创建时间
     * #return int last_modify 最后更新时间
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

        //判断应急演练是否存在
        $reportDomain = new Domain_Zhianbao_UrgentReport();
        $reportInfo = $reportDomain->getBaseInfo($this->reportId);
        if( !$reportInfo) {
            DI()->logger->debug('Urgent report not exist', $this->reportId);

            $rs['code'] = 141;
            $rs['msg'] = T('Urgent report not exist');
            return $rs;
        }

        $rs['info'] = $reportInfo;

        return $rs;
    }
    
}
