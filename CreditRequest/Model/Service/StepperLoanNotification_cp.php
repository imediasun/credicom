<?php
namespace App\modules\CreditRequest\Model\Service;

//traits
use \App\modules\Core\Model\Base;
use \App\modules\Core\Model\Traits\Singleton;

//models
use \App\modules\Core\Model\Base as BaseModel;
use \App\modules\Core\Model\Registry;
use \App\modules\Core\Model\Utils;

use \App\modules\CreditRequest\Model\CreditRequest as ModelCreditRequest;

use \App\modules\CreditRequest\Block;

//collections
use \App\modules\CreditRequest\Collection\CreditRequest as CollectionCreditRequest;
use \App\modules\Client\Collection\Client as CollectionClient;

//services and stuff
use \App\modules\CreditRequest\Model\Service as CreditRequestService;
use \App\modules\CreditRequest\Model\Service\Mail as CreditRequestMailService;
use \App\modules\CreditRequest\Model\Service\Sms as CreditRequestSmsService;
use \App\modules\CreditRequest\Model\Service\Epost as EpostService;
use \App\modules\CreditRequest\Model\Tcpdf\Epost as EpostPdf;
use App\Http\ArraysClass;
use App\CreditOrder;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
class StepperLoanNotification_cp extends BaseModel
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

    public function addCreditRequestNote($text, $creditRequest) {
        return CreditRequestService::getInstance()->addCreditRequestNote($text, $creditRequest);
    }

    public function process()
    {
		dump('$this->enabled',$this->enabled);

		$orderLog = new Logger('files');
		$orderLog->pushHandler(new StreamHandler(storage_path('logs/stepper.log')), Logger::INFO);
		$orderLog->info('Enabled in stepper'.
		' enabled=>'.$this->enabled);
		
        if(!$this->enabled) return;


        foreach($this->getStepsConfig()->toArray() as $stepConfig) {
			
            $this->processStep($stepConfig);
        }
    }

    public function processStep($stepConfig)
    {
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
    }

    public function processAction($config, $list)
    {
        $type = $config->getType();
		$orderLog = new Logger('files');
		$orderLog->pushHandler(new StreamHandler(storage_path('logs/stepper.log')), Logger::INFO);
		$orderLog->info('Checking type action stepper'.date("Y-m-d H:i:s").
		' type=>'.print_r($type,true));
        if(!$type) return;
        $function = sprintf('process%sAction', ucfirst($type));
        if(!method_exists($this, $function)) return;

        $this->$function($config, $list);
    }

    /**
     * Send email to client
     * @param $config
     * @param array $list
     */
    public function processMailAction($config, $list)
    {
        $mailService = CreditRequestMailService::getInstance();
        foreach($list as $creditRequest) {
            $blockClass = $config->getBlock();
            $client = CollectionClient::getInstance()->load($creditRequest->getKid());
            $mailBlock = new $blockClass([
                'creditRequest' => $creditRequest,
                'client' => $client,
                'sender' => $mailService->getSender(),
                'recipient' => new BaseModel([
                    'email' => $creditRequest->getEmail(),
                ])
            ]);
            $mailSendResult = $mailService->send($mailBlock);
            if($mailSendResult) {
                //add credit request note about sent email
                $this->addCreditRequestNote(
                    sprintf('Email an "%s" gesendet: "%s"', $creditRequest->getEmail(), $mailBlock->creditRequestNote),
                    $creditRequest
                );
            } else {
                //add credit request note about error
                $this->addCreditRequestNote($mailService->getErrorMessage(), $creditRequest);
            }
        } 
    }

    /**
     * Send sms to client
     * @param $config
     * @param $list
     */
    public function processSmsAction($config, $list)
    {
         $smsService = CreditRequestSmsService::getInstance();
        foreach($list as $creditRequest) {
            $client = CollectionClient::getInstance()->load($creditRequest->getKid());

            $clientPhone = $creditRequest->getHandyv() . $creditRequest->getHandy();
            if (empty(trim($clientPhone))) {
                $clientPhone = $creditRequest->getTelefonv() . $creditRequest->getTelefon();
            }

            $blockClass = $config->getBlock();
            $smsBlock = new $blockClass([
                'creditRequest' => $creditRequest,
                'client' => $client,
                'phone' => $clientPhone,
            ]);
			$orderLog = new Logger('files');
		$orderLog->pushHandler(new StreamHandler(storage_path('logs/stepper.log')), Logger::INFO);
		$orderLog->info('Step in stepper'.
		' sms1=>');
            $smsSendResult = $smsService->send($smsBlock);
			$orderLog = new Logger('files');
		$orderLog->pushHandler(new StreamHandler(storage_path('logs/stepper.log')), Logger::INFO);
		$orderLog->info('Step in stepper'.
		' sms2=>');
            if ($smsSendResult) {
                //add credit request note about sent sms
                $this->addCreditRequestNote(
                    sprintf('SMS an "%s" gesendet: "%s"', $smsBlock->getPhone(), $smsBlock->creditRequestNote),
                    $creditRequest
                );
            } else {
                //add credit request note about error
                $this->addCreditRequestNote($smsService->getErrorMessage(), $creditRequest);
            }
        } 
    }

    /**
     * Send mail via sftp mail server
     *
     * @param $config
     * @param $list
     */
    public function processEpostAction($config, $list)
    {
         $epostService = EpostService::getInstance();
        foreach($list as $creditRequest) {
            $blockClass = $config->getBlock();
            $epostBlock = new $blockClass([
                'creditRequest' => $creditRequest,
                'sender' => $epostService->getConfig()->getSender(),
            ]);
            $epostSendResult = $epostService->send($epostBlock, EpostPdf::class);
            if ($epostSendResult) {
                //add credit request note about sent sms
                $this->addCreditRequestNote(
                    sprintf('Epost gesendet: "%s"', $epostBlock->creditRequestNote),
                    $creditRequest
                );
            } else {
                //add credit request note about error
                $this->addCreditRequestNote($epostService->getErrorMessage(), $creditRequest);
            }
        } 
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