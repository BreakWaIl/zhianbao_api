<?php
/**
 * 计划任务调度器
 *
 * - 异常失败，会轮循重试
 * - 彩蛋式的抽象方法名
 *
 * @author dogstar <chanzonghuang@gmail.com> 20150516
 */

abstract class Task_Runner {

    /**
     * @var MQ队列实例
     */
    protected $mq;

    /**
     * @var int $step 批次的数据，步长
     */
    protected $step;

    /**
     * @param Task_MQ $mq MQ队列实例
     * @param int $step 批次的数据，步长
     */
    public function __construct(Task_MQ $mq, $step = 10) {
        $this->mq = $mq;

        $this->step = max(1, intval($step));
    }

    /**
     * 执行任务

     * @return array('total' => 总数量, 'fail' => 失败数量)
     */
    public function go() {
        $rs = array('total' => 0, 'fail' => 0);

        $todoList = $this->mq->pop($this->step);
        // var_dump($todoList);exit;
        $failList = array();

        while (!empty($todoList)) {
            $rs['total'] += count($todoList);

            foreach ($todoList as $task) {
                try {

                    $this->setRuning($task);
                    $result = $this->youGo($task);
                    if (empty($result)) {
                        $rs['fail'] ++;
                        $this->setFail($task,$result);
                    }else{
                        $this->setFinish($task,$result);
                    }
                } catch (PhalApi_Exception_InternalServerError $ex) {
                    $rs['fail'] ++;

                    $failList[] = $task;

                    DI()->logger->error('task occur exception to go',
                        array('task' => $task, 'error' => $ex->getMessage()));
                }
            }

            $todoList = $this->mq->pop($this->step);
        }

        /*foreach ($failList as $params) {
         $this->mq->add($service, $params);
         }*/

        return $rs;
    }

    private function setRuning($task){
        $this->mq->run($task['id']);
    }

    private function setFinish($task,$result){
        $this->mq->finish($task['id'],'succ',$result);
    }

    private function setFail($task,$result){
        $this->mq->finish($task['id'],'fail',$result);
    }

    /**
     * 具体的执行，这里使用了一个彩蛋的命名
     * @param array $params 参数
     * @return boolean 成功返回TRUE，失败返回FALSE
     */
    abstract protected function youGo( $params);
}
