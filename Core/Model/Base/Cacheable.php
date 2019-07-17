<?php

namespace Core\Model\Base;


trait Cacheable { 
    public function checkCacheRequirements() {
        if(
            !isset($this->config) || 
            !isset($this->config['cache']) || 
            !isset($this->config['cache']['baseDir']) || 
            !isset($this->config['cache']['files'])
        ) {
            throw new Excepton(sprintf('Class %s cannot implement Cacheable trait as it does not meet the requirements', get_class($this)));
        }
    }
    
    public function initCache()
    {
        $tokens = array();
        foreach($this->config as $k => $v) {
            if(is_array($v)) continue;
            $tokens[sprintf('{%s}', $k)] = $v;
        }
        foreach ($this->config['cache']['files'] as $k => $file) {
            $this->config['cache']['files'][$k] = str_replace(array_keys($tokens), $tokens, $file);
        }
    }
    
    public function saveCache($name, $data) {
        $this->checkCacheRequirements();
        
        $filePath = sprintf('%s/%s', $this->config['cache']['baseDir'], $this->config['cache']['files'][$name]);
        return $this->saveData($filePath, $data);
    }
    
    public function loadCache($name) {
        $this->checkCacheRequirements();
        
        $filePath = sprintf('%s/%s', $this->config['cache']['baseDir'], $this->config['cache']['files'][$name]);
        return $this->loadData($filePath);
    }
}