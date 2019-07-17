<?php

namespace App\modules\CreditRequest\Block\Mail\ExportDataForPartner;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class Planfinanz24 extends BaseNotification {
    public $template = 'mail/exportDataForPartner/planfinanz24';
    public $mailSubject = 'import-credicom';
    public $creditRequestNote = "planfinanz24 - export data";

    public function __construct($array=array())
    {
        $this->recipient=$array['recipient'];
        $this->addresser='svexport@credicom.de';//$array['rewriteSender'];
        $this->client=$array['client'];
        $this->attachment=$array['attachment'];

    }
}


