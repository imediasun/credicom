<?php
namespace App\modules\CreditRequest\Model\Service;

//traits
use \App\modules\Core\Model\Base;
use \App\modules\Core\Model\Traits\Singleton;
use \App\modules\Core\Model\Base as BaseModel;
//models
use \App\modules\CreditRequest\Model\Service\BaseStepper;
use \App\modules\Core\Model\Registry;
use \App\modules\Core\Model\Utils;

use \App\modules\CreditRequest\Model\CreditRequest as ModelCreditRequest;
use \App\modules\CreditRequest\Collection\CreditRequest as CollectionCreditRequest;

use \App\modules\CreditRequest\Block;
use DB;
//collections
use \App\UnterlagenSteper;
use \App\modules\Client\Collection\Client as CollectionClient;

//services and stuff
use \App\modules\CreditRequest\Model\Service as CreditRequestService;
use App\Http\ArraysClass;
use App\CreditOrder;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
class StepperUnterlagenNotification extends BaseStepper
{
    use Singleton;

    public $enabled = false;

    public function __construct(array $items = array())
    {
        parent::__construct($items);
        $this->init();
		
		$orderLog = new Logger('files');
		$orderLog->pushHandler(new StreamHandler(storage_path('logs/stepper.log')), Logger::INFO);
		$orderLog->info('Init of Credicom stepper'.date("Y-m-d H:i:s").
		' init=>'); 
    }

    public function init()
    {
        $this->initConfig();
    }


    public function initConfig() {
        $globalConfig = Registry::getInstance()->getConfig();
        $globalConfig = new ArraysClass();
        $globalConfig=$globalConfig->conf;
        $this->setConfig(Utils::arrayToModel($globalConfig['notification']['stepper_loan']));
        $this->enabled = $this->getConfig()->getEnabled();
    }

    public function getStepsConfig()
    {
        $key = 'stepsConfig';
        if($this->has($key)) return $this->get($key);

        $stepsConfig = [
            //step #1
            [
                'notification_step' => 'step1',
                'filter' => ['notification_step' => ['is' => 'NULL']],
                'interval' => '3 day',
                'actions' => [
                    [
                        'type' => 'mail',
                        'block' => Block\Mail\UnterlagenNotificationStep1::class,
                    ]/* ,
                    [
                        'type' => 'epost',
                        'block' => Block\Epost\UnterlagenNotificationStep1::class,
                    ], */
                ],
            ],
            //step #2 in progress
            [
                'notification_step' => 'step2',
                'filter' => ['notification_step' => 'step1'],
                'interval' => '5 day',
                'actions' => [
                    [
                        'type' => 'mail',
                        'block' => Block\Mail\UnterlagenNotificationStep2::class,
                    ]
                ],
            ],
            //step #3 done
            [
                'notification_step' => 'step3',
                'filter' => ['notification_step' => 'step2'],
                'interval' => '5 day',
                'actions' => [
                    [
                        'type' => 'mail',
                        'block' => Block\Mail\UnterlagenNotificationStep3::class,
                    ]
                ],
            ],
            //step #4 done
            [
                'notification_step' => 'step4',
                'filter' => ['notification_step' => 'step3'],
                'interval' => '5 day',
                'actions' => [
                    [
                        'type' => 'mail',
                        'block' => Block\Mail\UnterlagenNotificationStep4::class,
                    ]
					,
                    [
                        'type' => 'epost',
                        'block' => Block\Epost\FehlendeUnterlagenCredicom_Schritt_5::class,
                    ],
					[
                        'type' => 'sms',
                        'block' => Block\Sms\FehlendeUnterlagenCredicom_Schritt_5::class,
                    ],
                ],
            ],
            [
                'notification_step' => 'step5',
                'filter' => ['notification_step' => 'step4'],
                'interval' => '4 day',
                'actions' => [
                    [
                        'type' => 'mail',
                        'block' => Block\Mail\UnterlagenNotificationStep5::class,
                    ],
					
                    [
                        'type' => 'update',
                        'options' => ['status_intern' => ModelCreditRequest::STATUS_NV_KEIN_KONTAKT],
                    ],
                ],
            ]
        ];

        $result = Utils::arrayToModel ($stepsConfig);
        $this->set($key, $result);
        return $result;
    }

    public function processStep($stepConfig)
    {
        //interval
        $validFromDate = null;
        if($stepConfig->getInterval()) {
            $validFromDate = new \DateTime();
            if($stepConfig->getInterval()) {
                $validFromDate->sub(\DateInterval::createFromDateString($stepConfig->getInterval()));
            }
        }

   //load credit requests
        $filter = Utils::modelToArray($stepConfig->getFilter([]));
		
  dump('stepp',$filter);
		$lists = DB::table('unterlagen_stepers')->select('unterlagen_stepers.order_id')
		->where('unterlagen_stepers.notification_step_updated','<=',$validFromDate->format('Y-m-d H:i:s'))
		->where('unterlagen_stepers.notification_step',(is_array($filter['notificationStep'])) ? null : $filter['notificationStep'])
		->get();
		dump($lists);
		$list=[];
		foreach($lists as $key=>$value){
			$list[$key]=$value->order_id;
		}
		if(!count($list)) return;
		dump($list);
		$query='SELECT  k.* FROM credit_orders as k WHERE `id` IN ('.implode(',',$list).')';
        $list = CollectionCreditRequest::getInstance()->getListIn($query);
		
		$orderLog = new Logger('files');
		$orderLog->pushHandler(new StreamHandler(storage_path('logs/stepper.log')), Logger::INFO);
		$orderLog->info('Checking credit request in stepper'.date("Y-m-d H:i:s").
		' init=>'.print_r($list,true));
		$orderLog = new Logger('files');
		$orderLog->pushHandler(new StreamHandler(storage_path('logs/stepper.log')), Logger::INFO);
		$orderLog->info('Count list in stepper'.
		' count=>'.count($list));
         //nothing to process
        //process each action
        foreach($stepConfig->getActions([])->toArray() as $action) {
            $this->processAction($action, $list);
        }
		
		
		dump('getNotificationStep()',$stepConfig->getNotificationStep());
        //update listed items
         $this->processAction(new BaseModel([
            'type' => 'update',
            'options' => [
                'notification_step' => $stepConfig->getNotificationStep(),
                'notification_step_updated' => date('Y-m-d H:i:s'),
            ],
        ]), $list); 
    }

	
    public function processUpdateAction($config, $list)
    {
		$orderLog = new Logger('files');
		$orderLog->pushHandler(new StreamHandler(storage_path('logs/stepper.log')), Logger::INFO);
		$orderLog->info('Checking update config stepper'.date("Y-m-d H:i:s").
		' config=>'.print_r($config,true));
		
		
		$orderLog = new Logger('files');
		$orderLog->pushHandler(new StreamHandler(storage_path('logs/stepper.log')), Logger::INFO);
		$orderLog->info('Checking update list stepper'.date("Y-m-d H:i:s").
		' init=>'.print_r($list,true));
		foreach($list as $creditRequest) {
			
            //update status via global service, with comment and stuff
            if(($internStatus = (isset( $config->getOptions()['statusIntern']) && $config->getOptions()['statusIntern']==16) ? 16 : null)) {
				dump('status',$internStatus);
                CreditRequestService::getInstance()->changeInternStatus($internStatus, $creditRequest);
            }
			
				$update=[
				'notification_step' => $config->getOptions([])['notification_step'],
                'notification_step_updated' => $config->getOptions([])['notification_step_updated']
				];
			
			\App\UnterlagenSteper::where('order_id',$creditRequest->id)->update( $update);

        }
    }
}