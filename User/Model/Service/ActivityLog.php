<?php

namespace User\Model\Service;

//models
use \Core\Model\Base as BaseModel;
use \User\Model\ActivityLog as ModelActivityLog;

//collections
use \User\Collection\ActivityLog as CollectionActivityLog;

//traits
use \Core\Model\Traits\Singleton;

class ActivityLog extends BaseModel {
    use Singleton;


    function addEntry() {
        $entry = new ModelActivityLog([
            'date' => date('Y-m-d H:i:s'),
            'ip' => $this->getUserIp(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'session' => session_id(),
            'url' => $_SERVER["REQUEST_URI"],
            'method' => $_SERVER['REQUEST_METHOD'],
            'data' => count($_REQUEST) ? var_export($_REQUEST, true) : '',
        ]);
        CollectionActivityLog::getInstance()->save($entry);
    }

    function getUserIp() {
        if(isset($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
        if(isset($_SERVER['HTTP_X_FORWARDED'])) return $_SERVER['HTTP_X_FORWARDED'];
        if(isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        if(isset($_SERVER['HTTP_FORWARDED_FOR'])) return $_SERVER['HTTP_FORWARDED_FOR'];
        if(isset($_SERVER['HTTP_FORWARDED'])) return $_SERVER['HTTP_FORWARDED'];
        if(isset($_SERVER['REMOTE_ADDR'])) return $_SERVER['REMOTE_ADDR'];
        return null;
    }
}