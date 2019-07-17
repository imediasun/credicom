<?php
namespace App\modules\Auxmoney\Controller;

use App\AuxmoneyReply;
use \App\modules\Core\Controller\Base as BaseController;
use Elibyy\TCPDF\TCPDF;
use \App\modules\Auxmoney\Model\Service\Auxmoney as AuxmoneyService;
use \App\modules\Auxmoney\Collection\Auxmoney as AuxmoneyCollection;
use \App\modules\CreditRequest\Collection\CreditRequest as CreditRequestCollection;
use Illuminate\Support\Facades\Response;
class Contract extends BaseController {    
    public $layout = 'core/layout/frontend.php';

    function file_get_contents_utf8($fn) {
        $content = file_get_contents($fn);
        return mb_convert_encoding($content, 'UTF-8',
            mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
    }

    public function viewAction($isMainApplicant, $creditRequestId, $creditRequestCode) {

        $auxmoneyId = AuxmoneyService::getInstance()->getAuxmoneyIdByCreditRequestData($isMainApplicant, $creditRequestId, $creditRequestCode);

        if(!$auxmoneyId) {
            $this->crutchForOldData($creditRequestId, $creditRequestCode);
        }
        
        //$entityAuxmoney = AuxmoneyCollection::getInstance()->load($auxmoneyId);
        $entityAuxmoney = AuxmoneyReply::where('id',$auxmoneyId)->first();
        if($entityAuxmoney->contract) {
            $filename = 'auxmoney-contract-' . pathinfo($entityAuxmoney->contract, PATHINFO_BASENAME);
            
            //header('Content-Type: application/pdf');
            //header('Content-Disposition: inline; filename="' . $filename . '"');
            
            //echo file_get_contents(base_path().$entityAuxmoney->contract);
/*            $response=  Response::make(file_get_contents(base_path().$entityAuxmoney->contract), 200);
            $response->header('Content-Type', 'application/pdf');
            $response->header('Content-Disposition'   , 'attachment; filename="' . $filename . '"');
echo $response;*/
            $file=base_path().$entityAuxmoney->contract;
            //$file='C:\Users\imediasun\Downloads/Startseite.pdf';
            $headers = array(
                'Content-Type: application/pdf',
                'Content-Disposition:attachment; filename="contract.pdf"',
                'Content-Transfer-Encoding:base64',

            );
            return  response()->file($file, $headers);



            exit;
        } else {
            $this->showErrorPage('Contract not found');
        }

        exit;
    }    

    public function crutchForOldData($creditRequestId, $creditRequestCode) { 
       
        $entityCreditRequest = CreditRequestCollection::getInstance()->load([
            'filter' => [
                'id' => $creditRequestId, 
                'code' => $creditRequestCode
            ]
        ]); 
        if(!$entityCreditRequest) {            
            $this->showErrorPage("Oops! It looks like this page doesn't exist.<br>Please check the url.");
        }

        if($entityCreditRequest['auxmoney_vertrag']) {
            $filename = 'auxmoney-contract-' . $entityCreditRequest['id'] . '.pdf';
            
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $filename . '"');
            
            echo base64_decode($entityCreditRequest['auxmoney_vertrag']);
            exit;
        } else {
            $this->showErrorPage('Contract not found');
        }        
    }
    
    public function showErrorPage($text) { 
        $this->view->appendData(array(
            'text' => $text                    
        ));
        $this->app->render('error.php');
        exit;
    }    
    
}
