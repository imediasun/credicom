<?php

namespace Core\Block\Form\Element;

use Core\Block\Base as BaseBlock;

class Checkbox extends BaseBlock {
    public $template = 'form/element/checkbox.php';
    
    public function init()
    {
        if(!$this->getName()) $this->setName('select');
        if(!$this->getId()) $this->setId(trim(str_replace ('__','_', str_replace(array('[',']','#'), '_', $this->getName())), '_'));
        
        $this->setViewData(array('block' => $this));
    }
}