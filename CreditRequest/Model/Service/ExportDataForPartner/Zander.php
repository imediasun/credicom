<?php

namespace App\modules\CreditRequest\Model\Service\ExportDataForPartner;

//traits
use \App\modules\Core\Model\Traits\Singleton;

//models
use \App\modules\CreditRequest\Model\Service\ExportDataForPartner\Base as BaseExportDataForPartnerService;
use \App\modules\Core\Model\Registry;


class Zander extends BaseExportDataForPartnerService {
    use Singleton;
    
    public $folderPath = '../files/Zander';
    public $mailBlockName = '\App\modules\CreditRequest\Block\Mail\ExportDataForPartner\Zander';
    

    public function init() {
        $config = Registry::getInstance()->getConfig();      

        $this->enabled = $config['api']['zander']['enabled'];        
        if(!$this->enabled) return;
        
        $this->sender = $config['api']['zander']['mail']['sender'];
        $this->recipient = $config['api']['zander']['mail']['recipient'];  
    }
    
    public function getFileName() {
        $creditRequest = $this->getCreditRequest();
         $name = sprintf('credicom-export--id-%s--%s', $creditRequest->id, date('Y-m-d'));/*_H:i:s*/
        $this->mailSubject = $name;
        
        return $name . '.csv';
    }
    
    public function getExportData() {
        $data = [];
        
        $creditRequest = $this->getCreditRequest();
        $isMainApplicant = $this->getIsMainApplicant();
        $isBoth = $this->getIsBoth();
        
        $anr = ($isMainApplicant) ? $creditRequest['anr'] : $creditRequest['anr1'];
        $vorname = ($isMainApplicant) ? $creditRequest['vorname'] : $creditRequest['vorname1'];
        $nachname = ($isMainApplicant) ? $creditRequest['nachname'] : $creditRequest['nachname1'];
        $str = ($isMainApplicant) ? $creditRequest['str'] : $creditRequest['str1'];
        $str_nr = ($isMainApplicant) ? $creditRequest['str_nr'] : $creditRequest['str_nr1'];
        $plz = ($isMainApplicant) ? $creditRequest['plz'] : $creditRequest['plz1'];
        $ort = ($isMainApplicant) ? $creditRequest['ort'] : $creditRequest['ort1'];        
        $gebdat = ($isMainApplicant) ? $creditRequest['gebdat'] : $creditRequest['gebdat1'];
        $geb_ort = ($isMainApplicant) ? $creditRequest['geb_ort'] : $creditRequest['geb_ort1'];
        $wohnhaft_seit = ($isMainApplicant) ? $creditRequest['wohnhaft_seit'] : $creditRequest['wohnhaft_seit1'];
        $famstand = ($isMainApplicant) ? $creditRequest['famstand'] : $creditRequest['famstand1'];
        $staat = ($isMainApplicant) ? $creditRequest['staat'] : $creditRequest['staat1'];
        $arbeitgeber = ($isMainApplicant) ? $creditRequest['arbeitgeber'] : $creditRequest['arbeitgeber1'];
        $beruf = ($isMainApplicant) ? $creditRequest['beruf'] : $creditRequest['beruf1'];
        $anstellung = ($isMainApplicant) ? $creditRequest['anstellung'] : $creditRequest['anstellung1'];
        $netto = ($isMainApplicant) ? $creditRequest['netto'] : $creditRequest['netto1'];
//$vorname = 't\e|s"t/?';
        
        $data = [            
            'kid' => $creditRequest['kid'],
            'anr' => $this->getAnrede($anr),
            'vorname' => $this->prepareStringData($vorname),
            'nachname' => $this->prepareStringData($nachname),           
            'str+str_nr' => $this->prepareStringData($str) . ' ' . $str_nr,         
            'plz' => $plz,
            'ort' => $this->prepareStringData($ort),
            'gebdat' => $this->prepareDateData($gebdat, "d.m.Y"), 
            'handy' => implode('-', array_filter([$creditRequest['handyv'], $creditRequest['handy']])),
            'telefon' => implode('-', array_filter([$creditRequest['telefonv'], $creditRequest['telefon']])),             
            'email' => $this->prepareStringData($creditRequest['email']),
            'kreditbetrag' => $creditRequest['kreditbetrag'],            
            'mtl_rate' => $creditRequest['mtl_rate'],
            'vzweck' => $this->getVzweck($creditRequest['vzweck']),  
            'geb_ort' => $this->prepareStringData($geb_ort), 
            'wohnhaft_seit' => $this->prepareDateData($wohnhaft_seit, "d.m.Y"),            
            'famstand' => $this->getFamilyStatus($famstand),            
            'kinder' => $creditRequest['kinder'],            
            'wohnsituation' => $this->getWohnsituation($creditRequest['wohnsituation']),
            'miete' => $this->getMiete($creditRequest),            
            'staat' => $this->getStaat($staat),
            'arbeitgeber' => $this->prepareStringData($arbeitgeber),
            '_beruf' => $this->getBeruf($beruf), 
            'anstellung' => $this->prepareDateData($anstellung, "d.m.Y"),            
            'beruf' => $this->getBeruf($beruf), 
            'currentYearSales' => 0,  //default value
            'lastYearSales' => 0,  //default value
            'privacyConsented' => 'ja',  //default value
            'netto' => $netto,
            'informationAuthorityConsented' => 'ja',  //default value
            'date' => $this->prepareDateData($creditRequest['date'], "Y-m-d H:i:s"),
            
            'MA' => 'nein',       
            'kid_MA' => '',
            'anr_MA' => '',
            'vorname_MA' => '',
            'nachname_MA' => '',            
            'str+str_nr_MA' => '',            
            'plz_MA' => '',
            'ort_MA' => '',             
            'gebdat_MA' => '',
            'handy_MA' => '',
            'telefon_MA' => '',            
            'email_MA' => '',            
            'kreditbetrag_MA' => '',
            'mtl_rate_MA' => '',            
            'vzweck_MA' => '',               
            'geb_ort_MA' => '',
            'wohnhaft_seit_MA' => '',  
            'famstand_MA' => '',
            'kinder_MA' => '',
            'wohnsituation_MA' => '',
            'miete_MA' => '',            
            'staat_MA' => '',         
            'arbeitgeber_MA' => '',            
            '_beruf_MA' => '', 
            'anstellung_MA' => '',            
            'beruf_MA' => '', 
            'currentYearSales_MA' => '',
            'lastYearSales_MA' => '',
            'privacyConsented_MA' => '',
            'netto_MA' => '',
            'informationAuthorityConsented_MA' => '',
            'date_MA' => '',
        ]; 
        
        if($isBoth) {
            $coApplicantData = $this->setCoApplicantExportData();
            $data = array_merge($data, $coApplicantData);
        }
        
        return $data;
    } 
    
    public function setCoApplicantExportData() {
        $creditRequest = $this->getCreditRequest();
        
        return [             
            'MA' => 'ja',       
            'kid_MA' => $creditRequest['kid'],
            'anr_MA' => $this->getAnrede($creditRequest['anr1']),
            'vorname_MA' => $this->prepareStringData($creditRequest['vorname1']),
            'nachname_MA' => $this->prepareStringData($creditRequest['nachname1']),            
            'str+str_nr_MA' => $this->prepareStringData($creditRequest['str1']) . ' ' . $creditRequest['str_nr1'],            
            'plz_MA' => $creditRequest['plz1'],
            'ort_MA' => $this->prepareStringData($creditRequest['ort1']),             
            'gebdat_MA' => $this->prepareDateData($creditRequest['gebdat1'], "d.m.Y"),
            'handy_MA' => implode('-', array_filter([$creditRequest['handyv'], $creditRequest['handy']])),
            'telefon_MA' => implode('-', array_filter([$creditRequest['telefonv'], $creditRequest['telefon']])),             
            'email_MA' => $this->prepareStringData($creditRequest['email']),            
            'kreditbetrag_MA' => $creditRequest['kreditbetrag'],            
            'mtl_rate_MA' => $creditRequest['mtl_rate'],
            'vzweck_MA' => $this->getVzweck($creditRequest['vzweck']),            
            'geb_ort_MA' => $this->prepareStringData($creditRequest['geb_ort1']),
            'wohnhaft_seit_MA' => $this->prepareDateData($creditRequest['wohnhaft_seit1'], "d.m.Y"),             
            'famstand_MA' => $this->getFamilyStatus($creditRequest['famstand1']),            
            'kinder_MA' => $creditRequest['kinder'],            
            'wohnsituation_MA' => $this->getWohnsituation($creditRequest['wohnsituation']),
            'miete_MA' => $this->getMiete($creditRequest),             
            'staat_MA' => $this->getStaat($creditRequest['staat1']),         
            'arbeitgeber_MA' => $this->prepareStringData($creditRequest['arbeitgeber1']),
            '_beruf_MA' => $this->getBeruf($creditRequest['beruf1']), 
            'anstellung_MA' => $this->prepareDateData($creditRequest['anstellung1'], "d.m.Y"),
            'beruf_MA' => $this->getBeruf($creditRequest['beruf1']),
            'netto_MA' => $creditRequest['netto1'],
            'currentYearSales_MA' => 0,  //default value
            'lastYearSales_MA' => 0,  //default value
            'privacyConsented_MA' => 'ja',  //default value
            'informationAuthorityConsented_MA' => 'ja',  //default value
            'date_MA' => $this->prepareDateData($creditRequest['date'], "Y-m-d H:i:s"),
        ]; 
    }
    
    public function getExportDataHeader() {
        return [            
            'kid' => 'Kundennummer',
            'anr' => 'Anrede',
            'vorname' => 'Vorname',
            'nachname' => 'Nachname',            
            'str+str_nr' => 'Strasse inkl.Hausnummer',            
            'plz' => 'PLZ',
            'ort' => 'Wohnort',             
            'gebdat' => 'Geburtsdatum',
            'handy' => 'Mobil',
            'telefon' => 'Festnetz',            
            'email' => 'Email',            
            'kreditbetrag' => 'Kreditwunsch',
            'mtl_rate' => 'Ratenwunsch',            
            'vzweck' => 'Verwendungszweck',               
            'geb_ort' => 'Geburtsort',
            'wohnhaft_seit' => 'wohnhaft seit',  
            'famstand' => 'Familienstand',
            'kinder' => 'Anzahl Kinder unter 18',
            'wohnsituation' => 'Wohnart', 
            'miete' => 'Miete',            
            'staat' => iconv('UTF-8', "iso-8859-1", 'Staatsangehörigkeit'),         
            'arbeitgeber' => 'Firmenname',            
            '_beruf' => 'Berufsart', 
            'anstellung' => iconv('UTF-8', "iso-8859-1", 'Beschäftigt seit'),            
            'beruf' => 'Beruf',
            'netto' => 'Netto',
            'currentYearSales' => 'Umsatz aktueller Jahr',
            'lastYearSales' => 'Umsatz Vorjahr',
            'privacyConsented' => 'Datenschutz eingewilligt',
            'informationAuthorityConsented' => 'Auskunftsvollmacht eingewilligt',
            'date' => 'Zeitstempel',            

            'MA' => 'MA',            
            'kid_MA' => 'Kundennummer_MA',
            'anr_MA' => 'Anrede_MA',
            'vorname_MA' => 'Vorname_MA',
            'nachname_MA' => 'Nachname_MA',            
            'str+str_nr_MA' => 'Strasse inkl.Hausnummer _MA',            
            'plz_MA' => 'PLZ_MA',
            'ort_MA' => 'Wohnort_MA',             
            'gebdat_MA' => 'Geburtsdatum_MA',
            'handy_MA' => 'Mobil_MA',
            'telefon_MA' => 'Festnetz_MA',            
            'email_MA' => 'Email_MA',            
            'kreditbetrag_MA' => 'Kreditwunsch_MA',
            'mtl_rate_MA' => 'Ratenwunsch_MA',            
            'vzweck_MA' => 'Verwendungszweck_MA',               
            'geb_ort_MA' => 'Geburtsort_MA',
            'wohnhaft_seit_MA' => 'wohnhaft seit _MA',  
            'famstand_MA' => 'Familienstand_MA',
            'kinder_MA' => 'Anzahl Kinder unter 18 _MA',
            'wohnsituation_MA' => 'Wohnart_MA',
            'miete_MA' => 'Miete_MA',            
            'staat_MA' => iconv('UTF-8', "iso-8859-1", 'Staatsangehörigkeit_MA'),         
            'arbeitgeber_MA' => 'Firmenname_MA',            
            '_beruf_MA' => 'Berufsart_MA', 
            'anstellung_MA' => iconv('UTF-8', "iso-8859-1", 'Beschäftigt seit _MA'),            
            'beruf_MA' => 'Beruf_MA',
            'netto_MA' => 'Netto_MA',
            'currentYearSales_MA' => 'Umsatz aktueller Jahr _MA',
            'lastYearSales_MA' => 'Umsatz Vorjahr _MA',
            'privacyConsented_MA' => 'Datenschutz eingewilligt _MA',
            'informationAuthorityConsented_MA' => 'Auskunftsvollmacht eingewilligt _MA',
            'date_MA' => 'Zeitstempel_MA',
        ];
    }

    public function getFamilyStatus($familyStatusInDB) { 
        $options = \App\Http\Model\CreditForm\Options::getInstance();
        $familyStatus = $options->getFamilyStatus($familyStatusInDB, 'bez');
        return iconv('UTF-8', "iso-8859-1", $familyStatus);
    }
    
    public function getBeruf($berufId) { 
        $options = \App\Http\Model\CreditForm\Options::getInstance();
        $beruf = $options->getBeruf($berufId, 'bez'); 
        return html_entity_decode($beruf, ENT_HTML5, "iso-8859-1");
    }
    
    public function getVzweck($vzweckId) { 
        $options = \App\Http\Model\CreditForm\Options::getInstance();
        $vzweck = $options->getIntendedUse($vzweckId, 'bez'); 
        return iconv('UTF-8', "iso-8859-1", $vzweck);
    }
    
    public function getWohnsituation($wohnsituationId) {
        $options = \App\Http\Model\CreditForm\Options::getInstance();
        $wohnsituation = $options->getResidentType($wohnsituationId); 
        return iconv('UTF-8', "iso-8859-1", $wohnsituation);
    }
    
    public function getMiete($creditRequest) {
        $miete = 0;

        switch ($creditRequest['wohnsituation']) {
            case 1:  // 'zur Miete'
                $miete = $creditRequest['miete'];
                break;
            case 2:  // 'im Eigenheim'
                $miete = $creditRequest['eigentum_belastung_mtl'];
                break;
            default:  // 'bei Eltern'
                $miete = 0;
        }
        
        return $miete;
    }
    
    public function getStaat($staatId) {
        $options = \App\Http\Model\CreditForm\Options::getInstance();
        $staat = $options->getLaender($staatId);         
        return iconv('UTF-8', "iso-8859-1", $staat);        
    }
    
    public function getAnrede($anredeId) {
        $options = \App\Http\Model\CreditForm\Options::getInstance();
        $anrede = $options->getSalutation($anredeId);         
        return iconv('UTF-8', "iso-8859-1", $anrede);        
    }
    
    public function additionalFileProcessing($csv, $filePath) {
        $newline = $csv->getNewline();
        $delimiter = $csv->getDelimiter();
        $content = file_get_contents($filePath);
        $rows = explode($newline, $content);
        
        foreach($rows as $rowKey => $rowValue) {
            if(empty($rowValue)) continue;
            
            $fields = [];
            $fields = explode($delimiter, $rowValue);
            
            foreach($fields as $fieldKey => $fieldValue) {
                if(!preg_match('/^"(.*)"$/i', $fieldValue)) {
                    $fields[$fieldKey] = sprintf('"%s"', $fieldValue);
                }
            }            
            $rows[$rowKey] = implode($delimiter, $fields);
        }
        
        $newContent = implode($newline, $rows);

        return file_put_contents($filePath, $newContent);
    }
        
}

