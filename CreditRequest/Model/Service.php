<?php

namespace App\modules\CreditRequest\Model;

//traits
use App\CreditOrder;
use App\CreditOrdersComment;
use \App\modules\Core\Model\Traits\Singleton;

//models
use \App\modules\Core\Model\Base as BaseModel;
use \App\modules\Acl\Model\Acl;

//collection
use \App\modules\CreditRequest\Collection\CreditRequestNote as CollectionCreditRequestNote;
use \App\modules\CreditRequest\Collection\CreditRequest as CollectionCreditRequest;
use \App\modules\CreditRequest\Collection\CreditRequestStatus as CollectionCreditRequestStatus;
use App\OrdersStatus;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Log;
use Illuminate\Support\Facades\Auth;
class Service extends BaseModel
{
    use Singleton;

	public function normoliseEncoding($creditRequest){
	//var_dump($creditRequest);	
		foreach($creditRequest as $key=>$value ){
		$creditRequest->$key=mb_convert_encoding(trim($value), 'UTF-8', mb_detect_encoding(trim($value), 'UTF-8, ISO-8859-1', true));
		if($creditRequest->eigentumZusatz==null){
			$creditRequest->eigentumZusatz=0;
		}	 
		if($key=='id' && $value==''){
			$creditRequest->$key=null;
		}
		if($key=='svPkvDatum' && $creditRequest->sv_pkv_datum == '' ){
		$creditRequest->sv_pkv_datum=date('Y-m-d', strtotime(0000-00-00));
		}
		if($key=='gesamtbetrachtung' && $value==""){
		$value=0;
		$creditRequest->$key=$value;
		}
		if($key=='anr1' && $value==""){
			$creditRequest->$key=null;
		}
		if($key=='unterhaltEnabled1' && ( $value=="" || $value==null)){
			//var_dump('unterhaltEnabled1');
			$creditRequest->$key="0";
		}
		if($key=='unterhaltEnabled' && ( $value=="" || $value==null)){
			$creditRequest->$key="0";
		}
			if($key=='auxmoneyId' && ($value=="" || $value==null)){
			$creditRequest->$key=null;
		}
		
		if($key=='notificationStep' && ($value=="" || $value==null)){
			$creditRequest->$key=null;
		}
		
		if($key=='coapplicantAuxmoneyId' && ($value=="" || $value==null)){
			$creditRequest->$key=null;
		}
		
		if($key=='createdAt' ){
				$value=date("Y-m-d H:i:s");
				$creditRequest->$key=$value;
			}
			if($key=='updatedAt' ){
				$value=date("Y-m-d H:i:s");
				$creditRequest->$key=$value;
			}
			if($key=='sessionId' && $value==null){
			$value=session()->getId();
				$creditRequest->$key=$value;
			}
			if($key=='arbeitBefristet' && $creditRequest->arbeitBefristet==null){

			$value=0;
				$creditRequest->$key=0;
			}
			if($key=='nebeneinkommen' && $creditRequest->nebeneinkommen==null){

			$value=0;
				$creditRequest->$key=0;
			}
			if($key=='eigentumTyp1' && $creditRequest->eigentumTyp1==""){
				$creditRequest->$key=0;
			}
			if($key=='eigentum' && $creditRequest->eigentum==null){

			$value=0;
				$creditRequest->$key=0;
			}
			
				if($key=='wiedervorlage' && $value==""){
				$value=null;
				$creditRequest->$key=$value;
			}
				if($key=='wiedervorlageUser' && $value==""){
				$value=null;
				$creditRequest->$key=$value;
			}
				if($key=='statusIntern' && $value==null){
				$value=0;
				$creditRequest->$key=$value;
			}
				if($key=='created_old' ){
					var_dump($value);
				$value=null;
				$creditRequest->$key=$value;
			}
			
			
		}

		return $creditRequest;
	}
	
    public function addCreditRequestNote($text, $creditRequest)
    {
        $acl = Acl::getInstance();
        $user = $acl->getUserAdmin();
		$collection = new CreditOrdersComment();
        $entity = $collection->setData([
            'anfrageid' => $creditRequest->id,
            'userid' => ($user) ? $user->id : ((isset(Auth::user()->id)) && Auth::user()->id) ? Auth::user()->id :0,
            'inhalt' => iconv( mb_detect_encoding($text, 'UTF-8, ISO-8859-1'), 'UTF-8',$text),
            'datum' => date("Y-m-d H:i:s"),
            'aktiv' => 1,
        ]);
        $collection->save();
        return $entity;
    }

    public function callStack($stacktrace) {
	Log::info('unknown status error: '.date("Y-m-d H:i:s").str_repeat("=", 50) ."\n");
        $i = 1;
        foreach($stacktrace as $node) {
			if(isset($node['file'])){
				Log::info('unknown error: '.date("Y-m-d H:i:s").'steck=>'."$i. ".basename($node['file']) .":" .$node['function'] ."(" .$node['line'].")\n");
			}
            $i++;
        }
	/////call it
	//$this->callStack(debug_backtrace());
		
    } 
	
    public function changeInternStatus($status, $creditRequest)
    {
		
		
		
		$ValidStatusListForStepper=array(23,24,52,11,74);

        $previousStatus = $creditRequest->status_intern;//getStatusIntern()
        if($status == $previousStatus) return;

        //add credit request note about status change
        $statusForSelect = CreditRequest::getStatusForSelect();
        $previousStatusLabel = isset($statusForSelect[$previousStatus]) ? $statusForSelect[$previousStatus] : 'none';
        $statusLabel = isset($statusForSelect[$status]) ? $statusForSelect[$status] : 'none';

        $this->addCreditRequestNote(
            sprintf('Status ge&auml;ndert: %s -> %s', $previousStatusLabel, $statusLabel),
            $creditRequest
        );

        //change status
        //
		
        if($creditRequest instanceof CreditOrder){
		$creditRequest->status_intern=$status;	
		if(in_array($creditRequest->status_intern,$ValidStatusListForStepper)){
			$creditRequest->notification_step=null;
		}
        $creditRequest->save();
        }else{
			$creditRequest->setStatusIntern($status);
			foreach($creditRequest as $key=>$value){
			if($creditRequest->eigentumZusatz==null){
			$creditRequest->eigentumZusatz=0;
		}	
		
			if($value==''){
				if($key=='anr1'){
				}
			$creditRequest->$key=null;
			}
			if($key=='svPkvDatum' && $value==""){
				$value=date('Y-m-d',strtotime(0000-00-00));
			}
		
			if($key=='gesamtbetrachtung' && $value==""){
				$value=0;
				$creditRequest->$key=$value;
			}
			

		
			
			if($key=='createdAt' ){
				$value=date("Y-m-d H:i:s");
				$creditRequest->$key=$value;
			}
			if($key=='updatedAt' ){
				$value=date("Y-m-d H:i:s");
				$creditRequest->$key=$value;
			}
			if($creditRequest->$key!==null){
				$creditRequest->$key=mb_convert_encoding($value, 'UTF-8', mb_detect_encoding($value, 'UTF-8, ISO-8859-1', true));
			}
			
			
            } 
            CollectionCreditRequest::getInstance()->_save($creditRequest);
        }


        $this->saveCreditRequestStatus($creditRequest, $previousStatus);

    }
    
    public function saveCreditRequestStatus($creditRequest, $previousStatus) { 
       $statusForSelect = CreditRequest::getStatusForSelect();
       $collection=new OrdersStatus();
        $entity = $collection->setData([
            'anfrageid' => $creditRequest->id,
            'userid' => '1',
            'stid_alt' => isset($statusForSelect[$previousStatus]) ? $previousStatus : '0',
            'stid_neu' => $creditRequest->status_intern,
            'datum' => date("Y-m-d H:i:s"),
        ]); 
        $collection->save();
 
        return $entity;
    }
}
