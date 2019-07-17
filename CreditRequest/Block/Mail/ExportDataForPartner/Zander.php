<?php

namespace App\modules\CreditRequest\Block\Mail\ExportDataForPartner;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class Zander extends BaseNotification {
    public $template = '/mail/exportDataForPartner/zander';
    public $mailSubject = 'credicom';
    public $creditRequestNote = "credit12.de - export";
    public $sender;
    public $client;
    public $attachment;

    public function __construct($array=array())
    {

        $this->recipient=$array['client']->email;
        $this->addresser=$array['rewriteSender'];
        $this->client=$array['client'];
        $this->attachment=$array['attachment'];

    }
}

