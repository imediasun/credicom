<?php

namespace Core\Block\Form\Element;

use Core\Block\Base as BaseBlock;

class Select extends BaseBlock {
    public $template = 'form/element/select.php';
    
    public function init()
    {
        if(!$this->getName()) $this->setName('select');
        if(!$this->getId()) $this->setId(trim(str_replace ('__','_', str_replace(array('[',']','#'), '_', $this->getName())), '_'));
        if(!$this->getOptions()) $this->setOptions(array());
        
        $this->setViewData(array('block' => $this));
        
        if($this->getForceSelected() && $this->getSelected()) {
            $options = $this->getOptions();
            if(!isset($options[$this->getSelected()])) {
                $options[$this->getSelected()] = $this->getSelected();
                $this->setOptions($options);
            }
        }
    }
}