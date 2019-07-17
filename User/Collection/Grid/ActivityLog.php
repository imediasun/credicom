<?php

namespace User\Collection\Grid;

use \Core\Collection\Grid\Base as BaseGridCollection;

use \User\Collection\ActivityLog as CollectionActivityLog;

class ActivityLog extends BaseGridCollection
{
    public $collectionClass = CollectionActivityLog::class;
}
