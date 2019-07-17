<?php

namespace App\modules\CreditRequest\Model\Service\CreditRequestForm;

use \App\modules\Core\Model\Traits\Singleton;

use \App\modules\CreditRequest\Model\Service\CreditRequestForm\Base as BaseCreditRequestFormService;
use \App\modules\Auxmoney\Model\Auxmoney as Auxmoney;
use Log;

class CoApplicant extends BaseCreditRequestFormService {
    use Singleton;

    public $ReplyTypeConfig;
	public $notwdvma;

	public function __construct($manual=null){
		if(isset($manual)){
			$this->notwdvma=$manual;
		}
		
		$this->init($manual);
	}
	
    public function init($manual=null) {

		if(isset($manual)){
			$this->notwdvma=$manual;
		}
		
        $this->isMainApplicant = false;
		//$notwdvma=(isset($_SESSION['coapplican_not_wdvma'])) ? $_SESSION['coapplican_not_wdvma'] : null;
		//Log::info('session: '.date("Y-m-d H:i:s").print_r($_SESSION,true));
        $this->ReplyTypeConfig= [
            self::REPLY_PAGE_TYPE_GREEN => [
                'getReplyBlock' => (isset($this->notwdvma) && $this->notwdvma=='manual_mail') ? '\App\modules\CreditRequest\Block\Reply\CoApplicant\Green' : '\App\modules\CreditRequest\Block\Reply\CoApplicant\Green',
                'sendEmail' => false,
                'sendInfoNotification' => '\App\modules\CreditRequest\Block\Mail\FormReplyInfoNotification\CoApplicant\Green',
                'changeStatus' => (isset($this->notwdvma) && $this->notwdvma=='manual_mail') ? false : self::STATUS_MAPPER_FOR_REPLY_PAGE_TYPES[self::REPLY_PAGE_TYPE_GREEN]
            ],
            self::REPLY_PAGE_TYPE_GREEN_AUXMONEY => [
                'getReplyBlock' => ( isset($this->notwdvma) && $this->notwdvma=='manual_mail') ? '\App\modules\CreditRequest\Block\Reply\CoApplicant\Green' : '\App\modules\CreditRequest\Block\Reply\CoApplicant\GreenAuxmoney',
                'sendEmail' => (isset($this->notwdvma) && $this->notwdvma=='manual_mail') ? false : '\App\modules\CreditRequest\Block\Mail\FormReply\CoApplicant\GreenAuxmoney',
                'sendInfoNotification' => '\App\modules\CreditRequest\Block\Mail\FormReplyInfoNotification\CoApplicant\GreenAuxmoney',
                'changeStatus' => false
            ],
            self::REPLY_PAGE_TYPE_YELLOW => [
                'getReplyBlock' => ( isset($this->notwdvma) && $this->notwdvma=='manual_mail') ? '\App\modules\CreditRequest\Block\Reply\CoApplicant\Green' : '\App\modules\CreditRequest\Block\Reply\CoApplicant\Yellow',
                'sendEmail' => (isset($this->notwdvma) && $this->notwdvma=='manual_mail') ? false : '\App\modules\CreditRequest\Block\Mail\FormReply\CoApplicant\Yellow',
                'sendInfoNotification' => false,
                'changeStatus' => false
            ],
            self::REPLY_PAGE_TYPE_RED => [
                'getReplyBlock' => (isset($this->notwdvma) && $this->notwdvma=='manual_mail') ? '\App\modules\CreditRequest\Block\Reply\CoApplicant\Green' : '\App\modules\CreditRequest\Block\Reply\CoApplicant\Red',
                'sendEmail' => (isset($this->notwdvma) && $this->notwdvma=='manual_mail') ? false : '\App\modules\CreditRequest\Block\Mail\FormReply\CoApplicant\Red',
                'sendInfoNotification' => false,
                'changeStatus' => (isset($this->notwdvma) && $this->notwdvma=='manual_mail') ? false : self::STATUS_MAPPER_FOR_REPLY_PAGE_TYPES[self::REPLY_PAGE_TYPE_RED]
            ],
            self::REPLY_PAGE_TYPE_ZANDER_EXPORT => [
                'getReplyBlock' => (isset($this->notwdvma) && $this->notwdvma=='manual_mail') ? '\App\modules\CreditRequest\Block\Reply\CoApplicant\Green' : '\App\modules\CreditRequest\Block\Reply\CoApplicant\Zander',
                'sendEmail' => (isset($this->notwdvma) && $this->notwdvma=='manual_mail') ? false : '\App\modules\CreditRequest\Block\Mail\FormReply\CoApplicant\Zander',
                'sendInfoNotification' => false,
                'changeStatus' => (isset($this->notwdvma) && $this->notwdvma=='manual_mail') ? false : self::STATUS_MAPPER_FOR_REPLY_PAGE_TYPES[self::REPLY_PAGE_TYPE_ZANDER_EXPORT]
            ],
        ];
    }
    
    public function saveFormDataToAdditionalTables($formEntity) {
        return null;
    }
    
    public function setFormDataToDbEntity($formEntity, $entityCreditRequest, $additionalData = null) {
        //$at_dat1 = $formEntity["coapplicant_at_jahr"] . '-' . $formEntity["coapplicant_at_monat"] . '-' . $formEntity["coapplicant_at_tag"];
        $at_dat1 = (isset($formEntity["coapplicant_at_jahr"]) && $formEntity["coapplicant_at_jahr"]!=="") ? $formEntity["coapplicant_at_jahr"] . '-' . $formEntity["coapplicant_at_monat"] . '-' . $formEntity["coapplicant_at_tag"] : date('Y-m-d', strtotime(0000-00-00));
        if($formEntity["coapplicant_beruf"] == self::GOOD_PROFESSIONS['Rentner']) {// coapplicant_beruf
            $arbeitgeber1 = 'DRV';
            $arbeitgeber_plz1 = '10707';
            $arbeitgeber_ort1 = 'Berlin';
            $at_dat1 = '2015-01-01';            
        } elseif(in_array($formEntity["coapplicant_beruf"], self::VERY_BAD_PROFESSIONS)) { // coapplicant_beruf
            $arbeitgeber1 = '';
            $arbeitgeber_plz1 = '';
            $arbeitgeber_ort1 = '';
        } else {
            $arbeitgeber1 = $formEntity["coapplicant_arbeitgeber"];//coapplicant_arbeitgeber
            $arbeitgeber_plz1 = $formEntity["coapplicant_arbeitgeber_plz"];//coapplicant_arbeitgeber_plz
            $arbeitgeber_ort1 = $formEntity["coapplicant_arbeitgeber_ort"];//coapplicant_arbeitgeber_ort
        }
        /* $enstr=(preg_replace('/ [\d,.]+/s', '', $formEntity['coapplicant_str']));

        $formEntity['coapplicant_str_nr'] = str_replace(" ","",str_replace(explode(" ", $enstr), '', $formEntity['coapplicant_str']));
        $formEntity['coapplicant_str']=$enstr; */

        $entityCreditRequest->setData([
            'masteller' => 1, // coapplicant_enabled 
            'anr1' => $formEntity['coapplicant_anrede'],
            'vorname1' => strip_tags(ucfirst($formEntity["coapplicant_vorname"])),
            'nachname1' => strip_tags(ucfirst($formEntity["coapplicant_nachname"])),
            'gesamtbetrachtung' => ($formEntity['coapplicant_same_household']==2 ) ? 1 : 0 ,
            'str1' => ($formEntity["coapplicant_same_household"] == 1) ? strip_tags(ucfirst($formEntity["coapplicant_str"])) : $entityCreditRequest['str'],
            'str_nr1' => ($formEntity["coapplicant_same_household"] == 1) ? strip_tags($formEntity["coapplicant_str_nr"]) : $entityCreditRequest['str_nr'],
            'plz1' => ($formEntity["coapplicant_same_household"] == 1) ? strip_tags($formEntity["coapplicant_plz"]) : $entityCreditRequest['plz'],
            'ort1' => ($formEntity["coapplicant_same_household"] == 1) ? strip_tags(ucfirst($formEntity["coapplicant_ort"])) : $entityCreditRequest['ort'],
            'wohnhaft_seit1' => (isset($formEntity["coapplicant_resident_since_year"])) ? $formEntity["coapplicant_resident_since_year"] . '-' . $formEntity["coapplicant_resident_since_month"] . '-' . $formEntity["coapplicant_resident_since_day"] : date('Y-m-d', strtotime(0000-00-00)),
            'gebdat1' => $formEntity["coapplicant_geb_jahr"] . '-' . $formEntity["coapplicant_geb_monat"] . '-' . $formEntity["coapplicant_geb_tag"],
            'land1' => 'DE',
            'geb_ort1' => strip_tags(ucfirst($formEntity["coapplicant_geb_ort"])),
            'staat1' => $formEntity['coapplicant_staat'],
            'famstand1' => $formEntity['coapplicant_famstand'],
            'beruf1' => $formEntity['coapplicant_beruf'],
            'arbeitgeber1' => strip_tags(ucfirst($arbeitgeber1)),
            'arbeitgeber_plz1' => strip_tags($arbeitgeber_plz1),
            'arbeitgeber_ort1' => strip_tags(ucfirst($arbeitgeber_ort1)),
            'anstellung1' => $at_dat1,
            'arbeit_befristet_date1' => ($formEntity["coapplicant_befristet"] == 1) ? ($formEntity["coapplicant_unb_jahr"] . '-' . $formEntity["coapplicant_unb_monat"] . '-' . $formEntity["coapplicant_unb_tag"]) : date('Y-m-d', strtotime(0000-00-00)),
            'arbeit_befristet1' => ($formEntity["coapplicant_befristet"] == 1) ? 1 : 2,
            'netto1' => strip_tags($formEntity["coapplicant_netto"]),
            'anstellung_als1' => strip_tags($formEntity["coapplicant_anstellung_als"]),
            'nebeneinkommen1' => ($formEntity["coapplicant_additional_revenue_enabled"] == 1) ? 1 : 0,
            'nebeneinkommen_mtl1' => ($formEntity["coapplicant_additional_revenue_enabled"] == 1) ? strip_tags($formEntity["coapplicant_additional_revenue"]) : 0,
			'unterhalt_enabled1' => ($formEntity["coapplicant_unterhalt_enabled"] == 1) ? strip_tags($formEntity["coapplicant_unterhalt_enabled"]) : 0,
            'unterhalt1' =>  ($formEntity["coapplicant_unterhalt_enabled"] == 1) ? strip_tags($formEntity["coapplicant_unterhalt"]) : 0,
//            'status_intern' => $status_intern,  // will be set in $this->getReplyType($entityCreditRequest) 
'coapplicant_bank_account_type'=>(isset($formEntity["coapplicant_bank_account_type"])) ? $formEntity["coapplicant_bank_account_type"] : null,
'coapplicant_iban'=>(isset($formEntity["coapplicant_iban"])) ? $formEntity["coapplicant_iban"] : null,
'coapplicant_bic'=>(isset($formEntity["coapplicant_bic"])) ? $formEntity["coapplicant_bic"] : null,
'coapplicant_kto'=>(isset($formEntity["coapplicant_kto"])) ? $formEntity["coapplicant_kto"] : null,
'coapplicant_blz'=>(isset($formEntity["coapplicant_blz"])) ? $formEntity["coapplicant_blz"] : null
			
        ]);
        return $entityCreditRequest;
    }
    
// setCoApplicantDataToFormEntity
    public function setDataToFormEntity($formEntity, $entityCreditRequest) {       
        $formEntity->setData([
            'coapplicant_anrede' => $entityCreditRequest['anr1'],
            'coapplicant_vorname' => $entityCreditRequest['vorname1'],
            'coapplicant_nachname' => $entityCreditRequest['nachname1'],
            'coapplicant_same_household' => $entityCreditRequest['gesamtbetrachtung'],                    
            'coapplicant_str' => $entityCreditRequest['str1'],
            'coapplicant_str_nr' => $entityCreditRequest['str_nr1'],
            'coapplicant_plz' => $entityCreditRequest['plz1'],
            'coapplicant_ort' => $entityCreditRequest['ort1'],
            'coapplicant_geb_ort' => $entityCreditRequest['geb_ort1'],
            'coapplicant_staat' => $entityCreditRequest['staat1'],
            'coapplicant_famstand' => $entityCreditRequest['famstand1'],
            'coapplicant_beruf' => $entityCreditRequest['beruf1'],                    
            'coapplicant_arbeitgeber' => $entityCreditRequest['arbeitgeber1'],
            'coapplicant_arbeitgeber_plz' => $entityCreditRequest['arbeitgeber_plz1'],
            'coapplicant_arbeitgeber_ort' => $entityCreditRequest['arbeitgeber_ort1'],                    
            'coapplicant_befristet' => ($entityCreditRequest['arbeit_befristet1'] == 1) ? 1 : '',                    
            'coapplicant_netto' => $entityCreditRequest['netto1'],
            'coapplicant_anstellung_als' => $entityCreditRequest['anstellung_als1'],  
            'coapplicant_additional_revenue_enabled' => ($entityCreditRequest['nebeneinkommen1'] == 2) ? 1 : '',  
            'coapplicant_additional_revenue' => ($entityCreditRequest['nebeneinkommen1'] == 0) ? '' : $entityCreditRequest['nebeneinkommen_mtl1'] ,
            'coapplicant_unterhalt_enabled' =>  ($entityCreditRequest['unterhalt'] == 0) ? '' : 1 ,   
            'coapplicant_unterhalt' => $entityCreditRequest['unterhalt1'],
        ]);

        list($formEntity["coapplicant_geb_jahr"], $formEntity["coapplicant_geb_monat"], $formEntity["coapplicant_geb_tag"]) = explode('-',$entityCreditRequest['gebdat1']);                
        list($formEntity["coapplicant_at_jahr"], $formEntity["coapplicant_at_monat"], $formEntity["coapplicant_at_tag"]) = explode('-',$entityCreditRequest['anstellung1']);
        list($formEntity["coapplicant_unb_jahr"], $formEntity["coapplicant_unb_monat"], $formEntity["coapplicant_unb_tag"]) = explode('-',$entityCreditRequest['arbeit_befristet_date1']);         
        list($formEntity["coapplicant_resident_since_year"], $formEntity["coapplicant_resident_since_month"], $formEntity["coapplicant_resident_since_day"]) = explode('-',$entityCreditRequest['wohnhaft_seit1']); 
        
        return $formEntity;
    }
    
}






