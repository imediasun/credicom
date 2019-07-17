<?php
namespace App\modules\Core\Model\Traits;

trait Singleton {

    private static $_instances = array();
    public static function getInstance($data = array()) {
        $class = get_called_class();
        if (!isset(self::$_instances[$class])) {
            self::$_instances[$class] = new $class($data);
        }
        return self::$_instances[$class];
    }

    public function __clone() {
        trigger_error('Cloning '.__CLASS__.' is not allowed.',E_USER_ERROR);
    }

    public function __wakeup() {
        trigger_error('Unserializing '.__CLASS__.' is not allowed.',E_USER_ERROR);
    }
}