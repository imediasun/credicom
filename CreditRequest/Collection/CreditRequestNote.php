<?php
namespace CreditRequest\Collection;

use \Core\Collection\Base as BaseCollection;
use \CreditRequest\Model\CreditRequestNote as ModelCreditRequestNote;

class CreditRequestNote extends BaseCollection
{
    public $table = 'kreditanfragen_bemerkungen';
    public $tableAlias = 'kb';

    public $modelClass = ModelCreditRequestNote::class;
}
