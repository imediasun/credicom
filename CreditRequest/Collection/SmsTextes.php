<?php
namespace App\modules\CreditRequest\Collection;

use \App\modules\Core\Collection\Base as BaseCollection;
//use \App\modules\CreditRequest\Model\CreditRequest as CreditRequestModel;

class SmsTextes extends BaseCollection
{
    public $table = 'sms';//was kreditanfragen
    public $tableAlias = 'sms';

    //public $modelClass = CreditRequestModel::class;
}
