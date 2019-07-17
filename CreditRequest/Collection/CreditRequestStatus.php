<?php
namespace App\modules\CreditRequest\Collection;

use \App\modules\Core\Collection\Base as BaseCollection;
use \App\modules\CreditRequest\Model\CreditRequestStatus as CreditRequestStatusModel;

class CreditRequestStatus extends BaseCollection
{
    public $table = 'credit_order_statuses';
    public $tableAlias = 'kst';

    public $modelClass = CreditRequestStatusModel::class;
}

