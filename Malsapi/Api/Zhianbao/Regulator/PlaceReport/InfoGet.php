<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_PlaceReport_InfoGet extends PhalApi_Api {
    
    public function getRules() {
        return array (
                 'Go' => array(
                     'regulatorId' => array('name' => 'regulator_id','type'=>'string','require'=> true,'desc'=> '监管者ID'),
                     'reportId' => array('name' => 'report_id', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '检测ID'),
            ),
        );
    }
  
  /**
     * 获取场地检测报告详情
     * #desc 用于获取场地检测报告详情
     * #return int code 操作码，0表示成功
     * #return int id 检测ID
     * #return int company_id 公司ID
     * #return string img_url 图片路径
     * #return int create_time 创建时间
     * #return int last_modify  最后更新时间
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

        //判断检测报告是否存在
        $reportDomain = new Domain_Zhianbao_PlaceReport();
        $reportInfo = $reportDomain->getBaseInfo($this->reportId);
        if( !$reportInfo) {
            DI()->logger->debug('Place report not found', $this->reportId);

            $rs['code'] = 125;
            $rs['msg'] = T('Place report not exists');
            return $rs;
        }

        $rs['info'] = $reportInfo;

        return $rs;
    }
    
}
