<?php
namespace Core\Model\Client;

use Core\Model\Base as BaseModel;

class SecureShell extends BaseModel
{
    public $settings = array();
    public $active = false;
    public $errors = array();
    
    static $enableDebug = false;
    static $debug = [];
    
      
    public function __construct($config = array())
    {
        $this->settings = $config;
        
        set_error_handler(array($this, 'errorHandler'), E_USER_NOTICE);
        
        try {
            $this->initClient();
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage();
            $this->active = false;
        }
    }
    
    function errorHandler($errno, $errstr, $errfile, $errline) {
        $this->errors[] = $errstr;
        return true;
    }
    
    public function initClient()
    {
        if(!$this->active) return false;
        
        $this->client = new \phpseclib\Net\SSH2($this->settings['server'], $this->settings['port']);

        $key = new \phpseclib\Crypt\RSA();
        if(isset($this->settings['user']) && isset($this->settings['pass'])) {
            $key->setPassword($this->settings['pass']);
        } else if(isset($this->settings['user']) && isset($this->settings['identityFile'])) {
            $key->loadKey(file_get_contents($this->settings['identityFile']));
        }

        $success = $this->client->login($this->settings['user'], $key);
        
        if(!$success) {
            $this->active = false;
            $this->errors[] = 'Authentication Failed';
        }
    }
    
    /**
     * Exec one command, env won't be preserved
     * @param string $command
     * @return string
     */
    public function exec($command)
    {
        $response = trim($this->client->exec($command));
        
        if(self::$enableDebug) self::$debug[] = ['command' => $command, 'response' => $response];

        return $response;
    }
}