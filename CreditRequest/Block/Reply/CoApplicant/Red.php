<?php

namespace App\modules\CreditRequest\Block\Reply\CoApplicant;

use \App\modules\CreditRequest\Block\Reply\Base as BaseReplyBlock;

class Red extends BaseReplyBlock {
    public $template = 'reply/coApplicant/redType';
    public $availableRecipients = self::AVAILABLE_RECIPIENTS_BOTH;
	

}



