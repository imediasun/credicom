<?php

namespace App\modules\Sigma\Model;


use \App\modules\Core\Model\Registry;

use App\modules\Core\Model\Traits\Singleton;

use App\modules\Sigma\Model\Service\SigmaExport;
use App\modules\Sigma\Model\Service\SigmaImport;

use App\modules\CreditRequest\Collection\CreditRequestStatus as CollectionCreditRequestStatus;
use App\modules\CreditRequest\Model\CreditRequest as ModelCreditRequest;
use App\Http\ArraysClass;

class Service
{
    use Singleton;

    public $enabled = false;
    public $folders;

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        $config = Registry::getInstance()->getConfig();
        $config = new ArraysClass();
		$this->config=$config;
        $config = $config->conf;
        //dump('SIgmaService');
        //dump($config['api']['sigma']['enabled']);
        $this->enabled = $config['api']['sigma']['enabled'];
        if (!$this->enabled) return;

        $this->initFolderStructure();
    }

    public function initFolderStructure()
    {
        $this->folders = [
            'process' => [
                'export' => sprintf('%s/files/cache/sigma/process/export', base_path()),//BASE_DIR
                'import' => sprintf('%s/files/cache/sigma/process/import', base_path()),//BASE_DIR
            ],
            'archive' => [
                'export' => sprintf('%s/files/cache/sigma/archive/export', base_path()),//BASE_DIR
                'import' => sprintf('%s/files/cache/sigma/archive/import', base_path()),//BASE_DIR
            ],
        ];

        foreach ($this->folders as $category => $list) {
            foreach ($list as $type => $path) {
                if (is_dir($path)) continue;
                mkdir($path, 0774, true);
            }
        }
    }

    /**
     * Generate CSV
     * Send generated CSV via email
     */
    public function processGenerate()
    {
        if (!$this->enabled) return;
        dump('processGenerate');
        //$digits = 3;
        //$fileName = sprintf('export.%s.%d.csv', date('Y-m-d H:i:s'),rand(pow(10, $digits-1), pow(10, //$digits)-1));
        /* $fileNameMa = sprintf('export.%s.%d.csv', date('Y-m-d H:i:s'),rand(pow(10, $digits-1), pow(10, $digits)-1)); */
        //$processFilePath = sprintf('%s/%s', $this->folders['process']['export'], $fileName);
        //$archiveFilePath = sprintf('%s/%s', $this->folders['archive']['export'], $fileName);
        /* 		$processFilePathMa = sprintf('%s/%s', $this->folders['process']['export'], $fileNameMa);
                $archiveFilePathMa = sprintf('%s/%s', $this->folders['archive']['export'], $fileNameMa); */

//        if(file_exists($archiveFilePath)) {
//            //TODO: log: file already generated and sent, no further actions required
//            return;
//        }

        //generate csv file
        //dump($processFilePathMa);
        //$exported = Service\Export::getInstance(['file' => $processFilePath/* ,'Mafile' => $processFilePathMa */])->process();
        //dump('$exported',$exported);
        //if(!$exported) {
        //TODO: log
        //return;
        //}

        $processFilePath = SigmaExport::process();

        if (null != $processFilePath) {

            //send csv file
            $mailService = Service\Mail::getInstance()->setFileExport($processFilePath);
            dump('$mailService', $mailService);
            $mailService->processSend();
        }

    }

    public function processSendGenerated() 
    {
        dump('processSendGenerated');
        $processFilePathPattern = sprintf('%s/*.csv', $this->folders['process']['export']);
        foreach (glob($processFilePathPattern) as $processFilePath) {
            $fileName = basename($processFilePath);
            $archiveFilePath = sprintf('%s/%s', $this->folders['archive']['export'], $fileName);

            //send csv file
            $mailService = Service\Mail::getInstance()->setFileExport($processFilePath);
            $mailService->processSend();

            //move file to archive
            rename($processFilePath, $archiveFilePath);
        }
    }


    /**
     * Check email
     * Get csv attachment from email
     * Parse cvs
     * Update client data
     */
    public function processReceive()
    {
        if (!$this->enabled) return;

        $fileNameTemplate = sprintf('import.%s.on.%%s.csv', date('Y-m-d'));
        $processFilePathTemplate = sprintf('%s/%s', $this->folders['process']['import'], $fileNameTemplate);

        //check mailbox and download csv attachment
        $mailService = Service\Mail::getInstance()->setFileImportTemplate($processFilePathTemplate);
        dump('processReceive$mailService', $mailService);
        $mailService->processReceive();

        $importService = Service\Import::getInstance();

        dump($importService);
        //import all downloaded files
        $files = glob(sprintf('%s/*.csv', $this->folders['process']['import']));
        foreach ($files as $processFilePath) {
            //import csv data
            //$importService->setFile($processFilePath)->process();

            //move file to archive
            //$archiveFilePath = sprintf('%s/%s', $this->folders['archive']['import'], basename($processFilePath));
            //rename($processFilePath, $archiveFilePath);
			
			SigmaImport::process($processFilePath);
			
        }

        dump('END _ processReceive');

		$hour=(int)date('H');		
		if($hour==6 || $hour==10 || $hour==13 || $hour==15 || $hour==17){
			$this->processGenerate();
			 dump('END _ processExport');
		}	
		
       
    }

    /**
     * Change status for requests waiting for clarification that were created week ago
     */
    public function processClarificationTimeout()
    {
        if (!$this->enabled) return;

        $clarificationTimeoutService = Service\ClarificationTimeout::getInstance();
        $clarificationTimeoutService->process();
    }

    public function checkDuplicateRequestList($creditRequestIdList = [])
    {
        if (!is_array($creditRequestIdList) || empty($creditRequestIdList)) return false;

        $list = CollectionCreditRequestStatus::getInstance()->getList([
            'select' => [
                'COUNT(`anfrageid`) AS count',
                'anfrageid',
                'id'
            ],
            'filter' => [
                'anfrageid' => $creditRequestIdList,
                'stid_neu' => ModelCreditRequest::STATUS_WDV_SK,
                'datum' => ['betweenRaw' => ['DATE_SUB( NOW() , INTERVAL 28 DAY)', 'NOW()']]
            ],
            'group' => ['anfrageid']
        ])->toArray();

        $result = [];
        foreach ($list as $item) {
            $result[$item['anfrageid']] = ((int)$item['count'] > 1) ? true : false;
        }

        $resultKeys = array_keys($result);
        foreach ($creditRequestIdList as $creditRequestId) {
            if (!in_array($creditRequestId, $resultKeys)) {
                $result[$creditRequestId] = false;
            }
        }

        return $result;
    }

}