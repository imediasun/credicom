<?php
namespace App\modules\Cron\Model;

use \App\modules\Core\Model\Traits\Singleton;

use \App\modules\Core\Model\Registry;
use \App\modules\Core\Model\Base as BaseModel;
use App\Http\ArraysClass;
use \App\modules\Cron\Model\Task;
use \App\modules\Cron\Model\Job;
use \App\modules\Cron\Collection\Job as CollectionJob;
use Log;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
//use \App\Cron as CollectionJob;
class Service extends BaseModel{
    use Singleton;

    public $enabled = true;


    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        $config = Registry::getInstance()->getConfig();
        $config = new ArraysClass();
        $config=$config->conf;

        $this->setConfig($config['module']['cron']);

    }

    public function process()
    {
		
		
        //get tasks list from config
        $taskList = $this->getTaskList();
        //process each task
		Log::info('Service process : '.date("Y-m-d H:i:s").' task list=>'.print_r($taskList,true));
        foreach($taskList as $key=>$task) {
		$orderLog = new Logger('files');
		$orderLog->pushHandler(new StreamHandler(storage_path('logs/'.$task->getFunction().'.log')), Logger::INFO);
		$orderLog->info('Work of Credicom crone'.date("Y-m-d H:i:s").
			' processible=>'.print_r($task->isProcessable(),true)); 

            if(!$task->isProcessable()) continue;
            $this->processTask($task);
        }
    }

    public function getTaskList()
    {
        $config = $this->getConfig();

        //load jobs from db
        $jobList = CollectionJob::getInstance()->getListByName();

        //load tasks form config, attach existing jobs
        $result = [];
        foreach($config as $name => $taskConfig) {
            $enabled = (!isset($taskConfig['enabled']) || $taskConfig['enabled']);
            if(!$enabled) continue;

            $job = isset($jobList[$name]) ? $jobList[$name] : null;
            if(!$job) {
                $job = new Job([
                    'name' => $name,
                    'description' => $taskConfig['description'],
                    'enabled' => $enabled,
                    'interval' => $taskConfig['interval'],
                ]);
            }
            if(!$job->getStatus()) $job->setStatus(Job::STATUS_PENDING);

            //skip if not enabled or not in pending status
            if(!$job->getEnabled() || $job->getStatus() != Job::STATUS_PENDING) continue;

            $task = new Task($taskConfig);
            $task->setName($name);
            $job->setDescription($task->getDescription());

            $task->setJob($job);

            $result[$name] = $task;
        }

        return $result;
    }

    public function processTask($task)
    {
        $serviceName = $task->getService();
        $function = $task->getFunction();
        //var_dump('function processTask Service 85',$function);
        $task->getJob()->setStatus(Job::STATUS_STARTED);
        $task->getJob()->setLastRun(date('Y-m-d H:i:s'));
        CollectionJob::getInstance()->_save($task->getJob());

        try {
	
			 $service = $serviceName::getInstance();
            $service->$function();	
            $task->getJob()->setStatus(Job::STATUS_PENDING);
            $task->getJob()->setLastError(null);
        } catch (\Exception $e) {
            $task->getJob()->setStatus(Job::STATUS_ERROR);
            $task->getJob()->setLastError($e->getMessage());

		$errorLog = new Logger('errors');
		$errorLog->pushHandler(new StreamHandler(storage_path('logs/'.$task->getFunction().'.log')), Logger::INFO);
		$errorLog->info('Error'.date("Y-m-d H:i:s").
		'error=>'.print_r($e->getMessage(),true)); 

		/* Log::info('Work of Credicom crone ERROR: '.date("Y-m-d H:i:s").
			'message'.$e->getMessage()
			
			);  */
        }

        CollectionJob::getInstance()->_save($task->getJob());
    }


}