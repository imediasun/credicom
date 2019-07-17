<?php

namespace App\modules\Cron\Collection\Grid;

use App\Cron;
use \App\modules\Core\Collection\Grid\Base as BaseGridCollection;

use \App\modules\Cron\Collection\Job as CollectionJob;

class Job extends BaseGridCollection
{

    public $collectionClass = CollectionJob::class;//
}
