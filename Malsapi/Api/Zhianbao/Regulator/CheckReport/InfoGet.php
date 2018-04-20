<?php
/**
 * 默认接口服务类
 *
 * @author: Dm
 */
class Api_Zhianbao_Regulator_CheckReport_InfoGet extends PhalApi_Api {
	
	public function getRules() {
		return array (
				 'Go' => array(
                     'reportId' => array('name' => 'report_id','type'=>'int','require'=> true,'desc'=> '检查报告ID'),
            ),
		);
 	}

  
  /**
     * 获取检查报告详情
     * #desc 用于获取检查报告详情
     */
    public function Go() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        //查看隐患项目是否存在
        $reportDomain = new Domain_Zhianbao_CheckReport();
        $reportInfo = $reportDomain->getBaseInfo($this->reportId);
        if(! $reportInfo){
            $rs['code'] = 122;
            $rs['msg'] = T('Report not exists');
            return $rs;
        }

        $rs['info'] = $reportInfo;
        return $rs;
    }

}

