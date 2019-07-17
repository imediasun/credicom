<?php
namespace App\modules\CreditRequest\Collection;

use \App\modules\Core\Collection\Base as BaseCollection;
use \App\modules\CreditRequest\Model\CreditRequest as CreditRequestModel;

class CreditRequest extends BaseCollection
{
    public $table = 'credit_orders';//was kreditanfragen
    public $tableAlias = 'k';

    public $modelClass = CreditRequestModel::class;
}
