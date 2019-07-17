<?php
namespace App\modules\Client\Collection;

use \App\modules\Client\Model\Client as ModelClient;
use \App\modules\Core\Collection\Base as BaseCollection;

class Client extends BaseCollection
{
    public $table = 'clients'; //was kunden
    public $modelClass = ModelClient::class;
}
