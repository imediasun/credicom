<?php
namespace App\modules\Cron\Model;

use \App\modules\Core\Model\Base as BaseModel;


class Job extends BaseModel {

    const STATUS_PENDING = 'pending';
    const STATUS_STARTED = 'started';
    const STATUS_ERROR = 'error';
}