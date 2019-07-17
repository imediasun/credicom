<?php
namespace App\modules\Core\Model;

use \App\modules\Core\Model\Traits\Singleton as TraitSingleton;

class Registry extends \App\modules\Core\Model\Base
{
    use TraitSingleton;

    // Config
    public function getConfig() {
        $key = 'config';
        if($this->has($key)) return $this->get($key);

        global $conf;
        $this->set($key, $conf);
        
        return $conf;
    }

}


