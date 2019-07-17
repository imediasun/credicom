<?php
namespace App\modules\Etc\Model\Service;

use App\modules\Core\Model\Traits\Singleton;
//models
use \App\modules\Core\Model\Base as BaseModel;
use \App\modules\Core\Model\Registry;
use \App\modules\Core\Model\Utils;
use \App\CreditOrder;
use App\Http\ArraysClass;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Log;
class Test extends BaseModel{
    use Singleton;

    public $enabled = true;

      public function __construct(array $items = array())
    {
        parent::__construct($items);
        $this->init();
    }

    public function init()
    {


    }




    public function processTest()
    {
        if(!$this->enabled) return;
		Log::info('Service Test : '.date("Y-m-d H:i:s"));
        dump('END _ processReceive');
    }
	

	

  

}