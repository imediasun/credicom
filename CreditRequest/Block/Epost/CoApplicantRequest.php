<?php

namespace App\modules\CreditRequest\Block\Epost;

use \App\modules\CreditRequest\Block\Epost\Base as BaseEpostNotification;

class CoApplicantRequest extends BaseEpostNotification {
    public $template = 'epost/coApplicantRequest';
    public $creditRequestNote = 'Mitantragsteller Brief autom.';
    public $availableRecipients = self::AVAILABLE_RECIPIENTS_BOTH;
    
    public function setManualMode() {
        $this->template = 'epost/coApplicantManualRequest';
        $this->creditRequestNote = 'Mitantragsteller Brief man.';
    }
	
	  public function render(){
	
        return view($this->template,['creditRequest'=>$this->getCreditRequest(),'client'=>$this->getClient(),'sender'=>$this->getSender()]);
    }
    
}