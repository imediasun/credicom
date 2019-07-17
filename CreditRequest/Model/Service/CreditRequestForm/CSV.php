<?php

namespace App\modules\CreditRequest\Model\Service\CreditRequestForm;

use \App\modules\Core\Model\Traits\Singleton;

//Model
use \App\modules\CreditRequest\Model\Service\CreditRequestForm\Base as BaseCreditRequestFormService;
use \App\modules\Auxmoney\Model\Auxmoney as Auxmoney;
use \App\modules\CreditRequest\Model\CreditRequest as CreditRequestModel;

//Collection
use \App\modules\Client\Collection\Client as CollectionClient;
use \App\modules\CreditRequest\Collection\CreditRequest as CollectionCreditRequest;
use \App\modules\CreditRequest\Model\Service\CreditRequestForm\CoApplicant;
use Log;
use App\Client;
use DB;
use App\CreditOrder;
class CSV extends BaseCreditRequestFormService {
    use Singleton;
    
    const STATUSES_FOR_CHECK_DUPLICATE = [
        CreditRequestModel::STATUS_NV_NEGATIVE_SCHUFA,  // 58
        CreditRequestModel::STATUS_NV_SV,  // 10
        CreditRequestModel::STATUS_DOPPLER,  // 31
    ];    

    public $ReplyTypeConfig;



    function user_add($eingabe) {
        $eingabe=strip_tags($eingabe);
        return $eingabe;
    }

    public function init() {
        $this->isMainApplicant = true;
        $this->ReplyTypeConfig = [
            self::REPLY_PAGE_TYPE_GREEN => [

                'getReplyBlock' => '\App\modules\CreditRequest\Block\Reply\MainApplicant\Green',
                'sendEmail' => '\App\modules\CreditRequest\Block\Mail\FormReply\MainApplicant\Green',
                'sendInfoNotification' => false,
                'changeStatus' => self::STATUS_MAPPER_FOR_REPLY_PAGE_TYPES[self::REPLY_PAGE_TYPE_GREEN]
            ],
            self::REPLY_PAGE_TYPE_GREEN_AUXMONEY => [

                'getReplyBlock' => '\App\modules\CreditRequest\Block\Reply\MainApplicant\GreenAuxmoney',
                'sendEmail' => '\App\modules\CreditRequest\Block\Mail\FormReply\MainApplicant\GreenAuxmoney',
                'sendInfoNotification' => '\CreditRequest\Block\Mail\FormReplyInfoNotification\MainApplicant\GreenAuxmoney',
                'changeStatus' => false
            ],
            self::REPLY_PAGE_TYPE_YELLOW => [

                'getReplyBlock' => '\App\modules\CreditRequest\Block\Reply\MainApplicant\Yellow',
                'sendEmail' => '\App\modules\CreditRequest\Block\Mail\FormReply\MainApplicant\Yellow',
                'sendInfoNotification' => false,
                'changeStatus' => false
            ],
            self::REPLY_PAGE_TYPE_RED => [

                'getReplyBlock' => '\App\modules\CreditRequest\Block\Reply\MainApplicant\Red',
                'sendEmail' => '\App\modules\CreditRequest\Block\Mail\FormReply\MainApplicant\Red',
                'sendInfoNotification' => false,
                'changeStatus' => self::STATUS_MAPPER_FOR_REPLY_PAGE_TYPES[self::REPLY_PAGE_TYPE_RED]
            ],
            self::REPLY_PAGE_TYPE_DUPLICATE => [

                'getReplyBlock' => '\App\modules\CreditRequest\Block\Reply\MainApplicant\Green', // as in Green
                'sendEmail' => '\App\modules\CreditRequest\Block\Mail\FormReply\MainApplicant\Green', // as in Green
                'sendInfoNotification' => false, // as in Green
                'changeStatus' => self::STATUS_MAPPER_FOR_REPLY_PAGE_TYPES[self::REPLY_PAGE_TYPE_DUPLICATE
                ]
            ],
            self::REPLY_PAGE_TYPE_ZANDER_EXPORT => [
                'getReplyBlock' => '\App\modules\CreditRequest\Block\Reply\MainApplicant\Zander',
                'sendEmail' => '\App\modules\CreditRequest\Block\Mail\FormReply\MainApplicant\Zander',
                'sendInfoNotification' => false,
                'changeStatus' => self::STATUS_MAPPER_FOR_REPLY_PAGE_TYPES[self::REPLY_PAGE_TYPE_ZANDER_EXPORT]
            ],
        ];
		
		      $this->CoApplicantReplyTypeConfig= [
			  
			     self::REPLY_PAGE_TYPE_GREEN => [

                'getReplyBlock' => '\App\modules\CreditRequest\Block\Reply\CoApplicant\MainFormGreen',
                'sendEmail' => '\App\modules\CreditRequest\Block\Mail\FormReply\CoApplicant\MainFormGreen',
                'sendInfoNotification' => false,
                'changeStatus' => self::STATUS_MAPPER_FOR_REPLY_PAGE_TYPES[self::REPLY_PAGE_TYPE_GREEN]
            ],
            self::REPLY_PAGE_TYPE_GREEN_AUXMONEY => [

                'getReplyBlock' => '\App\modules\CreditRequest\Block\Reply\CoApplicant\MainFormGreenAuxmoney',
                'sendEmail' => '\App\modules\CreditRequest\Block\Mail\FormReply\CoApplicant\MainFormGreenAuxmoney',
                'sendInfoNotification' => '\CreditRequest\Block\Mail\FormReplyInfoNotification\MainApplicant\GreenAuxmoney',
                'changeStatus' => false
            ],
            self::REPLY_PAGE_TYPE_YELLOW => [

                'getReplyBlock' => '\App\modules\CreditRequest\Block\Reply\CoApplicant\MainFormYellow',
                'sendEmail' => '\App\modules\CreditRequest\Block\Mail\FormReply\CoApplicant\MainFormYellow',
                'sendInfoNotification' => false,
                'changeStatus' => false
            ],
            self::REPLY_PAGE_TYPE_RED => [

                'getReplyBlock' => '\App\modules\CreditRequest\Block\Reply\CoApplicant\MainFormRed',
                'sendEmail' => '\App\modules\CreditRequest\Block\Mail\FormReply\CoApplicant\MainFormRed',
                'sendInfoNotification' => false,
                'changeStatus' => self::STATUS_MAPPER_FOR_REPLY_PAGE_TYPES[self::REPLY_PAGE_TYPE_RED]
            ],
            self::REPLY_PAGE_TYPE_DUPLICATE => [

                'getReplyBlock' => '\App\modules\CreditRequest\Block\Reply\CoApplicant\MainFormGreen', // as in Green
                'sendEmail' => '\App\modules\CreditRequest\Block\Mail\FormReply\CoApplicant\MainFormGreen', // as in Green
                'sendInfoNotification' => false, // as in Green
                'changeStatus' => self::STATUS_MAPPER_FOR_REPLY_PAGE_TYPES[self::REPLY_PAGE_TYPE_DUPLICATE
                ]
            ],
            self::REPLY_PAGE_TYPE_ZANDER_EXPORT => [
                'getReplyBlock' => '\App\modules\CreditRequest\Block\Reply\CoApplicant\MainFormZander',
                'sendEmail' => '\App\modules\CreditRequest\Block\Mail\FormReply\CoApplicant\MainFormZander',
                'sendInfoNotification' => false,
                'changeStatus' => self::STATUS_MAPPER_FOR_REPLY_PAGE_TYPES[self::REPLY_PAGE_TYPE_ZANDER_EXPORT]
            ],
			  

        ];
    }
    
    public function setDataToFormEntity($formEntity, $entityCreditRequest) {}
    
    public function saveFormDataToAdditionalTables($formEntity) {
        $additionalData = [];
        
        $clientId = $this->saveClientFormDataToDB($formEntity);

        $duplicate = $this->checkClientDuplicate($clientId, $formEntity);
        
        $additionalData['client'] = [
            'id' => $clientId,
            'duplicate' => $duplicate,
        ];

        return $additionalData;
    }
    
    public function setFormDataToDbEntity($formEntity, $entityCreditRequest/*функция записи данных*/, $additionalData = null) {
		
		//dump($formEntity);
        $at_dat = (isset($formEntity["at_jahr"]) && $formEntity["at_jahr"]!=="") ? $formEntity["at_jahr"] . '-' . $formEntity["at_monat"] . '-' . $formEntity["at_tag"] : date('Y-m-d', strtotime(0000-00-00));
        if($formEntity["beruf"] == self::GOOD_PROFESSIONS['Rentner']) {
            $arbeitgeber = 'DRV';
            $arbeitgeber_plz = '10707';
            $arbeitgeber_ort = 'Berlin';
            $at_dat = '2015-01-01';
        } elseif(in_array($formEntity["beruf"], self::VERY_BAD_PROFESSIONS)) {
            $arbeitgeber = '';
            $arbeitgeber_plz = '';
            $arbeitgeber_ort = '';
        } else {
            $arbeitgeber = $formEntity["arbeitgeber"];
            $arbeitgeber_plz = $formEntity["arbeitgeber_plz"];
            $arbeitgeber_ort = $formEntity["arbeitgeber_ort"];
        }
        

        $kto = $blz = $iban = $bic = '';
		//Log::info('$iban2: '.date("Y-m-d H:i:s").$formEntity["iban"]);
        if(!in_array($formEntity["beruf"], self::GOOD_PROFESSIONS)) {  // !GOOD_PROFESSIONS
            if($formEntity["bank_account_type"] === "ibanbic") {
                $iban = (isset($formEntity["iban"])) ? $formEntity["iban"] : null;
                $bic = (isset($formEntity["bic"])) ? $formEntity["bic"] : null;
            }
            else {
                $kto = $formEntity["kto"];
                $blz = $formEntity["blz"];
            }
        }
		else{
			$iban = $formEntity["iban"];
		}
		//Log::info('$iban3: '.date("Y-m-d H:i:s").$iban);
		
		


//запись данных
        /* $enstr=(preg_replace('/ [\d,.]+/s', '', $formEntity['str']));
		$formEntity['str_nr'] = str_replace(" ","",str_replace(explode(" ", $enstr), '', $formEntity['str']));
        $formEntity['str']=$enstr; */
		Log::info('good in CSV: '.date("Y-m-d H:i:s").'vzweck=>'.$formEntity['intended_use']);
		
        $entityCreditRequest->setData([
            'kid' => $additionalData['client']['id'],
            'kreditbetrag' => strip_tags($formEntity['betrag']),
            'mtl_rate' => strip_tags($formEntity['rate']),
            'vzweck' => $formEntity['intended_use'],//
            'anr' => $formEntity['anrede'],
            'vorname' => strip_tags(ucfirst($formEntity['vorname'])),
            'nachname' => strip_tags(ucfirst($formEntity['nachname'])),
            'str' => strip_tags(ucfirst($formEntity['str'])),
            'str_nr' => strip_tags(ucfirst($formEntity['str_nr'])),
            'plz' => strip_tags($formEntity['plz']),
            'ort' => strip_tags(ucfirst($formEntity['ort'])),
            'land' => 'DE',
            'handy' => strip_tags($formEntity['handy']),
            'telefon' => strip_tags($formEntity['tel']),
            'email' => strip_tags($formEntity['mail']),
            'erreichbarkeit' => strip_tags($formEntity['preferredCallTime']),
            'gebdat' => $formEntity["geb_jahr"] . '-' . $formEntity["geb_monat"] . '-' . $formEntity["geb_tag"],
            'geb_ort' => strip_tags(ucfirst($formEntity['geb_ort'])),
            'staat' => $formEntity['staat'],
            'famstand' => $formEntity['famstand'],
            'kinder' => strip_tags($formEntity['kinder']),

//wohnsituation|resident_type = [
//    1 =>  'zur Miete',
//    2 =>  'im Eigenheim',
//    //3 =>  'In einer Eigentumswohnung',
//    4 =>  'bei Eltern'
//];
            'wohnsituation' => (isset($formEntity['wohnsituation'])) ? $formEntity['wohnsituation'] : ((isset($formEntity['resident_type'])) ? strip_tags($formEntity['resident_type']) : null),
            'wohnhaft_seit' => $formEntity["resident_since_year"] . '-' . $formEntity["resident_since_month"] . '-' . $formEntity["resident_since_day"],
            'miete' => (isset($formEntity['resident_type']) && $formEntity['resident_type'] == 1) ? strip_tags($formEntity['rental_fee']) : 0,
            'eigentum' => (isset($formEntity['resident_type']) && $formEntity['resident_type'] == 2 ) ? 1 : 0, // 2:1
            'eigentum_typ' => (isset($formEntity['resident_type']) && $formEntity['resident_type'] == 2) ? strip_tags($formEntity['resident_owned_propery_type']) : 0,
            'eigentum_wert' => (isset($formEntity['resident_type']) && $formEntity['resident_type'] == 2) ? strip_tags($formEntity['resident_owned_total_value_approx']) : 0,
            'eigentum_belastung' => (isset($formEntity['resident_type']) && $formEntity['resident_type'] == 2) ? strip_tags($formEntity['resident_owned_total_load_approx']) : 0,
            'eigentum_belastung_mtl' => (isset($formEntity['resident_type']) && $formEntity['resident_type'] == 2) ? strip_tags($formEntity['resident_owned_property_approx']) : 0,
            'eigentum_miete' => (isset($formEntity['resident_type']) && $formEntity['resident_type'] == 2) ? strip_tags($formEntity['resident_owned_property_rental_income']) : 0,
            'eigentum_zusatz' => ($formEntity['own_residential_property'] == 1) ? 1 : 0, // 2:1
            'eigentum_typ_zusatz' => ($formEntity['own_residential_property'] == 1 && isset($formEntity['propery_type']) ) ? strip_tags($formEntity['propery_type']) : 0,
            'eigentum_wert_zusatz' => ($formEntity['own_residential_property'] == 1 && isset($formEntity['total_value_approx'])) ? strip_tags($formEntity['total_value_approx']) : 0,
            'eigentum_belastung_zusatz' => ($formEntity['own_residential_property'] == 1 && isset($formEntity['total_load_approx'])) ? strip_tags($formEntity['total_load_approx']) : 0,
            'eigentum_belastung_mtl_zusatz' => ($formEntity['own_residential_property'] == 1 && isset($formEntity['property_approx'])) ? strip_tags($formEntity['property_approx']) : 0,
            'eigentum_miete_zusatz' => ($formEntity['own_residential_property'] == 1 && isset($formEntity['property_rental_income'])) ? strip_tags($formEntity['property_rental_income']) : 0,
            'beruf' => strip_tags($formEntity['beruf']),
            'arbeitgeber' => strip_tags(ucfirst($arbeitgeber)),
            'arbeitgeber_plz' => strip_tags($arbeitgeber_plz),
            'arbeitgeber_ort' => strip_tags(ucfirst($arbeitgeber_ort)),
            'anstellung' => strip_tags($at_dat),
            'arbeit_befristet' => ($formEntity["befristet"] == 1) ? 1 : 0,//1:2
            'arbeit_befristet_date' => ($formEntity["befristet"] == 1) ? ($formEntity["unb_jahr"] . '-' . $formEntity["unb_monat"] . '-' . $formEntity["unb_tag"]) : date('Y-m-d', strtotime(0000-00-00)),
            'netto' => strip_tags($formEntity['netto']),
            'anstellung_als' => strip_tags($formEntity['anstellung_als']),
            'nebeneinkommen' => ($formEntity["additional_revenue_enabled"] == 1) ? 1 : 0,//2:1
            'nebeneinkommen_mtl' => ($formEntity["additional_revenue_enabled"] == 1) ? strip_tags($formEntity["additional_revenue"]) : 0,
            'unterhalt_enabled' => ($formEntity["unterhalt_enabled"] == 1) ? strip_tags($formEntity["unterhalt_enabled"]) : 0,
			'unterhalt' => ($formEntity["unterhalt_enabled"] == 1 && isset($formEntity["unterhalt"])) ? strip_tags($formEntity["unterhalt"]) : 0,
            'kto' => strip_tags($kto),
            'blz' => strip_tags($blz),
            'iban' => strip_tags($iban),
            'bic' => strip_tags($bic),
            'bank_account_type' => $formEntity['bank_account_type'],
            'masteller' => (int)$formEntity['coapplicant_enabled'],
            'date' => date("Y-m-d H:i:s",time()),
            'date_save' => time(),
            'ip' => (isset($formEntity['ip'])) ? $formEntity['ip'] : '0.0.0.0',//$_SERVER["REMOTE_ADDR"]
            'session_id' => session_id(),

            'kreditkarte' => $formEntity['kreditkarte'],
            'partner_id' => (isset($formEntity['partner_id'])) ? $formEntity['partner_id'] : 1400,
            'subid' => '',
            'code' => $this->generateCode(40),
//            'gclid' => $gclid1, undefined variable $gclid1
//            'status_intern' => $status_intern,  // will be set in $this->getReplyType($entityCreditRequest)
        'status_intern'=>0

        ]);
		
		if(isset($_SESSION['partner_id'])) {
			$_SESSION['partner_id']=null;
		} 
		Log::info('good in CSV2: '.date("Y-m-d H:i:s").'request=>'.print_r($entityCreditRequest,true)); 
        if($formEntity['coapplicant_enabled'] == 1) {
			
            $entityCreditRequest = $this->setCoApplicantFormDataToDbEntity($formEntity, $entityCreditRequest);
        }
/*  foreach($entityCreditRequest as $key=>$value){
	$entityCreditRequest->$key=mb_convert_encoding($value, 'UTF-8', mb_detect_encoding($value, 'UTF-8, ISO-8859-1', true));
}  */

$creditRequestNorm=\App\modules\CreditRequest\Model\Service::getInstance();
Log::info('good in CSV3: '.date("Y-m-d H:i:s"));
$entityCreditRequest=$creditRequestNorm->normoliseEncoding($entityCreditRequest);
Log::info('good in CSV4: '.date("Y-m-d H:i:s"));
        return $entityCreditRequest;
    }
    
    public function setCoApplicantFormDataToDbEntity($formEntity, $entityCreditRequest) {
        //dump('setCoApplicantFormDataToDbEntity');
        //dump($formEntity);
        //dump($entityCreditRequest);
        return \App\modules\CreditRequest\Model\Service\CreditRequestForm\CoApplicant::getInstance()->setFormDataToDbEntity($formEntity, $entityCreditRequest);
    } 

    public function saveClientFormDataToDB($formEntity) {
        //$clientCollection = CollectionClient::getInstance();

      /*  $client = $clientCollection->load(['filter' => [
            'vorname' => [ 'like' => '%' . mysql_real_escape_string(trim(strip_tags($formEntity["vorname"]))) . '%' ],
            'nachname' => [ 'like' => '%' . mysql_real_escape_string(trim(strip_tags($formEntity["nachname"]))) . '%' ],
            'gebdat' => $formEntity["geb_jahr"] . '-' . $formEntity["geb_monat"] . '-' . $formEntity["geb_tag"]
        ]]);*/
        $client=Client::where('vorname','like','%' . trim($this->user_add($formEntity['vorname'])).'%')->where('nachname','like','%'.trim($this->user_add($formEntity['nachname'])).'%')->where('gebdat',$formEntity["geb_jahr"] . '-' . $formEntity["geb_monat"] . '-' . $formEntity["geb_tag"])->first();
        if(!$client) {
            //$client = $clientCollection->emptyLoad();

       // dump($formEntity);
        $clientData=[
            'anr' => $formEntity['anrede'],
            'vorname' => strip_tags(ucfirst($formEntity['vorname'])),
            'nachname' => strip_tags(ucfirst($formEntity['nachname'])),
            'gebdat' => $formEntity["geb_jahr"] . '-' . $formEntity["geb_monat"] . '-' . $formEntity["geb_tag"],
            'famstand' => $formEntity['famstand'],
            'str' => strip_tags(ucfirst($formEntity['str'])),
            'str_nr' => 0,
            'plz' => strip_tags($formEntity['plz']),
            'ort' => strip_tags(ucfirst($formEntity['ort'])),
            'staat' => $formEntity['staat'],
            'telefon' => strip_tags($formEntity['tel']),
            'handy' => strip_tags($formEntity['handy']),
            'email' => strip_tags($formEntity['mail']),
            'status' => '1',
        ];
        //$clientCollection->save($client);
            Client::insert($clientData);
            $lastInsertId=DB::getPdo()->lastInsertId();
            $client['id']=$lastInsertId;
    }

        return $client['id'];
    }    

    public function checkClientDuplicate($clientId, $formEntity) {
        $duplicate = false;

       // $collectionCreditRequest = CollectionCreditRequest::getInstance();
        
//        $lastCreditRequest = $collectionCreditRequest->load([
//            'filter' => [ 'kid' => $clientId ],
//            'sort' => [ 'id' => 'DESC', ]
//        ]);

        $lastCreditRequest = CreditOrder::where('kid',$clientId)->orderBy('id', 'desc')->first();
     
        if($lastCreditRequest){
            $dateInterval = $this->getDateInterval($lastCreditRequest->date);
//dump($dateInterval);
            if($dateInterval->d < 7 && $dateInterval->m == 0 && $dateInterval->y == 0) { 
                $duplicate = $this->checkDuplicateCoApplicantForDuplicateClient($formEntity, $clientId, '7');
            } elseif ($dateInterval->y < 1) {
				
                if(in_array($lastCreditRequest->status_intern, self::STATUSES_FOR_CHECK_DUPLICATE)) {
                    $duplicate = $this->checkDuplicateCoApplicantForDuplicateClient($formEntity, $clientId, '365');
                }          
            }            
        }
       //dump('duplicate',$duplicate);
        return $duplicate;
    }
    
    //If a new CreditRequest contains a coApplicant, then we check the availability of requests not older than $interval ('7 DAY' | '1 YEAR') from the same client with the same coApplicant
    public function checkDuplicateCoApplicantForDuplicateClient($formEntity, $clientId, $interval) {
        $duplicate = false;
     // dump($formEntity['coapplicant_enabled']);
        if(boolval($formEntity['coapplicant_enabled'])) {
           // $collectionCreditRequest = CollectionCreditRequest::getInstance();
            $interval = \Carbon\Carbon::today()->subDays($interval);
           // dump( $interval);
            $creditRequestListForLastWeekWithTheSameCoApplicant = CreditOrder::where('kid',$clientId)->Where('masteller',1)->Where('vorname1',strip_tags(ucfirst($formEntity['coapplicant_vorname'])))->
            Where('nachname1',strip_tags(ucfirst($formEntity['coapplicant_nachname'])))->Where('gebdat1',$formEntity['coapplicant_geb_jahr'] . '-' . $formEntity['coapplicant_geb_monat'] . '-' . $formEntity['coapplicant_geb_tag'])
                ->where('date', '<', \Carbon\Carbon::now())
                ->where('date', '>', $interval)
                ->get();

           //dd($creditRequestListForLastWeekWithTheSameCoApplicant);
            /*$collectionCreditRequest->getList([
                'filter' => [
                    'kid' => $clientId,
                    'date' => ['betweenRaw' => ['DATE_SUB( NOW() , INTERVAL ' . $interval . ')', 'NOW()']],
                    'masteller' => 1,
                    'vorname1' => strip_tags(ucfirst($formEntity['coapplicant_vorname'])),                    
                    'nachname1' => strip_tags(ucfirst($formEntity['coapplicant_nachname'])),  
                    'gebdat1' => $formEntity['coapplicant_geb_jahr'] . '-' . $formEntity['coapplicant_geb_monat'] . '-' . $formEntity['coapplicant_geb_tag']
                ]
            ]);*/
            if(count($creditRequestListForLastWeekWithTheSameCoApplicant)) {
                $duplicate = true;
            }  
        } else {
            $duplicate = true;
        }

       // dump('duplicate',$duplicate);
        return $duplicate;
    }
    
}






