<?php

namespace App\modules\CreditRequest\Block\Mail\FormReplyInfoNotification\CoApplicant;

use \App\modules\CreditRequest\Block\Mail\Base as BaseMailBlock;

class InfoMa extends BaseMailBlock {
    public $availableRecipients = self::AVAILABLE_RECIPIENTS_APPLICANT;
    public $template = 'mail/formReplyInfoNotification/coApplicant/InfoMa';
    public $mailSubject = 'Ihre Anfrage bei credicom.de';
    public $creditRequestNote = "Form's reply type - Green";
    public $sender;
    public $to;
    public $client;
    public $creditRequest;
    public $host;

    public function __construct($array=array())
    {
        $this->to=$array['recipient'];
        $this->sender=$array['sender'];
        $this->client=$array['client'];
        $this->creditRequest=$array['creditRequest'];
		$this->host=url('/');
    }

}

