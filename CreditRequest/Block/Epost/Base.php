<?php

namespace App\modules\CreditRequest\Block\Epost;

use \App\modules\Core\Block\Notification\Base as BaseNotification;

class Base extends BaseNotification {
    const AVAILABLE_RECIPIENTS_APPLICANT = 'applicant';
    const AVAILABLE_RECIPIENTS_COAPPLICANT = 'coapplicant';
    const AVAILABLE_RECIPIENTS_BOTH = 'both';

    public $availableRecipients = self::AVAILABLE_RECIPIENTS_BOTH;

    public function init()
    {
        //if no co-applicant is set, use applicant instead
        $availableRecipients = $this->availableRecipients;
        if(in_array($availableRecipients, [
            self::AVAILABLE_RECIPIENTS_COAPPLICANT,
                self::AVAILABLE_RECIPIENTS_BOTH
        ]) && !$this->getCreditRequest()->getMasteller()) {
            $availableRecipients = self::AVAILABLE_RECIPIENTS_APPLICANT;
        }
        $this->setAvailableRecipients($availableRecipients);

        parent::init();
    }
}