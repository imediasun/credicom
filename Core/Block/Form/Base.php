<?php
namespace App\modules\Core\Block\Form;

use \App\modules\Core\Block\Base as BaseBlock;

class Base extends BaseBlock {

    public $entityClass = null;
    public $ulrs = [];

    public $subFormsList = [];
    public $disabledFields = [];
    public $subForms = [];
    public $validationErrors = [];

    public function __construct($data = array()){
        parent::__construct($data);

        //init
        $this->initSubForms();
        $this->setViewData(array('entity' => $this->getEntity()));
    }

    public function initSubForms() {
        if(!$this->getEntity() && $this->entityClass) $this->setEntity(new $this->entityClass());

        $data = [
            'entity' => $this->getEntity(),
            'ulrs' => $this->ulrs,
        ];

        foreach($this->subFormsList as $key => $className) {
            $subFormData = $data;
            if(isset($this->disabledFields[$key])) {
                $subFormData['disabledFields'] = $this->disabledFields[$key];
            }
            $this->subForms[$key] = new $className($subFormData);
        }
    }


    public function valid()
    {
        $this->validationErrors = [];

        //validate subforms
        foreach($this->subForms as $subform) {
//            if(!$subform->active) continue;
            if(!$subform->valid()) {
                $this->validationErrors = array_merge($this->validationErrors, $subform->validationErrors);
            }
        }

        return !(bool)count($this->validationErrors);
    }
}


