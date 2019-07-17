<?php
namespace User\Collection;

use \Core\Collection\Base as BaseCollection;

class ActivityLog extends BaseCollection
{
    public $table = 'user_activity_log';
    public $tableAlias = 'ual';
}
