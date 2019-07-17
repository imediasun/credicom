<?php

namespace App\modules\CreditRequest\Block\Reply\MainApplicant;

use \App\modules\CreditRequest\Block\Reply\Base as BaseReplyBlock;

class Zander extends BaseReplyBlock {
    public $template = 'reply/mainApplicant/zander';
    public $availableRecipients = self::AVAILABLE_RECIPIENTS_APPLICANT;
}

