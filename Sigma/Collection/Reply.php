<?php
namespace App\modules\Sigma\Collection;

use \App\modules\Sigma\Model\Reply as ModelReply;
use \App\modules\Core\Collection\Base as BaseCollection;

class Reply extends BaseCollection
{
    public $table = 'sigma_reply';
    public $tableAlias = 'sr';
    public $modelClass = ModelReply::class;
}