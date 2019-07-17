<?php

namespace App\modules\Core\Block;

class Base extends \App\modules\Core\Model\Base
{
    public $app;
    public $view;
    public $template;
    public $cache = false;
    public $cacheTimout = 3600; //one hour
    public $renderError = false;
    
    public function __construct($items = array())
    {
        parent::__construct($items);
        
        $this->app = \Slim\Slim::getInstance();
        $this->view = $this->app->view();

        //try loading cache
        $cachedResult = $this->loadCache();
        if($cachedResult) {
            $this->setCachedResult($cachedResult);
            return;
        }
        
        $debugTimeStart = microtime(true); 

        $this->init();
        
        $debugTimeEnd = microtime(true);
        if(defined("DEBUG_MODE") && DEBUG_MODE) {
			\cerTemplateDebug::$debug[] = array(
				'time' => ($debugTimeEnd-$debugTimeStart),
                'timeStart' => $debugTimeStart,
                'timeEnd'=> $debugTimeEnd,
				'template' => get_class($this).':init()',
			);
		}
    }
    
    public function init() {}        
    
    public function __toString()
    {
        return $this->render();
    }
    
    public function getTemplate()
    {
        $classes = class_parents($this);
        $classes = array(get_class($this) => get_class($this)) + $classes;
  
        foreach($classes as $class) {
            $classParts = explode('\\',$class, 3);
            $module = $classParts[0];
            $template = sprintf('%s/blocks/%s', $module, $this->template);
            
            $templatePathname = $this->view->getTemplatePathname($template);
            if(is_file($templatePathname)) return $template;
        }

        return null;
    }
    
    public function getCacheKey() {
        return str_replace(['\\','_'], '', get_class($this));
    }
    
    public function loadCache() {
        if(!$this->cache || !$this->template) return false;
        
        $cacheFileName = sprintf('%s/files/cache/cache_%s',BASE_DIR, $this->getCacheKey());
        $cacheLife = $this->cacheTimout ? $this->cacheTimout : 60; //caching time, in seconds  - one minute by default

        //check for cache
        if(is_file($cacheFileName) && (time() - filemtime($cacheFileName) < $cacheLife)){
            return file_get_contents($cacheFileName);
        }
        return false;
    }
    
    public function saveCache($content) {
        if(!$this->cache || !$this->template) return false;
        
        $cacheFileName = sprintf('%s/files/cache/cache_%s',BASE_DIR, $this->getCacheKey());
        file_put_contents($cacheFileName,$content);
    }
    
    public function render($data = null)
    {
        if(!$this->template) return '';
        
        //try loading cache
        if(($result = $this->getCachedResult())) return $result;
        
        //render
        $this->renderError = false;
        try {
            $template = $this->getTemplate();
            $content = $this->view->renderTemplate($template, $this->getViewData());
            if($this->cache) $this->saveCache($content);
            return $content;
        } catch (\Exception $e) {
            $this->renderError = true;
            return '<pre>'.$e->__toString().'</pre>';
        }
    }
    
}
