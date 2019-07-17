<?php

namespace App\modules\CreditRequest\Block\Mail\FormReply\CoApplicant;

use \App\modules\CreditRequest\Block\Mail\Base as BaseMailBlock;

class Green extends BaseMailBlock {
    public $availableRecipients = self::AVAILABLE_RECIPIENTS_BOTH;
    public $template = 'mail/formReply/coApplicant/greenType';
    public $mailSubject = 'Ihre Anfrage bei credicom.de';
    public $creditRequestNote = "Form's reply type - Green";
	
}

