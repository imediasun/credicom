<?php

namespace Core\Block\Form\Element;

use Core\Block\Base as BaseBlock;

class Checkboxes extends BaseBlock {
    public $template = 'form/element/checkboxes.php';
    
    public function init()
    {
        if(!$this->getName()) $this->setName('select');
        if(!$this->getId()) $this->setId(trim(str_replace ('__','_', str_replace(array('[',']','#'), '_', $this->getName())), '_'));
        if(!$this->getOptions()) $this->setOptions(array());
        if(!$this->getSelected()) $this->setSelected(array());
        
        $this->setViewData(array('block' => $this));
    }
}