<?php
namespace App\modules\Auxmoney\Collection;

use \App\modules\Core\Collection\Base as BaseCollection;
use \App\modules\Auxmoney\Model\Auxmoney as AuxmoneyModel;

class Auxmoney extends BaseCollection
{
    public $table = 'auxmoney_replies';
    public $tableAlias = 'aux';
    public $modelClass = AuxmoneyModel::class;
}

