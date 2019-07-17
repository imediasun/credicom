<?php

namespace App\modules\CreditRequest\Controller;

//Controller
use \App\modules\Core\Controller\Base as BaseController;

//Service
use \App\modules\CreditRequest\Model\Service\CreditRequestForm\MainApplicant as MainApplicantServiceCreditRequestForm;

//Collection
use \App\modules\CreditRequest\Collection\CreditRequest as CollectionCreditRequest;
use App\CreditOrder;

class CreditRequest extends BaseController {
    
    public $layout = 'core/layout/frontend.php';
    public $entityCreditRequest;
    public $entity;
    
    //Metas
    public $meta_title="credicom Kreditanfrage - Sofortkredite ab 1.000,-&euro; bis 300.000,-&euro;";
    public $meta_description="credicom Kreditanfrage - Sofortkredite ab 1.000,-&euro; bis 300.000,-&euro;";
    public $meta_keywords="";

    
    function replyAction() {
        //$this->app->render('reply.php');
        $result = [

            'menu'=>'',
            'data_meta'=>null

        ];
		
        return view('reply.reply',$result);
        //exit;
    }
    
    public function getReplyTypeAction(/*$import=null*/) {

     $this->entity = new \App\Http\Model\Entity\Form\Credit(); 
        $this->entity->load();
/*		if($import!==null){
		$this->entity = new \App\Http\Model\Entity\Form\Import_from_csv();
		$this->entity->setFlags( \ArrayObject::STD_PROP_LIST|\ArrayObject::ARRAY_AS_PROPS );
		$this->entity=$this->entity->setArray($import);
		}*/
     //load entity data from storage

        //check if form is completed, redirect it if it's not
    /*    if(!$this->entity->getCompleted()) {
            header('Location: /');
            exit;
        }*/

        //force user browser to forget user data
        //$this->entity->deleteCookie();

        $creditFormUrls = [
            'ajaxSubmit' => '/kreditanfrage-post-edit.ajax',
            'checkIban'=>'/check-iban.ajax',
			'checkKonto'=>'/check-konto.ajax'
        ];

       $form = new \App\Http\Render\Block\CreditForm\Income\BankData([
            'entity' => $this->entity,
        ]);

        $collectionCreditRequest = CollectionCreditRequest::getInstance();
        $entityCreditRequest = $collectionCreditRequest->emptyLoad();
		//dump($entityCreditRequest);
        //$entityCreditRequest = new CreditOrder();
        $mainApplicantServiceCreditRequestForm = MainApplicantServiceCreditRequestForm::getInstance();
        $mainApplicantServiceCreditRequestForm->setCreditRequest($entityCreditRequest)->setFormEntity($this->entity); 
        $reply = $mainApplicantServiceCreditRequestForm->processDataForm();
		
        //dd('$reply 64',$reply);
        $result = [

            'menu'=>'',
            'data_meta'=>null,
            'creditRequest' => $reply['CreditRequest'],
            'form'=>$form,
            'creditFormUrls'=>$creditFormUrls,'availableRecipients'=>$reply['reply']->availableRecipients,
            'reply'=>$reply['reply'],

        ];
		/* unset($_COOKIE['credit-form-id']);
		setcookie('credit-form-id', '', time() - 3600); */
        //make append to view
        echo view($reply['reply']->template, $result);
    }





}

