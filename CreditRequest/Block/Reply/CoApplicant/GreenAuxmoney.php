<?php

namespace App\modules\CreditRequest\Block\Reply\CoApplicant;

use \App\modules\CreditRequest\Block\Reply\Base as BaseReplyBlock;

class GreenAuxmoney extends BaseReplyBlock {
    public $template = 'reply/coApplicant/greenAuxmoneyType';
    public $availableRecipients = self::AVAILABLE_RECIPIENTS_BOTH;
	
	 public function __construct($array=array())
    {
        $this->auxmoney=$array['auxmoney'];
    }
}




