<?php
namespace App\modules\Cron\Model;

use \App\modules\Core\Model\Base as BaseModel;

use \App\modules\Cron\Model\Job;

class Task extends BaseModel {

    public function isProcessable()
    {
        $job = $this->getJob();
		dump('job',$job);
dump($job->getLastRun());
        if(!$job || !$job->getEnabled() || $job->getStatus()!= Job::STATUS_PENDING) return false;
dump('!return');
        $lastRun = new \DateTime($job->getLastRun() ? $job->getLastRun() : sprintf('-%s',$job->getInterval()));
		dump('$lastRun',$lastRun);
        $nexRun = clone $lastRun;
		dump('$nexRun',$nexRun);
		dump('interval',$job->getInterval());
        $nexRun->add(\DateInterval::createFromDateString($job->getInterval()));
		dump('$nexRun2',$nexRun);
dump('time',time() >= $nexRun->getTimestamp());
        return (time() >= $nexRun->getTimestamp());
    }

}