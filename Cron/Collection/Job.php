<?php
namespace App\modules\Cron\Collection;

use \App\modules\Cron\Model\Job as ModelJob;
use \App\modules\Core\Collection\Base as BaseCollection;

class Job extends BaseCollection
{
    public $table = 'crons';
    public $modelClass = ModelJob::class;

    /**
     id, name, enabled, interval, status, last_run, last_error
     */

    public function getListByName($queryParams = [])
    {
        $list = parent::getList($queryParams);
        $result = [];

        foreach ($list as $item) {
            $result[$item->getName()] = $item;
        }

        return $result;
    }
}
