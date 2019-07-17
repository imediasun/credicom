<?php
namespace App\modules\CreditRequest\Collection;

use \App\modules\Core\Collection\Base as BaseCollection;
use \App\modules\CreditRequest\Model\CreditCard as CreditCardModel;

class CreditCard extends BaseCollection
{
    public $table = 'credit_cards';//kreditkarten
    public $tableAlias = 'kk';

    public $modelClass = CreditCardModel::class;
}
