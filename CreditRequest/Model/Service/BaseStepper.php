<?php
namespace App\modules\CreditRequest\Model\Service;

//traits
use \App\modules\Core\Model\Base;
use \App\modules\Core\Model\Traits\Singleton;


use \App\modules\Core\Model\Base as BaseModel;
use \App\modules\Core\Model\Registry;
use \App\modules\Core\Model\Utils;
use \App\modules\CreditRequest\Model\CreditRequest as ModelCreditRequest;
use \App\modules\CreditRequest\Block;
use \App\modules\CreditRequest\Collection\CreditRequest as CollectionCreditRequest;
use \App\modules\Client\Collection\Client as CollectionClient;
use \App\modules\CreditRequest\Model\Service as CreditRequestService;
use \App\modules\CreditRequest\Model\Service\Mail as CreditRequestMailService;
use \App\modules\CreditRequest\Model\Service\Sms as CreditRequestSmsService;
use \App\modules\CreditRequest\Model\Service\Epost as EpostService;
use \App\modules\CreditRequest\Model\Tcpdf\Epost as EpostPdf;
use App\Http\ArraysClass;
use App\CreditOrder;
use Log;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use \App\Services\Services;

abstract class BaseStepper extends BaseModel
{
    use Singleton;

    public $enabled = false;

 /*    public function __construct(array $items = array())
    {
        parent::__construct($items);
        $this->init();
		
		$orderLog = new Logger('files');
		$orderLog->pushHandler(new StreamHandler(storage_path('logs/stepper.log')), Logger::INFO);
		$orderLog->info('Init of Credicom stepper'.date("Y-m-d H:i:s").
		' init=>'); 
    } */

    abstract public function init();
    abstract public function initConfig();
    abstract public function getStepsConfig();
	abstract public function processStep($stepConfig);
	abstract public function processUpdateAction($config, $list);

/*     public function addCreditRequestNote($text, $creditRequest) {
        return CreditRequestService::getInstance()->addCreditRequestNote($text, $creditRequest);
    } */

    public function process(){
		Log::info('BaseStepper.php: '.date("Y-m-d H:i:s").
			'start process processStep 61string');
		$orderLog = new Logger('files');
		$orderLog->pushHandler(new StreamHandler(storage_path('logs/stepper.log')), Logger::INFO);
		$orderLog->info('Enabled in stepper'.
		' enabled=>'.$this->enabled);
		
        if(!$this->enabled) return;


        foreach($this->getStepsConfig()->toArray() as $stepConfig) {
            $this->processStep($stepConfig);
        }
		Log::info('BaseStepper.php: '.date("Y-m-d H:i:s").
			'end process process 78string');
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
        //

        //$this->$function($config, $list);
		if($function!='processUpdateAction'){
			if(!method_exists(Services::getInstance(), $function)) return;
			Services::getInstance()->$function($config, $list);
		}
		else{
			if(!method_exists($this, $function)) return;
			$this->$function($config, $list);
		}
		
    }

/* 
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
                $this->addCreditRequestNote(
                    sprintf('Email an "%s" gesendet: "%s"', $creditRequest->getEmail(), $mailBlock->creditRequestNote),
                    $creditRequest
                );
            } else {
                $this->addCreditRequestNote($mailService->getErrorMessage(), $creditRequest);
            }
        } 
    }

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
                $this->addCreditRequestNote(
                    sprintf('SMS an "%s" gesendet: "%s"', $smsBlock->getPhone(), $smsBlock->creditRequestNote),
                    $creditRequest
                );
            } else {
                $this->addCreditRequestNote($smsService->getErrorMessage(), $creditRequest);
            }
        } 
    }


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
                $this->addCreditRequestNote(
                    sprintf('Epost gesendet: "%s"', $epostBlock->creditRequestNote),
                    $creditRequest
                );
            } else {
                $this->addCreditRequestNote($epostService->getErrorMessage(), $creditRequest);
            }
        } 
    }
	 */
    
}