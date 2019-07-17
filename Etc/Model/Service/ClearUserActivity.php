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

class ClearUserActivity extends BaseModel{
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




    public function processClear()
    {
        if(!$this->enabled) return;
		$array=['opened'=>null];
		CreditOrder::where('opened','!=',null)->update($array);
        dump('END _ processReceive');
    }
	

	

  

}