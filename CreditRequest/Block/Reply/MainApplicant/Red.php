<?php

namespace App\modules\CreditRequest\Block\Reply\MainApplicant;

use \App\modules\CreditRequest\Block\Reply\Base as BaseReplyBlock;

class Red extends BaseReplyBlock {
    public $template = 'reply/mainApplicant/redType';
    public $availableRecipients = self::AVAILABLE_RECIPIENTS_APPLICANT;
}



