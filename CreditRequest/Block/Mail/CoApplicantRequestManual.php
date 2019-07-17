<?php

namespace App\modules\CreditRequest\Block\Mail;

use \App\modules\Core\Block\Notification\Base as BaseNotification;
use Log;
class CoApplicantRequestManual extends BaseNotification {
	public $availableRecipients = 'applicant';
    public $template = 'emails/mail/coApplicantRequest/coApplicantRequest';
    public $mailSubject = 'Ihre Kreditanfrage bei credicom - wichtige Information';
    public $creditRequestNote = 'Mitantragsteller autom.';
    
    public function setManualMode() {
        $this->template = 'emails/mail/coApplicantRequest/coApplicantManualRequest';
        $this->creditRequestNote = 'Mitantragsteller man.';
    }

    public function render(){
		Log::info('cmail block manual: '.date("Y-m-d H:i:s"));
        return view($this->template,['creditRequest'=>$this->getCreditRequest(),'availableRecipients'=>$this->availableRecipients,'manual'=>1]);
    }
}


