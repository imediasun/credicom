<?php

namespace App\modules\Sigma\Model\Service;

//traits
use App\Http\ArraysClass;
use \App\modules\Core\Model\Traits\Singleton;
//models
use \App\modules\Core\Model\Base as BaseModel;
use \App\modules\CreditRequest\Model\CreditRequest as ModelCreditRequest;
//collections
use \App\modules\CreditRequest\Collection\CreditRequest as CollectionCreditRequest;
//vendor
use \League\Csv\Writer as CsvWriter;

use PDO;

class Export extends BaseModel
{
    use Singleton;

    public function process()
    {
        $fileName = $this->getFile();
		//$fileNameMa = $this->getMafile();

        //get data
        $data = $this->getExportData();
        if(!count($data)) {
            //TODO: log [no data to export, return]
            return false;
        }

        //save data
        $csv = CsvWriter::createFromPath($fileName, "w");
        $csv->setDelimiter(';');
        //$csv->insertOne($this->getExportDataHeader()); //header
        $csv->insertAll($data);

        return true;
    }

    public function getExportDataHeader()
    {

        return [
            'nachname' => 'Nachname',
            'vorname' => 'Vorname',
            'geb_dat' => 'Geb.-Datum',

//            'anr' => 'Anrede',
//            'str' => 'Straße',
//            'plz' => 'PLZ',
//            'ort' => 'Ort',
//            'netto' => 'Einkommen',
//            'arbeitgeber_seit' => 'seit wann beschäftigt',
//            'kreditbetrag' => 'Kredithöhe',
//            'status' => 'Status',
//            'staat' => '',
//            'famstand' => '',
//            'telefon' => '',
//            'handy' => '',
//            'email' => '',
//            'beruf' => '',
//            'anstellung_als' => '',
//            'kinder' => '',
//            'arbeitgeber' => '',
//            'arbeitgeber_plz' => '',
//            'arbeitgeber_ort' => '',
//            'kto' => '',
//            'blz' => '',
        ];
    }

    public function getExportData()
    {
        global $array_status_kreditanfragen, $laender, $array_famstand, $array_berufe;
        $config=new ArraysClass();
        //$options
        $optionsCountry = array_flip($config->laender);

        $optionsBeruf = [];
        foreach($config->array_berufe as $key => $value) $optionsBeruf[$value['id']] = $value['bez1'];

        $optionFamstand = [];
        foreach($config->array_famstand as $key=>$value) $optionFamstand[$value['id']] = $value['bez'];

        $optionsCreditRequestStatus = [];
        foreach($config->array_status_kreditanfragen as $key=>$value) $optionsCreditRequestStatus[$value['id']] = $value['bez'];


        $result = [];

        //filter
        $filter = [
            'csv_sigma_abaco_view' => '0',
            'status_intern' => ModelCreditRequest::STATUS_WDV_SK,
        ];

		
        //load data
        $collection = CollectionCreditRequest::getInstance();
        dump('$filter 100 Export',$filter);
        $data = $collection->getList(['filter' => $filter]);
        dump('Data after getList',$data);
        //map data
        foreach($data as $item) {
            $exportItem = [];
            $exportItem['nachname'] = iconv('UTF-8', "iso-8859-1", utf8_encode(trim($item['nachname'])));
            $exportItem['vorname'] =  iconv('UTF-8', "iso-8859-1", utf8_encode(trim($item['vorname'])));
            $exportItem['geb_dat'] = ($item['gebdat'] != '0000-00-00') ? date("d.m.Y",strtotime($item['gebdat'])) : null;

//            $exportItem['anr'] = ($item['anr']==1) ? 'Herr' : 'Frau';
//            $exportItem['str']=$item['str'].' '.$item['str_nr'];
//            $exportItem['plz']=$item['plz'];
//            $exportItem['ort']=$item['ort'];
//            $exportItem['netto']=$item['netto']+$item['nebeneinkommen_mtl'];
//            $exportItem['arbeitgeber_seit']= ($item['anstellung'] != '0000-00-00') ? date("d.m.Y",strtotime($item['anstellung'])) : null;
//            $exportItem['kreditbetrag']=$item['kreditbetrag'];
//            $exportItem['status'] = isset($optionsCreditRequestStatus[$item['status_intern']]) ? $optionsCreditRequestStatus[$item['status_intern']] : null;
//            $exportItem['staat'] = isset($optionsCountry[$item['staat']]) ? $optionsCountry[$item['staat']] : null;
//            $exportItem['famstand'] = isset($optionFamstand[$item['famstand']]) ? $optionFamstand[$item['famstand']] : null;
//            $exportItem['telefon']=implode('-', array_filter([$item['telefonv'],$item['telefon']]));
//            $exportItem['handy']=implode('-', array_filter([$item['handyv'],$item['handy']]));
//            $exportItem['email']=$item['email'];
//            $exportItem['beruf'] = isset($optionsBeruf[$item['beruf']]) ? $optionsBeruf[$item['beruf']] : null;
//            $exportItem['anstellung_als']=$item['anstellung_als'];
//            $exportItem['kinder']=$item['kinder'];
//            $exportItem['arbeitgeber']=$item['arbeitgeber'];
//            $exportItem['arbeitgeber_plz']=$item['arbeitgeber_plz'];
//            $exportItem['arbeitgeber_ort']=$item['arbeitgeber_ort'];
//            $exportItem['kto']=$item['kto'];
//            $exportItem['blz']=$item['blz'];

            $result[] = $exportItem;
        }
		//dd($result);
		
		    //filter
        $filterMa = [
            'csv_sigma_abaco_view_ma' => '0',
            'status_intern' => ModelCreditRequest::STATUS_WDV_SKMA,
        ];
        //load data
        $dataMa = $collection->getList(['filter' => $filterMa]);
		
		      foreach($dataMa as $item) {
            $exportItem = [];
            $exportItem['nachname'] = iconv('UTF-8', "iso-8859-1", utf8_encode(trim($item['nachname1'])));
            $exportItem['vorname'] =  iconv('UTF-8', "iso-8859-1", utf8_encode(trim($item['vorname1'])));
            $exportItem['geb_dat'] = ($item['gebdat'] != '0000-00-00') ? date("d.m.Y",strtotime($item['gebdat1'])) : null;
            $result[] = $exportItem;
        }
		
		//dd($result);
		
		
		
        //mark found items as exported
        $updateQuery = sprintf(
            "UPDATE %s SET `csv_sigma_abaco_view`='1' WHERE id IN (%s)",
            $collection->table,
            implode(',', array_keys($data->toArray()))
        );
        //$this->conn = new PDO("mysql:host=".env('DB_HOST').";dbname=".env('DB_DATABASE')."", env('DB_USERNAME'),  env('DB_PASSWORD', ''));
		$this->conn = new PDO("mysql:host=".env('TUNNELER_LOCAL_ADDRESS').";port=".env('TUNNELER_LOCAL_PORT').";dbname=".env('DB_DATABASE')."", env('DB_USERNAME'),  env('DB_PASSWORD', ''));
        $this->conn->query($updateQuery);
        //mysqli_query($aVar,$updateQuery);
		
		 //mark found items as exported
        $updateQueryMa = sprintf(
            "UPDATE %s SET `csv_sigma_abaco_view_ma`='1' WHERE id IN (%s)",
            $collection->table,
            implode(',', array_keys($dataMa->toArray()))
        );
        //$this->conn = new PDO("mysql:host=".env('DB_HOST').";dbname=".env('DB_DATABASE')."", env('DB_USERNAME'),  env('DB_PASSWORD', ''));
        $this->conn = new PDO("mysql:host=".env('TUNNELER_LOCAL_ADDRESS').";port=".env('TUNNELER_LOCAL_PORT').";dbname=".env('DB_DATABASE')."", env('DB_USERNAME'),  env('DB_PASSWORD', ''));
 	$this->conn->query($updateQueryMa);
		
dump('$result',$result);
        return $result;
    }
	
	
}