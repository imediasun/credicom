<?php
namespace App\modules\Core\Model;
use Log;
class Base extends \Slim\Helper\Set
{
    public $formatCallback = [];
    public $clearFormatCallback = [
        'fetchFormatDate' => 'clearFormatDate',
        'explodeList' => 'implodeList',
    ];
    
    protected static $_underscoreCache = array();
    
    public function __construct($items = array())
    {
	
        //dump('array_walk',$items);
        array_walk($items, function(&$value, $key) {
            $value = $this->clearFormat($key, $value);
        });
	
        parent::__construct($items);

    }

    public function normalizeKey($name)
    {
        $name = lcfirst($name);

        if (!isset(self::$_underscoreCache[get_class($this)])){
            self::$_underscoreCache[get_class($this)] = array();
        }

        if (isset(self::$_underscoreCache[get_class($this)][$name])){
            return self::$_underscoreCache[get_class($this)][$name];
        }

        $result = str_replace('_', ' ', $name);
        $result = ucwords($result);
        $result = str_replace(' ', '', $result);
        $result = lcfirst($result);

        if(in_array($result, self::$_underscoreCache[get_class($this)])) {
            return $result;
        }

        self::$_underscoreCache[get_class($this)][$name] = $result;

        return $result;
    }

    public function denormalizeKey($key) {
        if (!isset(self::$_underscoreCache[get_class($this)])) return $key;
        if(!in_array($key, self::$_underscoreCache[get_class($this)])) return $key;

        return array_search($key, self::$_underscoreCache[get_class($this)]);
    }
    
    public function set($key, $value)
    {
        $value = $this->clearFormat($key, $value);
        parent::set($key, $value);
        return $this;
    }
    
    public function toArray()
    {
        return $this->all();
    }
    
    public function toStorageArray()
    {
        $data = $this->toArray();
        foreach($data as $k => $v) {
            
        }
    }
    
    /**
     * Set/Get attribute wrapper
     *
     * @param   string $method
     * @param   array $args
     * @return  mixed
     */
    public function __call($method, $args)
    {
		

        $key = $this->normalizeKey(substr($method,3));
        switch (substr($method, 0, 3))
        {

            case 'get' :
                $data = $this->get($key, isset($args[0]) ? $args[0] : null);
                return $data;

            case 'set' :
                $value = $this->clearFormat($key, isset($args[0]) ? $args[0] : null);
                $result = $this->set($key, isset($args[0]) ? $args[0] : null);
                return $result;

            case 'uns' :
                $result = $this->remove($key);
                return $result;

            case 'has' :
                return $this->has($key);
        }

        if(substr($method, 0, strlen('format')) == 'format')
        {
            $key = $this->normalizeKey(substr($method,6));
            return $this->format($key, isset($args[0]) ? $args[0] : null);
        }

        throw new \Exception("Invalid method ".get_class($this)."::".$method."(".print_r($args,1).")");
		
    }

    public function setData(array $data)
    {
        $this->replace($data);
    }
    
    public function format($key, $default = null)
    {
        $formatCallback = $this->getFormatCallback($key);
        if(!$formatCallback) {
            $key = $this->denormalizeKey($key);
            $formatCallback = $this->getFormatCallback($key);
        }

        if(!$formatCallback || !method_exists($this, $formatCallback)) return $default;
        
        $value = $this->get($key, $default);
        if(!$value) return $value;
        
        return $this->{$formatCallback}($value);
    }
    
    public function clearFormat($key, $value = null) {
        if(!$value) return $value;
        
        $formatCallback = $this->getFormatCallback($key);
        if(!$formatCallback || !method_exists($this, $formatCallback)) return $value;
       
        $clearFormatCallback = $this->getClearFormatCallback($formatCallback);
        if(!$clearFormatCallback || !method_exists($this, $clearFormatCallback)) return $value;
        
        return $this->{$clearFormatCallback}($value);
    }
    
    public function getFormatCallback($key) {
        return (isset($this->formatCallback[$key])) ? $this->formatCallback[$key] : null;
    }
    public function getClearFormatCallback($key) {
        return (isset($this->clearFormatCallback[$key])) ? $this->clearFormatCallback[$key] : null;
    }
    
    /* Format */
    public function fetchFormatDate($value) {
        $cfg = \CerConfiguration::getInstance();
        $format = $cfg->strftimeFormatToDateFormat($cfg->settings["date_format_input_date"]);
        $date = new \DateTime($value);
        return $date->format($format);
    }
    public function clearFormatDate($value) {
        $cfg = \CerConfiguration::getInstance();
        $format = $cfg->strftimeFormatToDateFormat($cfg->settings["date_format_input_date"]);

        $date = \DateTime::createFromFormat($format, $value);
        if($date && $date->format($format) == $value) {
            $value = $date->format('Y-m-d');
        }
        return $value;
    }
    
    public function explodeList($value) {
        if(!is_string($value)) return $value;
        return explode(',', $value);
    }
    public function implodeList($value) {
        if(!is_array($value)) return $value;
        return implode(',', $value);
    }
    
    function arrayMergeRecursiveDistinct (array $array1, array $array2)
    {
        $merged = $array1;
        foreach ( $array2 as $key => $value ) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->arrayMergeRecursiveDistinct($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }
        return $merged;
    }
    
    public function saveData($path, $data) {
        $result = file_put_contents($path, sprintf("<?php\n$%s = %s;", 'data', var_export($data, true)));
        return ($result !== false);
    }
    
    public function loadData($path) {
        if(!is_readable($path)) return null;
        include $path;
        return $data;
    }
}