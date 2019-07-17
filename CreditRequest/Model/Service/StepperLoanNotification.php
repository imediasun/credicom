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

use \App\modules\CreditRequest\Block;

//collections
use \App\modules\CreditRequest\Collection\CreditRequest as CollectionCreditRequest;
use \App\modules\Client\Collection\Client as CollectionClient;

//services and stuff
use \App\modules\CreditRequest\Model\Service as CreditRequestService;
use App\Http\ArraysClass;
use App\CreditOrder;
use Log;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
class StepperLoanNotification extends BaseStepper
{
    use Singleton;

    public $enabled = false;

    public function __construct(array $items = array())
    {
        parent::__construct($items);
        $this->init();
		
		Log::info('StepperLoanNotification.php: '.date("Y-m-d H:i:s").
			'start process construct 40string');
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


    public static function getValidStatusList()
    {
        //status_intern
        return [
            ModelCreditRequest::STATUS_KUNDENVERSAND_VEB,
            ModelCreditRequest::STATUS_KUNDENVERSAND_PFS_DSL,
            ModelCreditRequest::STATUS_KUNDENVERSAND_FINANZCHECK,
            ModelCreditRequest::STATUS_KUNDENVERSAND_SK,
            ModelCreditRequest::STATUS_AUXMONEY_VERTRAG,
        ];
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
                'interval' => null,
                'actions' => [
                    [
                        'type' => 'mail',
                        'block' => Block\Mail\LoanNotificationStep1::class,
                    ],
                    [
                        'type' => 'sms',
                        'block' => Block\Sms\LoanNotificationStep1::class,
                    ],
                ],
            ],
            //step #2 in progress
            [
                'notification_step' => 'step2',
                'filter' => ['notification_step' => 'step1'],
                'interval' => '3 day',
                'actions' => [
                    [
                        'type' => 'mail',
                        'block' => Block\Mail\LoanNotificationStep2::class,
                    ],
                    /* [
                        'type' => 'epost',
                        'block' => Block\Epost\LoanNotificationStep2::class,
                    ], */
                    [
                        'type' => 'sms',
                        'block' => Block\Sms\LoanNotificationStep2::class,
                    ],
                ],
            ],
            //step #3 done
            [
                'notification_step' => 'step3',
                'filter' => ['notification_step' => 'step2'],
                'interval' => '4 day',
                'actions' => [
                    [
                        'type' => 'mail',
                        'block' => Block\Mail\LoanNotificationStep3::class,
                    ],
                    [
                        'type' => 'epost',
                        'block' => Block\Epost\LoanNotificationStep3::class,
                    ],
                    [
                        'type' => 'sms',
                        'block' => Block\Sms\LoanNotificationStep3::class,
                    ],
                ],
            ],
            //step #4 done
            [
                'notification_step' => 'step4',
                'filter' => ['notification_step' => 'step3'],
                'interval' => '4 day',
                'actions' => [
                    [
                        'type' => 'mail',
                        'block' => Block\Mail\LoanNotificationStep4::class,
                    ],
                    /* [
                        'type' => 'epost',
                        'block' => Block\Epost\LoanNotificationStep4::class,
                    ],
                    [
                        'type' => 'sms',
                        'block' => Block\Sms\LoanNotificationStep4::class,
                    ], */
                ],
            ],
            //step #5 done
            [
                'notification_step' => 'step5',
                'filter' => ['notification_step' => 'step4'],
                'interval' => '4 day',
                'actions' => [
                    [
                        'type' => 'mail',
                        'block' => Block\Mail\LoanNotificationStep5::class,
                    ],
                    /* [
                        'type' => 'epost',
                        'block' => Block\Epost\LoanNotificationStep5::class,
                    ], */
                    [
                        'type' => 'sms',
                        'block' => Block\Sms\LoanNotificationStep5::class,
                    ],
                ],
            ],
            //step #6 done
            [
                'notification_step' => 'step6',
                'filter' => ['notification_step' => 'step5'],
                'interval' => '4 day',
                'actions' => [
                    [
                        'type' => 'mail',
                        'block' => Block\Mail\LoanNotificationStep6::class,
                    ],
                 /*    [
                        'type' => 'epost',
                        'block' => Block\Epost\LoanNotificationStep6::class,
                    ],
                    [
                        'type' => 'sms',
                        'block' => Block\Sms\LoanNotificationStep6::class,
                    ], */
                   
                ],
            ],
			 //step #7 done
            [
                'notification_step' => 'step7',
                'filter' => ['notification_step' => 'step6'],
                'interval' => '5 day',
                'actions' => [
                    [
                        'type' => 'mail',
                        'block' => Block\Mail\LoanNotificationStep7::class,
                    ],
                     [
                        'type' => 'epost',
                        'block' => Block\Epost\LoanNotificationStep7::class,
                    ],
                    [
                        'type' => 'sms',
                        'block' => Block\Sms\LoanNotificationStep7::class,
                    ],
                   
                ],
            ],
			//step #8 done
            [
                'notification_step' => 'step8',
                'filter' => ['notification_step' => 'step7'],
                'interval' => '3 day',
                'actions' => [
                    [
                        'type' => 'mail',
                        'block' => Block\Mail\LoanNotificationStep8::class,
                    ],
                 /*    [
                        'type' => 'epost',
                        'block' => Block\Epost\LoanNotificationStep6::class,
                    ],
                    [
                        'type' => 'sms',
                        'block' => Block\Sms\LoanNotificationStep6::class,
                    ], */
                    [
                        'type' => 'update',
                        'options' => ['status_intern' => ModelCreditRequest::STATUS_NV_KEIN_KONTAKT],
                    ],
                ],
            ],
        ];

        $result = Utils::arrayToModel($stepsConfig);
        $this->set($key, $result);
        return $result;
    }

    public function processStep($stepConfig)
    {
		Log::info('StepperLoanNotification.php: '.date("Y-m-d H:i:s").
			'start process processStep 248string');
		$orderLog = new Logger('files');
			$orderLog->pushHandler(new StreamHandler(storage_path('logs/stepper.log')), Logger::INFO);
			$orderLog->info('Work of Credicom stepper'.date("Y-m-d H:i:s").
			' processStep=>'.print_r($stepConfig,true)); 
		
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
        $filter['status_intern'] = self::getValidStatusList();
		
        if($validFromDate) {
            $filter['notification_step_updated'] = ['<=' => $validFromDate->format('Y-m-d H:i:s')];
        }
        //add global filter from config
        if(($globalFilter = $this->getConfig()->getFilter())) {
            $filter = Utils::merge(Utils::modelToArray($globalFilter), $filter);
        }
		dump($filter);
        $list = CollectionCreditRequest::getInstance()->getList(['filter' => $filter]);
		$orderLog = new Logger('files');
		$orderLog->pushHandler(new StreamHandler(storage_path('logs/stepper.log')), Logger::INFO);
		$orderLog->info('Checking credit request in stepper'.date("Y-m-d H:i:s").
		' init=>'.print_r($list,true));
		$orderLog = new Logger('files');
		$orderLog->pushHandler(new StreamHandler(storage_path('logs/stepper.log')), Logger::INFO);
		$orderLog->info('Count list in stepper'.
		' count=>'.count($list));
        if(!count($list->toArray())) return; //nothing to process

        //process each action
        foreach($stepConfig->getActions([])->toArray() as $action) {
            $this->processAction($action, $list);
        }
		
		
		dump('getNotificationStep()',$stepConfig->getNotificationStep());
        //update listed items
         $this->processAction(new BaseModel([
            'type' => 'update',
            'options' => new BaseModel([
                'notification_step' => $stepConfig->getNotificationStep(),
                'notification_step_updated' => date('Y-m-d H:i:s'),
            ]),
        ]), $list); 
		Log::info('StepperLoanNotification.php: '.date("Y-m-d H:i:s").
			'finish process processStep 302string');
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
            if(($internStatus = $config->getOptions()->getStatusIntern())) {
                CreditRequestService::getInstance()->changeInternStatus($internStatus, $creditRequest);
            }
            //update model based on sprecified options

            $creditRequest->setData($config->getOptions([])->toArray());
			


            foreach($creditRequest as $key=>$value){
			if($value){
			if($key=='svPkvDatum' && $value==""){
				$value=date('Y-m-d',strtotime(0000-00-00));
			}
			if($key=='gesamtbetrachtung' && $value==""){
				$value=0;
				$creditRequest->$key=$value;
			
			}
			
			if($key!=='gesamtbetrachtung'){
				
		  
		$creditRequest->$key=mb_convert_encoding($value, 'UTF-8', mb_detect_encoding($value, 'UTF-8, ISO-8859-1', true));
			}
			if($value==''){
				$creditRequest->$key=$value=null;
			}
			
			if($key=='createdAt' ){
			$creditRequest->$key=$value;
			}
			if($key=='updatedAt' ){
			
				$value=date("Y-m-d H:i:s");
				$creditRequest->$key=$value;
			}
			
            } 
			}
			//$credit->save();

            CollectionCreditRequest::getInstance()->_save($creditRequest);
        }
    }
}