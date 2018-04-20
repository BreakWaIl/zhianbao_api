<?php
class Domain_Zhianbao_ReplyRule {
	var $model;

	public function __construct() {
		$this->model = new Model_Zhianbao_ReplyRule ();
	}
	public function getBaseInfo($replyRuleId){
		return $this->model->get($replyRuleId);
	}

	public function getAllByPage($filter, $page = 1, $page_size = 20, $orderby = '') {
		$rs = $this->model->getAll ( '*', $filter, $page, $page_size, $orderby );
		foreach ($rs as $key => $value){
			$rs[$key]['key_words'] = json_decode($value['key_words'],true);
			$rs[$key]['contents'] = json_decode($value['contents'],true);
		}
		return $rs;
	}

	//获取数量
	public function getCount($filter) {
		return $this->model->getCount ( $filter );
	}
	public function addReplyRule($data){
		$keyWords = json_decode($data['key_words'],true);
		$isUsed = $this->isUsedKeyWord($data['wechat_id'],$keyWords);
		if($isUsed){
			throw new LogicException ( T ( 'Keyword is already exists' ), 171 );
		}

		$replyRuleId = $this->model->insert($data);

		if( ! $replyRuleId){
			throw new LogicException ( T ( 'Create failed' ), 144 );
		}
		return $replyRuleId;
	}
	public function deleteReplyRule($ruleId){
		$this->model->delete($ruleId);
	}
	public function updateReplyRule($data){
		$id = $data['id'];
		$rs = $this->model->update($id,$data);
		return $rs;
	}
	public function responseKeyWord($wechatId,$keyWord){
		$filter = array('wechat_id' => $wechatId);
		$rules = $this->model->getAll('*',$filter);
		$return_content = "";
		if(is_array($rules)){
			foreach ($rules as $key => $value){
				$keyWords = json_decode($value['key_words'],true);
				$contents = json_decode($value['contents'],true);
				if(in_array($keyWord,$keyWords)){
					foreach ($contents as $content){
							$return_content = $content;
					}
				}
			}
		}
		return $return_content;
	}
	//判断关键词是否存在
	public function isUsedKeyWord($wechatId,$keyWords){
		$rules = $this->model->getAll('*',array('wechat_id'=>$wechatId));
		$rulesKeyWords = array();
		if($rules){
			foreach ($rules as $key => $value){
				$key = json_decode($value['key_words'],true);
				$rulesKeyWords = array_merge($rulesKeyWords,$key);
			}
		}
		foreach ($keyWords as $k => $v) {
			if (in_array($v, $rulesKeyWords)) {
				return true;
			}
		}
		return false;
	}
	public function deleteReplyRuleContent($ruleInfo,$contentId){
		$contents = json_decode($ruleInfo['contents'],true);
		array_splice($contents,$contentId,1);
		$data = array(
			'contents' => json_encode($contents),
		);
		$this->model->update($ruleInfo['id'],$data);
	}
	public function updateReplyRuleContent($ruleInfo,$contentId,$content){
		$contents = json_decode($ruleInfo['contents'],true);
		$contents[$contentId] = $content;
		$data = array(
			'contents' => json_encode($contents),
		);
		$this->model->update($ruleInfo['id'],$data);
	}
	public function addReplyRuleContent($ruleInfo,$content){
		$contents = json_decode($ruleInfo['contents'],true);
		$contents[] = $content;
		$data = array(
			'contents' => json_encode($contents),
		);
		$this->model->update($ruleInfo['id'],$data);
	}

}
