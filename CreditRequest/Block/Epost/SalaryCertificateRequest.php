<?php

namespace App\modules\CreditRequest\Block\Epost;

use \App\modules\CreditRequest\Block\Epost\Base as BaseEpostNotification;

class SalaryCertificateRequest extends BaseEpostNotification {
    public $template = 'epost/salaryCertificateRequest';
    public $creditRequestNote = 'Anforderung Ihrer Gehaltsbescheinigung';
    public $availableRecipients = self::AVAILABLE_RECIPIENTS_BOTH;
	
	 public function render(){
	
        return view($this->template,['creditRequest'=>$this->getCreditRequest(),'client'=>$this->getClient(),'sender'=>$this->getSender(),'str'=>$this->getCreditRequest()->str,
		'strNr'=>$this->getCreditRequest()->strNr]);
    }
}