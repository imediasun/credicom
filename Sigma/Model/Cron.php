<?php
namespace App\modules\Sigma\Model;

use \App\modules\Core\Model\Traits\Singleton;
use \App\modules\Sigma\Model\Service as ServiceSigma;
use \App\modules\Sigma\Model\Service\GetCsvFromFtp as GetCsvFromFtp;
use \App\modules\Etc\Model\Service\ClearUserActivity as ClearUserActivity;
use Illuminate\Support\Facades\Queue;
use App\Jobs\GetCsvFromSftp;
use App\Jobs\GetCsvFromEmail;

class Cron {
    use Singleton;

    public function processGenerate() {
        ServiceSigma::getInstance()->processGenerate();
    }

    public function processReceive() {
        ServiceSigma::getInstance()->processReceive();
    }

    public function processClarificationTimeout() {
        ServiceSigma::getInstance()->processClarificationTimeout();
    }
	
	 public function processCSV() {
        //GetCsvFromFtp::getInstance()->processCSV();
		GetCsvFromSftp::getInstance()->handle();
    }
		 
	public function processEmailCSV() {
		GetCsvFromEmail::getInstance()->handle();
    }
	
}