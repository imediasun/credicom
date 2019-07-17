<?php

namespace App\modules\CreditRequest\Block\Mail\FormReply\MainApplicant;

use \App\modules\CreditRequest\Block\Mail\Base as BaseMailBlock;

class Green extends BaseMailBlock {
    public $availableRecipients = self::AVAILABLE_RECIPIENTS_APPLICANT;
    public $template = 'mail/formReply/mainApplicant/greenType';
    public $mailSubject = 'Ihre Anfrage bei credicom.de';
    public $creditRequestNote = "Form's reply type - Green";
    public $sender;
    public $to;
    public $client;
    public $creditRequest;
    public $host;

    public function __construct($array=array())
    {
        $this->to=$array['creditRequest']->email;
        $this->sender=$array['sender'];
        $this->client=$array['client'];
        $this->creditRequest=$array['creditRequest'];
        $this->host=$array['host'];
        parent::__construct($this->creditRequest);

    }

}

