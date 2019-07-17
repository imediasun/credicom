<?php

namespace App\modules\CreditRequest\Block\Mail\FormReply\CoApplicant;

use \App\modules\CreditRequest\Block\Mail\Base as BaseMailBlock;

class MainFormGreenAuxmoney extends BaseMailBlock {
    public $availableRecipients = self::AVAILABLE_RECIPIENTS_BOTH;
    public $template = 'mail/formReply/mainApplicant/greenAuxmoneyType';
    public $mailSubject = 'Ihre Kreditanfrage - Nur noch wenige Sekunden zu Ihrem Vertrag!';
    public $creditRequestNote = "auxmoney Vertrag erstellt";
    public $sender;
    public $to;
    public $client;
    public $creditRequest;

    public function __construct($array=array())
    {
		//dump($array['auxmoney']);
        $this->to=$array['creditRequest']->email;
        $this->sender=$array['sender'];
        $this->client=$array['client'];
        $this->creditRequest=$array['creditRequest'];
$this->auxmoney=$array['auxmoney'];
    }


}







