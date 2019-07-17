<?php
namespace User\Collection;

use \Core\Collection\Base as BaseCollection;

class UserAdmin extends BaseCollection
{
    public $table = 'user_admin';
    public $tableAlias = 'ua';
}
