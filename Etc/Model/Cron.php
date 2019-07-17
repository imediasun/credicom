<?php
namespace App\modules\Etc\Model;

use \App\modules\Core\Model\Traits\Singleton;

use \App\modules\Etc\Model\Service\ClearUserActivity as ClearUserActivity;
use \App\modules\Etc\Model\Service\Test as Test;
class Cron {
    use Singleton;

	
	 public function processClearUserActivity() {
        ClearUserActivity::getInstance()->processClear();
    }
	
	 public function processTest() {
        Test::getInstance()->processClear(); 
    }
}