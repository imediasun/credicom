<?php
namespace App\modules\Auxmoney\Model;

//models
use \App\modules\Core\Model\Base as BaseModel;
use \App\modules\CreditRequest\Model\CreditRequest as CreditRequestModel;

//service
use \App\modules\CreditRequest\Model\Service\CreditRequestForm\Base as CreditRequestFormService;

class Auxmoney extends BaseModel {   
    
    const CO_APPLICANT_POSTFIX_FOR_EXTERNAL_ID = 'ca';
    const CONTRACTS_DIR_RELATIVE_PATH = '/files/Auxmoney/contracts/';
    
    const AUXMONEY_RESPONSE_TYPE = [
        'rejected' => CreditRequestFormService::REPLY_PAGE_TYPE_RED,//,
		
		///  
'rejected_with_next_partner' => CreditRequestFormService::REPLY_PAGE_TYPE_RED_AUXMONEY,
		///
		
		
        'rejected_with_error' => CreditRequestFormService::REPLY_PAGE_TYPE_RED,
        'requires_additional_data' => CreditRequestFormService::REPLY_PAGE_TYPE_YELLOW,
        'approved' => CreditRequestFormService::REPLY_PAGE_TYPE_GREEN_AUXMONEY,
    ]; 

    const AUXMONEY_PROGRESS_MAPPER = [
        'qs_check' => [
            'status' => CreditRequestModel::STATUS_AUXMONEY, // 70
            'email' => false,
            'notification' => 'Kunde wurde f&uuml;r die manuelle Pr&uuml;fung ausgew&auml;hlt. Zur Verifizierung werden Kontoums&auml;tze der letzten 3 Monate vom Kunden ben&ouml;tigt, die er &uuml;ber das EKF bereitstellen kann.'
        ],   
        'first_contact_visited'	=> [
            'status' => false,
            'email' => false,
            'notification' => 'Der Kunde hat das Erstkontaktformular auf Auxmoney besucht.'
        ],
        'first_contact_completed' => [
            'status' => false,
            'email' => false,
            'notification' => 'Der Kunde hat das Erstkontaktformular auf Auxmoney ausgef&uuml;llt.'
        ],
        'qs_check_additional_documents' => [
            'status' => false,
            'email' => false,
            'notification' => 'Die Auxmoney-Qualit&auml;tssicherung fordert Kontoums&auml;tze f&uuml;r den Kunden nach, weil diese nicht vollst&auml;ndig waren.'
        ],
        'qs_check_documents_complete' => [
            'status' => false,
            'email' => false,
            'notification' => 'Die Auxmoney-Qualit&auml;tssicherung hat die eingereichten Dokumente gesichtet und als vollst&auml;ndig markiert.'
        ],
        'qs_check_successful' => [
            'status' => false,
            'email' => false,
            'notification' => 'Die QS-Pr&uuml;fung des Kunden war erfolgreich. Der Antrag wurde final genehmigt.'
        ],
        'credit_contract_generated_no_rkv' => [
            'status' => CreditRequestModel::STATUS_AUXMONEY_VERTRAG, // 74
            'email' => true,
            'notification' => 'Der Kreditvertrag wurde erstellt (ohne Restkreditversicherung); Vertrag ist im Push enthalten.'
        ],
        'credit_contract_generated_rkv_3' => [
            'status' => CreditRequestModel::STATUS_AUXMONEY_VERTRAG, // 74
            'email' => true,
            'notification' => 'Der Kreditvertrag wurde erstellt (mit Restkreditversicherung neu); Vertrag ist im Push enthalten.'
        ],
        'ready_for_payout' => [
            'status' => false,
            'email' => false,
            'notification' => 'Alle Freigaben und Vertragsdokumente liegen vor. Die Auszahlung wird nun angewiesen.'
        ],
        'credit_payout' => [
            'status' => CreditRequestModel::STATUS_AUXMONEY_AUSGEZAHLT, // 75
            'email' => false,
            'notification' => 'Der Kredit wurde ausgezahlt'
        ],
        'credit_reversal_bank' => [
            'status' => CreditRequestModel::STATUS_NV_KEIN_KONTAKT, // 16
            'email' => false,
            'notification' => 'Kreditstornierung der Auxmoney-Partnerbank aufgrund von Negativkriterien.'
        ],
        'credit_reversal' => [
            'status' => CreditRequestModel::STATUS_NV_KEIN_KONTAKT, // 16
            'email' => false,
            'notification' => 'Das Kreditprojekt wurde storniert, z.B. auf Kundenwunsch oder nach gescheiterter Zweitpr&uuml;fung.'
        ],
        'eight_week_reversal' => [
            'status' => CreditRequestModel::STATUS_WIDERRUF, // 8
            'email' => false,
            'notification' => 'Storno nach Auszahlung'
        ],
    ];
    
    

}



