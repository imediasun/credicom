<?php

namespace App\modules\CreditRequest\Block\Reply\CoApplicant;

use \App\modules\CreditRequest\Block\Reply\Base as BaseReplyBlock;

class MainFormZander extends BaseReplyBlock {
    public $template = 'reply/mainApplicant/zander';
    public $availableRecipients = self::AVAILABLE_RECIPIENTS_BOTH;
}

