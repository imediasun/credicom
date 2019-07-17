<?php

namespace App\modules\CreditRequest\Block\Reply\MainApplicant;

use \App\modules\CreditRequest\Block\Reply\Base as BaseReplyBlock;

class Green extends BaseReplyBlock {
    public $template = 'reply/mainApplicant/greenType';
    public $availableRecipients = self::AVAILABLE_RECIPIENTS_APPLICANT;
}

