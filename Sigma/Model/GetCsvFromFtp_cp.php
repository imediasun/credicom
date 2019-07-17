<?php
namespace App\modules\Sigma\Model;

use App\modules\Core\Model\Traits\Singleton;
//models
use \App\modules\Core\Model\Base as BaseModel;
use \App\modules\Core\Model\Registry;
use \App\modules\Core\Model\Utils;
use \League\Csv\Reader as CsvReader;

use \App\modules\CreditRequest\Model\Service\CreditRequestForm\CSV as CsvServiceCreditRequest;
use \App\modules\CreditRequest\Collection\CreditRequest as CollectionCreditRequest;
//vendor
use \phpseclib\Net\SFTP;

use App\Http\ArraysClass;

class GetCsvFromFtp_cp extends BaseModel{
    use Singleton;

    public $enabled = false;
	public $ftp_server;
	public $ftp_port;
	public $ftp_user_name;
	public $ftp_user_pass;
    public $folders;
	public $local_directory ;
	
	public $filename=[];
      public function __construct(array $items = array())
    {
        parent::__construct($items);
        $this->init();
    }

    public function init()
    {
        $this->initConfig();
        $this->initSftp();
    }


    public function initConfig() {
        $globalConfig = Registry::getInstance()->getConfig();
        $globalConfig=new ArraysClass();
        $globalConfig=$globalConfig->conf;
        $this->setConfig(Utils::arrayToModel($globalConfig['csv_sftp']));
        $this->enabled = $this->getConfig()->getEnabled();
		$this->remote_directory = $globalConfig['csv_sftp']['remoteBaseDir'];
		$this->ftp_server=$globalConfig['csv_sftp']['host'];
		$this->ftp_port=$globalConfig['csv_sftp']['port'];
		$this->ftp_user_name=$globalConfig['csv_sftp']['user'];
		$this->ftp_user_pass=$globalConfig['csv_sftp']['pass'];
		
		$this->local_directory= base_path()."/files/csv_upload/";
    }


    public function initSftp()
    {
        if(!$this->enabled) return;
        $config = $this->getConfig();
/* Set the correct include path to 'phpseclib'. Note that you will need 
   to change the path below depending on where you save the 'phpseclib' lib.
   The following is valid when the 'phpseclib' library is in the same 
   directory as the current file.
 */
set_include_path(get_include_path() . PATH_SEPARATOR . './phpseclib0.3.0');

 
/* Change the following directory path to your specification */

 
/* Add the correct FTP credentials below */
$sftp = new SFTP($this->ftp_server.":".$this->ftp_port);
if (!$sftp->login($this->ftp_user_name, $this->ftp_user_pass)) 
{
    exit('Login Failed');
} 
 
 $sftp->pwd(); // This will show you are in the root after connection.
$f=$sftp->chdir($this->remote_directory); // this will go inside the test directory.
$get_path = $sftp->pwd();
//If you want to download multiple data, use
$x = $sftp->nlist();
dump($x);
//Loop through `x` and then download the file using.
//$result = $sftp->get($x); // Normally I use the string information that is returned and then download using
//dump($result);

    /* This is the correct way to loop over the directory. */
   foreach($x as $file){
        if ($file != "." && $file != "..") 
        {
            $files_to_download[] = $file;
			$sftp->get($file, $this->local_directory.$file);
			$this->filename[]=$file;
			$sftp->delete($file, false);
        }
    } 

//file_put_contents($root, $result);
 
    }


    public function processReceive()
    {
        if(!$this->enabled) return;
		foreach($this->filename as $file){
		$csv = CsvReader::createFromPath($this->local_directory.$file, 'r');
		$csv->setDelimiter(';');
		
		foreach ($csv as $key=>$record) {
			
			foreach($record as $set=>$value){
				if($record[$set]!==""){
			$csv_array[$key]=$value;
			}
			}
			
			
   
} 


		$csvKeys = ['referenznr',
		'kreditbetrag',
		'ignored_1',
		'mtl_rate',
		'gesamtbetrachtung',
		'is_applicant',
		'anr',
		'vorname',
		'nachname',
		'gebdat',
		'geb_ort',
		'plz',
		'ort',
		'str_str_nr',
		'land',
		'wohnhaft_seit',
		'staat',
		'telefon',
		'handy',
		'email',
		'famstand',
		'kinder',
		'beruf',
		'arbeit_befristet',
		'anstellung',
		'arbeitgeber',
		'arbeitgeber_plz',
		'arbeitgeber_ort',
		'ignored_2',
		'anstellung_als',
		'netto',
		'nebeneinkommen',
		'nebeneinkommen_mtl',
		'miete',
		'ignored_3',
		'unterhalt',
		'eigentum',
		'eigentum_belastung_mtl',
		'ignored_4',
		'is_coapplicant',
		'anr1',
		'vorname1',
		'nachname1',
		'gebdat1',
		'geb_ort1',
		'plz1',
		'ort1',
		'str1_str_nr1',
		'land1',
		'wohnhaft_seit1',
		'staat1',
		'ignored_5',
		'ignored_6',
		'ignored_7',
		'famstand1',
		'kinder1',
		'beruf1',
		'arbeit_befristet1',
		'anstellung1',
		'arbeitgeber1',
		'arbeitgeber_plz1',
		'arbeitgeber_ort1',
		'ignored_13',
		'anstellung_als1',
		'netto1',
		'nebeneinkommen1',
		'nebeneinkommen_mtl1',
		'ignored_8',
		'ignored_9',
		'unterhalt1',
		'ignored_10',
		'ignored_11',
		'ignored_12'];
		dump(count($csvKeys));
        $result = [];
		dump('$csv->fetchAssoc()',$csv->fetchAssoc());
		dump($csv_array);
        //dump('$csv->fetchAssoc($csvKeys)',$csv->fetchAssoc($csvKeys));
		foreach ($csv_array as $string){
			$string_to_array=explode(",",$string);
			dump(count($string_to_array));
			$c[] = array_combine($csvKeys, $string_to_array);
			
		}
	}
		dump($c);
		$this->actionPutCSVToDB($c);
        dump('END _ processReceive');
    }
	
	public function actionPutCSVToDB($csv){
		foreach($csv as $value){
		$enstr=(preg_replace('/ [\d,.]+/s', '', $value['str_str_nr']));
		$value['str_nr'] = str_replace(" ","",str_replace(explode(" ", $enstr), '', $value['str_str_nr']));
        $value['str']=$enstr;
		
		$enstr1=(preg_replace('/ [\d,.]+/s', '', $value['str1_str_nr1']));
		$value['str_nr1'] = str_replace(" ","",str_replace(explode(" ", $enstr1), '', $value['str1_str_nr1']));
        $value['str1']=$enstr1;
			switch($value['beruf']){
				case 1:
				$beruf=6;
				break;
				case 2:
				$beruf=1;
				break;
				case 3:
				$beruf=31;
				break;
				case 4:
				$beruf=14;
				break;
				case 5:
				$beruf=33;
				break;
				case 6:
				$beruf=16;
				break;
				case 7:
				$beruf=7;
				break;
				case 9:
				$beruf=19;
				break;
				
				
			}
			if($value['beruf1']!==""){
			switch($value['beruf1']){
				case 1:
				$beruf1=6;
				break;
				case 2:
				$beruf1=1;
				break;
				case 3:
				$beruf1=31;
				break;
				case 4:
				$beruf1=14;
				break;
				case 5:
				$beruf1=33;
				break;
				case 6:
				$beruf1=16;
				break;
				case 7:
				$beruf1=7;
				break;
				case 9:
				$beruf1=19;
				break;
				
				
			}
			}else{
			$beruf1=0;	
			}
			switch($value['famstand']){
				case 'ledig':
				$famstand=1;
				break;
				case 'verheiratet':
				$famstand=2;
				break;
				case 'verwitwet':
				$famstand=3;
				break;
				case 'geschieden':
				$famstand=4;
				break;
				case 'getrennt lebend':
				$famstand=5;
				break;
				case 'Lebensgemeinschaft':
				$famstand=6;
				break;
					
				
			}
			if($value['famstand1']!==""){
			switch($value['famstand1']){
				case 'ledig':
				$famstand1=1;
				break;
				case 'verheiratet':
				$famstand1=2;
				break;
				case 'verwitwet':
				$famstand1=3;
				break;
				case 'geschieden':
				$famstand1=4;
				break;
				case 'getrennt lebend':
				$famstand1=5;
				break;
				case 'Lebensgemeinschaft':
				$famstand1=6;
				break;
					
				
			}
	}else{
		$famstand1=0;
	}
	
			if($value['anr1']!==""){
			switch($value['anr1']){
				case 'Frau':
				$anr1=1;
				break;
				case 'Herr':
				$anr1=2;
				break;
						
				
			}
	}else{
		$anr1=0;
	}
				if($value['anr']!==""){
			switch($value['anr']){
				case 'Frau':
				$anr=1;
				break;
				case 'Herr':
				$anr=2;
				break;
						
				
			}
	}else{
		$anr=0;
	}
			
		$data=new \App\CreditOrder();
		$data->referenznr=$value['referenznr'];
		$data->kreditbetrag=$value['kreditbetrag'];
		$data->mtl_rate=$value['mtl_rate'];
		$data->gesamtbetrachtung=$value['gesamtbetrachtung'];
		$data->anr=$anr;
		$data->vorname=$value['vorname'];
		$data->nachname=$value['nachname'];
		$data->gebdat=date('Y-m-d', strtotime($value['gebdat']));
		$data->geb_ort=$value['geb_ort'];
		$data->plz=$value['plz'];
		$data->ort=mb_convert_encoding(trim($value['ort']), 'UTF-8', mb_detect_encoding(trim($value['ort']), 'UTF-8, ISO-8859-1', true));
		$data->str=$value['str'];
		$data->str_nr=$value['str_nr'];
		$data->land=$value['land'];
		$data->wohnhaft_seit=date('Y-m-d', strtotime($value['wohnhaft_seit']));
		$data->staat=$value['staat'];
		$data->telefon=$value['telefon'];
		$data->handy=$value['handy'];
		$data->email=$value['email'];
		$data->famstand=$famstand;
		$data->kinder=$value['kinder'];
		$data->beruf=$beruf;
		$data->arbeit_befristet=($value['arbeit_befristet']=0) ? 1 : 0;
		$data->anstellung=$value['anstellung'];
		$data->arbeitgeber=$value['arbeitgeber'];
		$data->arbeitgeber_plz=$value['arbeitgeber_plz'];
		$data->arbeitgeber_ort=mb_convert_encoding(trim($value['arbeitgeber_ort']), 'UTF-8', mb_detect_encoding(trim($value['arbeitgeber_ort']), 'UTF-8, ISO-8859-1', true));
		$data->anstellung_als=$value['anstellung_als'];
		$data->netto=$value['netto'];
		$data->nebeneinkommen=($value['nebeneinkommen']=='Nein') ? 0 : 1;
		$data->nebeneinkommen_mtl=$value['nebeneinkommen_mtl'];
		$data->miete=$value['miete'];
		$data->unterhalt=$value['unterhalt'];
		$data->eigentum=$value['eigentum'];
		$data->eigentum_belastung_mtl=$value['eigentum_belastung_mtl'];
		$data->masteller=($value['is_coapplicant']=="M") ? 1 : 0;
		$data->anr1=$anr1;
		$data->vorname1=$value['vorname1'];
		$data->nachname1=$value['nachname1'];
		$data->gebdat1=date('Y-m-d', strtotime($value['gebdat1']));
		$data->geb_ort1=$value['geb_ort1'];
		$data->plz1=$value['plz1'];
		$data->ort1=mb_convert_encoding(trim($value['ort1']), 'UTF-8', mb_detect_encoding(trim($value['ort1']), 'UTF-8, ISO-8859-1', true));
		$data->str1=$value['str1'];
		$data->str_nr1=$value['str_nr1'];
		$data->land1=$value['land1'];
		$data->wohnhaft_seit1=date('Y-m-d', strtotime($value['wohnhaft_seit1']));
		$data->staat1=$value['staat1'];
		$data->famstand1=$famstand1;
		$data->kinder1=($value['kinder1'] > 0 ) ? $value['kinder1'] : 0;
		$data->beruf1=$beruf1;
		$data->arbeit_befristet1=($value['arbeit_befristet1']!=="") ? (($value['arbeit_befristet1']=0) ? 1 : 0) : 0;
		$data->anstellung1=date('Y-m-d', strtotime($value['anstellung1']));
		$data->arbeitgeber1=$value['arbeitgeber1'];
		$data->arbeitgeber_plz1=$value['arbeitgeber_plz1'];
		$data->arbeitgeber_ort1=mb_convert_encoding(trim($value['arbeitgeber_ort1']), 'UTF-8', mb_detect_encoding(trim($value['arbeitgeber_ort1']), 'UTF-8, ISO-8859-1', true));
		$data->anstellung_als1=$value['anstellung_als1'];
		$data->netto1=$value['netto1'];
		$data->nebeneinkommen1=($value['nebeneinkommen1']!=="") ? (($value['nebeneinkommen1']=='Nein') ? 0 : 1) : 0;
		$data->nebeneinkommen_mtl1=$value['nebeneinkommen_mtl1'];
		$data->unterhalt1=$value['unterhalt1'];
		$data->status_intern=0;
		$data->wohnsituation=($value['miete']==0 && $value['eigentum']!==0) ? 2 : 1;
		
		
		$collectionCreditRequest = CollectionCreditRequest::getInstance();
        $entityCreditRequest = $collectionCreditRequest->emptyLoad();
		$CsvServiceCreditRequest = CsvServiceCreditRequest::getInstance();
        $CsvServiceCreditRequest->setCreditRequest($entityCreditRequest)->setFormEntity($this->entity); 
        $reply = $CsvServiceCreditRequest->processDataForm();
		
		
		
		$data->save();
		}
	}

  

}