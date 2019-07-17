<?php

namespace App\modules\CreditRequest\Controller;

//Controller
use \App\modules\Core\Controller\Base as Controller;
use App\CreditOrder;
use \App\Http\Controllers\Controller;
//Service
use \App\modules\CreditRequest\Model\Service\CreditRequestForm\MainApplicant as MainApplicantServiceCreditRequestForm;

//Collection
use \App\modules\CreditRequest\Collection\CreditRequest as CollectionCreditRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Request;
class MyCreditRequest extends Controller {
    
    public $layout = 'core/layout/frontend.php';
    public $entityCreditRequest;
    public $entity;
    
    //Metas
    public $meta_title="credicom Kreditanfrage - Sofortkredite ab 1.000,-&euro; bis 300.000,-&euro;";
    public $meta_description="credicom Kreditanfrage - Sofortkredite ab 1.000,-&euro; bis 300.000,-&euro;";
    public $meta_keywords="";

    
    function replyAction() {
        $this->app->render('reply.php'); 
        exit;        
    }
    
    public function getReplyTypeAction() {

       $this->entity = new \App\Http\Model\Entity\Form\Credit();
        $this->entity->load();

     //load entity data from storage

        //check if form is completed, redirect it if it's not
        if(!$this->entity->getCompleted()) {
            header('Location: /');
            exit;
        }

        //force user browser to forget user data
        $this->entity->deleteCookie();

        $creditFormUrls = [
            'ajaxSubmit' => $GLOBALS['file_root'] . 'kreditanfrage-post-edit.ajax',
        ];

      $form = new \App\Http\Render\Block\CreditForm\Income\BankData([
            'entity' => $this->entity,
        ]);

       $collectionCreditRequest = CollectionCreditRequest::getInstance();
        $entityCreditRequest = $collectionCreditRequest->emptyLoad();


        $mainApplicantServiceCreditRequestForm = MainApplicantServiceCreditRequestForm::getInstance();
        $mainApplicantServiceCreditRequestForm->setCreditRequest($entityCreditRequest)->setFormEntity($this->entity);

       $reply = $mainApplicantServiceCreditRequestForm->processDataForm();

        $result = [

            'menu'=>'',
            'data_meta'=>null,
            'creditRequest' => $reply['CreditRequest'],
            'form'=>$form,
            'creditFormUrls'=>$creditFormUrls,'availableRecipients'=>$reply['reply']->availableRecipients
        ];
        return view($reply['reply']->template, $result);


      /* $this->view->appendData(array(
            'form' => $form,
            'creditFormUrls' => $creditFormUrls                
        ));*/
        
       // $html = $reply->render();
        
       // die($html);
    }


    public function kreditanfrageErfolgreich(){
        $this->entity = new \App\Http\Model\Entity\Form\Credit();
        $this->entity->load();
        dd('$this->entity 81',$this->entity);
        $daten=array_merge(session('request-einkommen'),session('request-persoenliche-angaben'),session('request-wunschbetrag'));
        $this->entity=$daten;
        dump('$this->entity',$this->entity);
       //$collectionCreditRequest = CollectionCreditRequest::getInstance();
      //  $entityCreditRequest = $collectionCreditRequest->emptyLoad();
      $entityCreditRequest = new CreditOrder();
        $mainApplicantServiceCreditRequestForm = MainApplicantServiceCreditRequestForm::getInstance();
       $mainApplicantServiceCreditRequestForm->setCreditRequest($entityCreditRequest)->setFormEntity($this->entity);
       // dump(session()->all());

        //dump('$daten',$daten);
dump('MyCreditRequest');
        $reply = $mainApplicantServiceCreditRequestForm->processDataForm();

        $creditFormUrls = [
            'ajaxSubmit' => 'kreditanfrage-post-edit.ajax',
        ];
        $form = new \App\Http\Render\Block\CreditForm\Income\BankData([
            'entity' => null,
        ]);
        $creditRequest=[];

        $result = [

            'menu'=>'',
            'data_meta'=>null,
        'creditRequest' => $reply['CreditRequest'],
            'form'=>$form,
            'creditFormUrls'=>$creditFormUrls,'availableRecipients'=>$reply['reply']->availableRecipients
        ];
        return view($reply['reply']->template, $result);
    }


}

