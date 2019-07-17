<?php

namespace App\modules\CreditRequest\Model\Service\ExportDataForPartner;

//traits
use \App\modules\Core\Model\Traits\Singleton;

//models
use \App\modules\CreditRequest\Model\Service\ExportDataForPartner\Base as BaseExportDataForPartnerService;
use \App\modules\Core\Model\Registry;
use App\Http\ArraysClass;
use Log;
class Planfinanz24 extends BaseExportDataForPartnerService {
    use Singleton;

    public $folderPath ;
    public $mailBlockName = '\App\modules\CreditRequest\Block\Mail\ExportDataForPartner\Planfinanz24';
    public $creditRequest;

    public function __construct()
    {
        parent::__construct();
        $this->folderPath = base_path().'/files/Planfinanz24';

    }
    public function inits($creditRequest){
        $this->creditRequest=$creditRequest;
        $this->process();
    }

    public function init() {
        //$config = Registry::getInstance()->getConfig();
        $config = new ArraysClass();
        $config=$config->conf;
        $this->enabled = $config['api']['planfinanz24']['enabled'];
        if(!$this->enabled) return;
        $this->sender = $config['api']['planfinanz24']['mail']['sender'];
        $this->recipient = $config['api']['planfinanz24']['mail']['recipient'];

    }

    public function process() {
        //generate csv file
        $filePath = $this->generateCSV();

        //send csv file
        $result = $this->sendViaEmail($filePath);
        return $result;
    }

    public function getFileName() {
        $creditRequest = $this->creditRequest;

        return sprintf('credicom-export--id-%s--%s.csv', $creditRequest->getId(), date('Y-m-d_H_i_s'));
    }

    public function getExportData() {
        $creditRequest = $this->creditRequest;
        return [
            'date' => ($creditRequest['date'] != '0000-00-00') ? date("d.m.Y", strtotime($creditRequest['date'])) : null,
            'anr' => ($creditRequest['anr']==1) ? 'Herr' : 'Frau',
            'vorname' =>iconv(mb_detect_encoding(trim($creditRequest['vorname']), 'UTF-8, ISO-8859-1', true), "iso-8859-1", trim($creditRequest['vorname'])) ,//iconv('UTF-8', "iso-8859-1//IGNORE", trim($creditRequest['vorname']))
            'nachname' =>iconv(mb_detect_encoding(trim($creditRequest['nachname']), 'UTF-8, ISO-8859-1', true), "iso-8859-1", trim($creditRequest['nachname'])) ,//iconv('UTF-8', "iso-8859-1//IGNORE", trim($creditRequest['nachname']))
            'str' => iconv(mb_detect_encoding(trim($creditRequest['str']), 'UTF-8, ISO-8859-1', true), "iso-8859-1", trim($creditRequest['str'])),//
            'str_nr' => $creditRequest['str_nr'],
            'plz' => $creditRequest['plz'],
            'ort' => iconv(mb_detect_encoding(trim($creditRequest['ort']), 'UTF-8, ISO-8859-1', true), "iso-8859-1", trim($creditRequest['ort'])),//iconv('UTF-8', "iso-8859-1//IGNORE", trim($creditRequest['ort']))
            'gebdat' => ($creditRequest['gebdat'] != '0000-00-00') ? date("d.m.Y", strtotime($creditRequest['gebdat'])) : null,
            'handy' => implode('-', array_filter([$creditRequest['handyv'], $creditRequest['handy']])),
            'telefon' => implode('-', array_filter([$creditRequest['telefonv'], $creditRequest['telefon']])),
            'email' => $creditRequest['email'],
            'famstand' => $this->getFamilyStatus($creditRequest['famstand']),
            'beruf' => $this->getBeruf($creditRequest['beruf']),
            'mtl_rate' => $creditRequest['mtl_rate'],
            'netto' => $creditRequest['netto'],
            'nebeneinkommen_mtl' => $creditRequest['nebeneinkommen_mtl'],
            'kinder' => $creditRequest['kinder'],
            'kreditbetrag' => $creditRequest['kreditbetrag'],
            'creditor' => 0,    //default value
            'id' => $creditRequest['id'],
            'action' => 'Aktion-erforderlich',  //default value
            'resubmission' => ''    //default value
        ];
    }

    public function getExportDataHeader() {
        return [
            'date' => 'Anfragedatum',
            'anr' => 'Anrede',
            'vorname' => 'Vorname',
            'nachname' => 'Nachname',
            'str' => 'Strasse',
            'str_nr' => 'HausNr',
            'plz' => 'Plz',
            'ort' => 'Ort',
            'gebdat' => 'Geburtsdatum',
            'handy' => 'Telefon1',
            'telefon' => 'Telefon2',
            'email' => 'Email',
            'famstand' => 'Familienstand',
            'beruf' => 'Beruf',
            'mtl_rate' => 'Rate',
            'netto' => 'Einnahmen',
            'nebeneinkommen_mtl' => 'Nebeneinkommen',
            'kinder' => 'Unterhalt',
            'kreditbetrag' => 'Schuldsumme',
            'creditor' => iconv('UTF-8', "iso-8859-1", 'Gläubiger'),//iconv('UTF-8', "iso-8859-1//IGNORE", 'Gläubiger')
            'id' => 'ExterneId',
            'action' => 'Bearbeitungsstatus',
            'resubmission' => 'Wiedervorlage-am'
        ];
    }

    public function getFamilyStatus($familyStatusInDB) {
        $options = \App\Http\Model\CreditForm\Options::getInstance();
        $familyStatus = $options->getFamilyStatus($familyStatusInDB, 'bez');
        return iconv('UTF-8', "iso-8859-1", $familyStatus); //iconv('UTF-8', "iso-8859-1//IGNORE", $familyStatus)
    }

    public function getBeruf($berufId) {
        $options = \App\Http\Model\CreditForm\Options::getInstance();
        $beruf = $options->getBeruf($berufId, 'planfinanz24');
        return iconv('UTF-8', "iso-8859-1", $beruf); //iconv('UTF-8', "iso-8859-1//IGNORE", $beruf)
    }

    public function additionalFileProcessing($csv, $filePath) {
        $content = file_get_contents($filePath);
        $newContent = str_replace($csv->getEnclosure(), '', $content);
        return file_put_contents($filePath, iconv(mb_detect_encoding(trim($newContent), 'UTF-8, ISO-8859-1', true),'iso-8859-1', $newContent));//,'iso-8859-1//IGNORE', $newContent)
    }

}
