<?php

namespace App\modules\CreditRequest\Controller;

use \App\modules\Core\Controller\Base as BaseController;
use App\modules\CreditRequest\Model\Service\CreditRequestForm\CoApplicant as CoApplicantServiceCreditRequestForm;
use \App\modules\CreditRequest\Model\Service\BaseMail as CreditRequestMailService;
use \App\modules\CreditRequest\Model\Service as CreditRequestService;
use \App\modules\Core\Model\Base as BaseModel;
use Log;
class CoApplicant extends BaseController {
    
    public $layout = 'core/layout/frontend.php';
    public $entityCreditRequest;
    public $entity;
    
    
    function formAction($id, $code) {
        
        $this->setEntitiesData($id, $code, $_POST['item']);

        if(!$this->entityCreditRequest) {
            $this->app->render('error.php');
            return;
        }
        
        if((int)$this->entityCreditRequest->masteller == 1) {            
            $this->app->render('alreadyReceived.php');
            exit;
        }

        $form = new \App\Http\Render\Block\CoApplicantDataForm([
            'entity' => $this->entity
        ]);

        if (isset($_POST) && isset($_POST['item'])) {

            if($form->valid()) {
                $this->view->appendData(array(
                    'id' => $id,
                    'code' => $code,    
                    'item' => $_POST['item'],
                ));
                
                $this->app->render('reply.php'); 
                exit;
            }
        }  
        
        $this->view->appendData(array(
            'form' => $form               
        ));

        $this->app->render('form.php');   
        
    }
    
    public function setDefaultValueToForm($entity) {
        $entity['coapplicant_befristet'] = NULL;
        $entity['coapplicant_same_household'] = 2;
        return $entity;
    }
    
	public function addCreditRequestNote($text, $creditRequest) {
        return CreditRequestService::getInstance()->addCreditRequestNote($text, $creditRequest);
    }
	
    public function getReplyTypeAction() {

		 $form = new \App\Http\Render\Block\CoApplicantDataForm([
            'entity' => $this->entity
        ]);


        $id = session()->get('coapplican_id');
        $code = session()->get('coapplican_code');
        $item = json_decode(session()->get('coapplican_item'));
		session()->forget('coapplican_id');
		session()->forget('coapplican_code');
		session()->forget('coapplican_item');
        $result = $this->setEntitiesData($id, $code, $item); 

        if(!$result) {
            $html = '<h2 class="mb-xxl">Oops! Something went wrong!</h2>';        
            die($html);
        }

		
		$creditRequest = \App\CreditOrder::where('id', $id)->first();
		
		/* if($creditRequest->status_intern!==76){
			$_SESSION['coapplican_not_wdvma']=22;

		} */
		$coApplicantServiceCreditRequestForm = CoApplicantServiceCreditRequestForm::getInstance((isset($_POST['mode'])) ? $_POST['mode'] : null);
        $coApplicantServiceCreditRequestForm->setCreditRequest($this->entityCreditRequest)->setFormEntity($this->entity); 
        $reply = $coApplicantServiceCreditRequestForm->processDataForm(); 
		$this->processMasteller($creditRequest);
		
        //unset($_SESSION['coapplican_not_wdvma']);
         $result = [

            'menu'=>'',
            'data_meta'=>null,
            'creditRequest' => $reply['CreditRequest'],
            'form'=>$form,
            //'creditFormUrls'=>$creditFormUrls,
            'reply'=>$reply['reply'],'availableRecipients'=>$reply['reply']->availableRecipients

        ];
		
		//send email to info
 //send notification email to info@credicom.de
        $mailService = CreditRequestMailService::getInstance(); 
		$client=\App\Client::where('id',$reply['CreditRequest']->kid)->first();

        $mailBlock = new \App\modules\CreditRequest\Block\Mail\FormReplyInfoNotification\CoApplicant\InfoMa([
            'creditRequest' => $reply['CreditRequest'],
            'client' => $client,
            'sender' => $mailService->getSender(),
            'recipient' => 'info@credicom.de'
        ]);
		
		
		
        $mailSendResult = $mailService->send($mailBlock);
        //make append to view
        return view($reply['reply']->template, $result);
        //$html = $reply->render();        
        //die($html);
    }
    
	
 	    public function processMasteller($creditRequest) {
            //add credit request note about masteller
            $this->addCreditRequestNote( 
                'Mitantragsteller wurde hinzugefügt',
                $creditRequest
            );
			 return [
                'is_success' => true,
                'message' => sprintf('Notification: %s gesendet!', 'Mitantragsteller wurde hinzugefügt')
            ];
    
    } 
	
    public function setEntitiesData($id, $code, $item) {
        
        if(!isset($id) || !isset($code)) {
            //redirect
            header('Location: /');
            exit;
        }
        
        $this->entity = new \App\Http\Model\Entity\Form\Credit();        
        $collectionCreditRequest = \App\modules\CreditRequest\Collection\CreditRequest::getInstance();

        /** @var \CreditRequest\Model\CreditRequest $this->entityCreditRequest */
        $this->entityCreditRequest = $collectionCreditRequest->load(['filter' => [
            'id' => $id,
            'code' => $code,
        ]]);

  /*       $coApplicantServiceCreditRequestForm = CoApplicantServiceCreditRequestForm::getInstance();
        if($this->entityCreditRequest) {
		Log::info('session_null: '.date("Y-m-d H:i:s").print_r($_SESSION,true));
            $this->entity = $coApplicantServiceCreditRequestForm->setDataToFormEntity($this->entity, $this->entityCreditRequest);
        } else {
            return false;
        }
        
        $this->entity = $this->setDefaultValueToForm($this->entity) */;
        
        if($item) {
            $this->entity->setData($item);
        }
        
        return true;
    }
   
}

