<?php

namespace App\modules\Auxmoney\Model\Service\Mapper;

//traits
use \App\modules\Core\Model\Traits\Singleton;

//models
use \App\modules\Core\Model\Base as BaseModel;
use \App\modules\Core\Model\Registry;
use \App\modules\Auxmoney\Model\Auxmoney as Auxmoney;
use App\Http\ArraysClass;
class Request extends BaseModel {
    use Singleton;

    public $options; 
    public $childBenefit;

    public function __construct() {
        $this->init();
    }

    public function init() {
        $this->options = \App\Http\Model\CreditForm\Options::getInstance();
        $config = Registry::getInstance()->getConfig();
        $config = new ArraysClass();
        $config=$config->conf;
        $this->childBenefit = $config['childBenefit'];
    }
    
    
    public function prepearingCoApplicantRequestData($entityCreditRequest, $additionalData = null) {
	 /* 	if($entityCreditRequest instanceof \App\CreditOrder){
		}
		else{
			$entityCreditRequest=\App\CreditOrder::where('id',$entityCreditRequest->id)->first();
			
		} */

		$iban_other='DE19500207000001234567';
		if(empty($entityCreditRequest->iban)) {
            $entityCreditRequest->iban=$iban_other;
        }	
		if(empty($entityCreditRequest->coapplicant_iban)) {
            $entityCreditRequest->coapplicant_iban=$iban_other;
        }	

		$bic_other='FBHLDEFFXXX';
		if(empty($entityCreditRequest->bic)) {
            $entityCreditRequest->bic=$bic_other;
        }	
		if(empty($entityCreditRequest->coapplicant_bic)) {
            $entityCreditRequest->coapplicant_bic=$bic_other;
        }		
				
		
        $netIncome = $this->getNetIncome($entityCreditRequest->netto1);
        $incomeChildBenefits = $this->getCoApplicantIncomeChildBenefits($entityCreditRequest);
        $incomeOther = $this->getIncomeOther($entityCreditRequest->nebeneinkommen_mtl1);
        $incomeTotal = $netIncome + $incomeChildBenefits + $incomeOther;      
                
        // additionalData
        $duration = isset($additionalData['duration']) ? $additionalData['duration'] : $this->getDuration(intval($entityCreditRequest->kreditbetrag));
        $rsv = (isset($additionalData['rsv']) && !$additionalData['rsv']) ? 0 : 3; // Wünscht der Kunde eine Restkreditversicherung? - 3 = "ja" | 0 = "nein"

		 if($entityCreditRequest->arbeitgeber_plz1==""){
			 if($entityCreditRequest->gesamtbetrachtung=="2"){
				 $entityCreditRequest->arbeitgeber_plz1=$entityCreditRequest->plz;
			 }
			 else{
				 //dump($entityCreditRequest->plz1);
				 $entityCreditRequest->arbeitgeber_plz1=$entityCreditRequest->plz1;
			 }
		 }  
		 
		 $creditRequestNorm=\App\modules\CreditRequest\Model\Service::getInstance();
		 $entityCreditRequest=$creditRequestNorm->normoliseEncoding($entityCreditRequest);
		 
        $requestData = [
            'external_id' => $entityCreditRequest->id . Auxmoney::CO_APPLICANT_POSTFIX_FOR_EXTERNAL_ID,
            'loan_asked' => $this->getLoanAsked(intval($entityCreditRequest->kreditbetrag)),
            'duration' => $duration, 
            'category' => 0, // "Sonstiges"
            'rsv' => $rsv,
            'collection_day' => 1, // Wann soll die monatliche Rate eingezogen werden? Am 1. oder am 15. des Monats?       
            'is_accepted_terms_of_service' => true, // Hat der Kunde die Nutzungsbedingungen akzeptiert?
            'is_accepted_solvency_retrieval' => true, // Hat der Kunde dem Abruf von Bonitätsinformationen zugestimmt?            
            'personal_data' => [
                'address' => $entityCreditRequest->anr1, // Anrede (1 = "Herr" | 2 = "Frau")
                'forename' => $entityCreditRequest->vorname1,
                'surname' => $entityCreditRequest->nachname1,
                'family_status' => $this->getFamilyStatus($entityCreditRequest->famstand1),
                'birth_date' => date("Y-m-d", strtotime($entityCreditRequest->gebdat1)),
                'nationality' => $entityCreditRequest->staat1,
                'occupation' => $this->getOccupation($entityCreditRequest->beruf1),
                'has_credit_card' => 0,
                'has_ec_card' => 1,  
                'has_real_estate' => 0,
                'main_earner' => 0,                 
                'housing_type' => $this->getHousingType($entityCreditRequest->wohnsituation),
                'car_owner' => 0
            ],            
            'contact_data' => [
                'living_since' => $this->getCoApplicantLivingSince($entityCreditRequest), 
                'street_name' => $entityCreditRequest->str1,
                'street_number' => $entityCreditRequest->str_nr1,
                'zip_code' => $entityCreditRequest->plz1,
                'city' => $entityCreditRequest->ort1,
                'telephone' => $this->getTelephone($entityCreditRequest),
                'email' => $entityCreditRequest->email
            ],            
            'income' => [
                'total' => $incomeTotal, 
                'net_income' => $netIncome,
                'child_benefits' => $incomeChildBenefits,
                'other' => $incomeOther
            ],            
            'expenses' => $this->getExpenses($entityCreditRequest, true),            
            'employer_data' => [
                'company' => ($entityCreditRequest->arbeitgeber1!==null) ? $entityCreditRequest->arbeitgeber1 : "",
                'street' => '-',
                'zip' => $entityCreditRequest->arbeitgeber_plz1,
                'city' => ($entityCreditRequest->arbeitgeber_ort1!==null) ? $entityCreditRequest->arbeitgeber_ort1 : "",
                'since' => date("Y-m-d", strtotime($entityCreditRequest->anstellung1))
                //'since' => date("Y-m-d", strtotime($row['anstellung1']))
            ],            
            'bank_data' => [
                'iban' => (isset($entityCreditRequest->coapplicant_iban)) ? $entityCreditRequest->coapplicant_iban : $entityCreditRequest->iban,
                 'bic' => (isset($entityCreditRequest->coapplicant_bic)) ? $entityCreditRequest->coapplicant_bic : $entityCreditRequest->bic,
            ] 
        ];

		//dump('prepearingCoApplicantRequestData');
		//var_dump($requestData);
		//echo json_encode($requestData);

        return $requestData;
    }
    
    public function prepearingApplicantRequestData($entityCreditRequest, $additionalData = null) {
	
		
		if($entityCreditRequest instanceof \App\CreditOrder){
		}
		else{
			$entityCreditRequest=\App\CreditOrder::where('id',$entityCreditRequest->id)->first();
		}
		
		$iban_other='DE19500207000001234567';
		if(empty($entityCreditRequest->iban)) {
            $entityCreditRequest->iban=$iban_other;
        }	
		if(empty($entityCreditRequest->coapplicant_iban)) {
            $entityCreditRequest->coapplicant_iban=$iban_other;
        }	
		

		$bic_other='FBHLDEFFXXX';
		if(empty($entityCreditRequest->bic)) {
            $entityCreditRequest->bic=$bic_other;
        }	
		if(empty($entityCreditRequest->coapplicant_bic)) {
            $entityCreditRequest->coapplicant_bic=$bic_other;
        }	
		
        $netIncome = $this->getNetIncome($entityCreditRequest->netto);
        $incomeChildBenefits = $this->getIncomeChildBenefits($entityCreditRequest->kinder);
        $incomeOther = $this->getIncomeOther($entityCreditRequest->nebeneinkommen_mtl);
        $incomeTotal = $netIncome + $incomeChildBenefits + $incomeOther;           

        // additionalData
        $duration = isset($additionalData['duration']) ? $additionalData['duration'] : $this->getDuration(intval($entityCreditRequest->kreditbetrag));
        $rsv = (isset($additionalData['rsv']) && !$additionalData['rsv']) ? 0 : 3; // Wünscht der Kunde eine Restkreditversicherung? - 3 = "ja" | 0 = "nein"
      
		 if($entityCreditRequest->arbeitgeber_plz==""){
			
				 $entityCreditRequest->arbeitgeber_plz=$entityCreditRequest->plz;
			 
		 }
        /*$enstr=(preg_replace('/ [\d,.]+/s', '', $entityCreditRequest->str));

        $entityCreditRequest->str_nr = str_replace(" ","",str_replace(explode(" ", $enstr), '', $entityCreditRequest->str));
        $entityCreditRequest->str=$enstr;*/
		$requestData = [
            'external_id' => (int)$entityCreditRequest->id,
            'loan_asked' => $this->getLoanAsked(intval($entityCreditRequest->kreditbetrag)),
            'duration' => $duration,
            'category' => 0, // "Sonstiges"
            'rsv' => $rsv,
            'collection_day' => 1, // Wann soll die monatliche Rate eingezogen werden? Am 1. oder am 15. des Monats?       
            'is_accepted_terms_of_service' => true, // Hat der Kunde die Nutzungsbedingungen akzeptiert?
            'is_accepted_solvency_retrieval' => true, // Hat der Kunde dem Abruf von Bonitätsinformationen zugestimmt?
            'personal_data' => [
                'address' => $entityCreditRequest->anr, // Anrede (1 = "Herr" | 2 = "Frau")
                'forename' => $entityCreditRequest->vorname,
                'surname' => $entityCreditRequest->nachname,
                'family_status' => $this->getFamilyStatus($entityCreditRequest->famstand),
                'birth_date' => date("Y-m-d", strtotime($entityCreditRequest->gebdat)),
                'nationality' => $entityCreditRequest->staat,
                'occupation' => $this->getOccupation($entityCreditRequest->beruf),
                'has_credit_card' => 0,
                'has_ec_card' => 1,  
                'has_real_estate' => $this->getValueForHasRealEstate($entityCreditRequest->eigentum_zusatz, $entityCreditRequest->wohnsituation),
                'main_earner' => 1,
                'housing_type' => $this->getHousingType($entityCreditRequest->wohnsituation),
                'car_owner' => 0
            ], 
            'contact_data' => [
                'living_since' => date("Y-m-d", strtotime($entityCreditRequest->wohnhaft_seit)),
                'street_name' => $entityCreditRequest->str,
                'street_number' => $entityCreditRequest->str_nr,
                'zip_code' => $entityCreditRequest->plz,
                'city' => $entityCreditRequest->ort,
                'telephone' => $this->getTelephone($entityCreditRequest),
                'email' => $entityCreditRequest->email
            ],            
            'income' => [
                'total' => $incomeTotal, 
                'net_income' => $netIncome,
                'child_benefits' => $incomeChildBenefits,
                'other' => $incomeOther
            ], 
            'expenses' => $this->getExpenses($entityCreditRequest, false),            
            'employer_data' => [
                'company' => ($entityCreditRequest->arbeitgeber!==null) ? $entityCreditRequest->arbeitgeber : "",
                'street' => '-',
                'zip' => $entityCreditRequest->arbeitgeber_plz,
                'city' => ($entityCreditRequest->arbeitgeber_ort!==null) ? $entityCreditRequest->arbeitgeber_ort : "",
                'since' => date("Y-m-d", strtotime($entityCreditRequest->anstellung))
                //'since' => date("Y-m-d", strtotime($row['anstellung']))
            ],            
            'bank_data' => [
                'iban' => $entityCreditRequest->iban,
                'bic' => $entityCreditRequest->bic
            ]            
        ];
		 

		 //
		 //dump('prepearingCoApplicantRequestData');
		 //echo json_encode($requestData);

		
       
        return $requestData;
    }
    
    public function getLoanAsked($loanAmount) {
        $loanAsked = 0;
        
        if($loanAmount < 3000)  $loanAsked = 3000;
        elseif ($loanAmount > 25000)  $loanAsked = 25000;
        else  $loanAsked = $this->roundLoanAmount($loanAmount);
        
        return $loanAsked;    
    }
    
    public function roundLoanAmount($loanAmount) {
        return (ceil($loanAmount / 100)) * 100;
//        return round($loanAmount, -2);
    }
    
    // Laufzeit des Kredits in Monaten
    // [12|24|36|48|60|72|84]
    public function getDuration($loanAmount) {
        $duration = 0;
        
        if (3000 <= $loanAmount && $loanAmount <= 6000)  $duration = 60;
        else  $duration = 84;
        
        return $duration;    
    }
    
    // Familienstand
    public function getFamilyStatus($familyStatusInDB) {
        return (int) $this->options->getFamilyStatus($familyStatusInDB, 'id_aux');
    }
    
    // Berufsgruppe
    public function getOccupation($berufId) {        
        return (int) $this->options->getBeruf($berufId, 'id_aux');
    }
    
    // Wohnsituation      
    public function getHousingType($wohnsituationId) { 
        return (int) $this->options->getHousingType($wohnsituationId, 'id_aux');
    }
    
    // Ist der Kunde Immobilienbesitzer?
    // 0 = nein | 1 = ja
    // $eigentumZusatzId => 'eigentum_zusatz'
    // $wohnsituationId => 'wohnsituation'    
    public function getValueForHasRealEstate($eigentumZusatzId, $wohnsituationId) { 
        $hasRealEstate = 0;
        
        if($wohnsituationId == 2) $hasRealEstate = 1; //'im Eigenheim'
        elseif($eigentumZusatzId == 2) $hasRealEstate = 1; //'Besitzen Sie Wohneigentum?' - 'Ja' 
   
        return $hasRealEstate;
    }    
    
    public function getTelephone($entityCreditRequest) {   
	if($entityCreditRequest->telefon){
	$entityCreditRequest->telefon = str_replace( "+","", $entityCreditRequest->telefon);
				$entityCreditRequest->telefon = str_replace( "-","", $entityCreditRequest->telefon);
				$string=str_split($entityCreditRequest->telefon, 2);
				if($string[0]=='49'){
					$rest = substr($entityCreditRequest->telefonn, 2);
					$entityCreditRequest->telefon="0".$rest;
			}
	}	
				
			if($entityCreditRequest->handy){	
			$entityCreditRequest->handy = str_replace( "+","", $entityCreditRequest->handy);
				$entityCreditRequest->handy = str_replace( "-","", $entityCreditRequest->handy);
				$string=str_split($entityCreditRequest->handy, 2);
				if($string[0]=='49'){
					$rest = substr($entityCreditRequest->handy, 2);
					$entityCreditRequest->handy="0".$rest;
			}}	
        return ($entityCreditRequest->handy) ? $entityCreditRequest->handyv . $entityCreditRequest->handy : $entityCreditRequest->telefonv . $entityCreditRequest->telefon;
    }    
    
    public function convertEuroToCents($euroAmount) {
//        return number_format($euroAmount, 0, '', '') * 100;
        return $euroAmount * 100;
    }

    // Seit wann wohnt der Kunde an der angegebenen Adresse?
    // YYYY-MM-DD
    // gesamtbetrachtung(coapplicant_same_household) = 2  <=> "Leben Sie im gleichen Haushalt wie der Hauptantragsteller?:" - "Ja"    
    public function getCoApplicantLivingSince($entityCreditRequest) { 
        if($entityCreditRequest->gesamtbetrachtung == 2) {
            return date("Y-m-d", strtotime($entityCreditRequest->wohnhaft_seit)); // перенимаем от MainApplicant
        } else {
            return date("Y-m-d", strtotime($entityCreditRequest->wohnhaft_seit1));
        }        
    }
    
    // Nettoeinkommen (pro Monat in Cent)
    // $netto => netto | netto1
    public function getNetIncome($netto) {     
        return $this->convertEuroToCents($netto);
    }
    
    // Sonstiges Einkommen (pro Monat in Cent)
    // $additionalRevenue => nebeneinkommen_mtl | nebeneinkommen_mtl1 
    public function getIncomeOther($additionalRevenue) {     
        return $this->convertEuroToCents($additionalRevenue);
    }
    
    // Kindergeld und Unterhalt (pro Monat in Cent)
    public function getIncomeChildBenefits($kinder) {  
        $incomeChildBenefits = 0;
        
        if($kinder > 3) {
            $incomeChildBenefits += ($kinder - 3) * $this->childBenefit['more'];
            $kinder = 3;
        }
        switch ($kinder) {
            case 3:
                $incomeChildBenefits += $this->childBenefit['third'];
            case 2:
                $incomeChildBenefits += $this->childBenefit['second'];
            case 1:
                $incomeChildBenefits += $this->childBenefit['first'];
        } 
        
        return $incomeChildBenefits;
    }
    
    // Kindergeld und Unterhalt (pro Monat in Cent)
    // gesamtbetrachtung(coapplicant_same_household) = 2  <=> "Leben Sie im gleichen Haushalt wie der Hauptantragsteller?:" - "Ja" 
    public function getCoApplicantIncomeChildBenefits($entityCreditRequest) {     
        if($entityCreditRequest->gesamtbetrachtung == 2) {
            return $this->getIncomeChildBenefits($entityCreditRequest->kinder); // перенимаем от MainApplicant
        } else {
            return 0;
        }
    }    
    
    //'total_expenses' => Summe aller Ausgaben (pro Monat in Cent); muss mit der Summe der anderen angegebenen Ausgabenwerte übereinstimmen
    //'rent_and_mortgage' => Miete/Rate für Eigentum (pro Monat in Cent)
    //'support_expenses' => Unterhaltskosten (pro Monat in Cent)
    //'insurance_and_savings' => Versicherungen und Sparen (pro Monat in Cent)
    //'memberships' => Mitgliedsbeiträge (pro Monat in Cent)
    //'debt_expenses' => Andere Kredite/Verpflichtungen (pro Monat in Cent)
    //'living_expenses' => ???
    //'other' => Sonstige Ausgaben (pro Monat in Cent)
    //
    // $unterhalt => unterhalt | unterhalt1
    public function getExpenses($entityCreditRequest, $coapplicant = false) { 
        
        $unterhalt = ($coapplicant) ? $entityCreditRequest->unterhalt1 : $entityCreditRequest->unterhalt;
        
        $expenses = [
            'total_expenses' => 0,
            'rent_and_mortgage' => $this->getExpensesRentAndMortgage($entityCreditRequest, $coapplicant), 
            'support_expenses' => $this->convertEuroToCents($unterhalt),            
            'insurance_and_savings' => 0,
            'memberships' => 0,
            'debt_expenses' => 0,
            'living_expenses' => 0,
            'other' => 0
        ];
        
        $expenses['total_expenses'] = array_sum($expenses);

        return $expenses;
    }
    
    //'rent_and_mortgage' => Miete/Rate für Eigentum (pro Monat in Cent)
    // gesamtbetrachtung(coapplicant_same_household) = 2  <=> "Leben Sie im gleichen Haushalt wie der Hauptantragsteller?:" - "Ja"  
    public function getExpensesRentAndMortgage($entityCreditRequest, $coapplicant = false) {        
        if($coapplicant && $entityCreditRequest->gesamtbetrachtung !== 2) { //coapplicant and living in different houses
            return 0;
        } else {
            return $this->convertEuroToCents($entityCreditRequest->miete + $entityCreditRequest->eigentum_belastung_mtl);
        }
    }   
    
    //////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////
    
    public function testGettingData($entityCreditRequest) {  

        $entityCreditRequest->famstand = 6;
        echo "<br>famstand: " . $entityCreditRequest->famstand;
        echo "<br>getFamilyStatus: " . $this->getFamilyStatus($entityCreditRequest->famstand) . "<hr>";

        $entityCreditRequest->beruf = 22;
        echo "<br>beruf: " . $entityCreditRequest->beruf;
        echo "<br>getOccupation: " . $this->getOccupation($entityCreditRequest->beruf) . "<hr>";

        echo "<br>wohnsituation: " . $entityCreditRequest->wohnsituation;
        echo "<br>getHousingType: " . $this->getHousingType($entityCreditRequest->wohnsituation) . "<hr>";

        echo "<br>eigentum_zusatz: " . $entityCreditRequest->eigentum_zusatz;
        echo "<br>wohnsituation: " . $entityCreditRequest->wohnsituation;
        echo "<br>getValueForHasRealEstate: " . $this->getValueForHasRealEstate($entityCreditRequest->eigentum_zusatz, $entityCreditRequest->wohnsituation) . "<hr>";


    //    $entityCreditRequest->gesamtbetrachtung'] = 2;
        echo "<hr><hr><br>gesamtbetrachtung: " . $entityCreditRequest->gesamtbetrachtung;
        echo "<br>wohnhaft_seit: " . $entityCreditRequest->wohnhaft_seit;
        echo "<br>wohnhaft_seit1: " . $entityCreditRequest->wohnhaft_seit1;
        echo "<br>getOccupation: " . $this->getCoApplicantLivingSince($entityCreditRequest) . "<hr>"; 

    }



}



