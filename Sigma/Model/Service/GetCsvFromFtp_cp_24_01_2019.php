<?php
namespace App\modules\Sigma\Model\Service;

use App\modules\Core\Model\Traits\Singleton;
//models
use \App\modules\Core\Model\Base as BaseModel;
use \App\modules\Core\Model\Registry;
use \App\modules\Core\Model\Utils;
use \League\Csv\Reader as CsvReader;
use Log;
use \App\modules\CreditRequest\Model\Service\CreditRequestForm\CSV as CsvServiceCreditRequest;
use \App\modules\CreditRequest\Collection\CreditRequest as CollectionCreditRequest;
use \App\modules\CreditRequest\Model\Service\Mail as CreditRequestMailService;
use \App\modules\CreditRequest\Block\Mail\SendCsvErrorNotification as SendCsvErrorNotification;
use \phpseclib\Net\SFTP;
use Illuminate\Support\Carbon;
use App\Http\ArraysClass;
use App\BonKredit;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class GetCsvFromFtp_cp_24_01_2019 extends BaseModel{
    use Singleton;

    public $enabled = false;
	public $ftp_server;
	public $ftp_port;
	public $ftp_user_name;
	public $ftp_user_pass;
    public $folders;
	public $local_directory ;
	public $csv_filename ;
	public $entity=[];
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
		$this->storage_directory= base_path()."/storage/app/public/";
    }


    public function initSftp()
    {
		
		Log::info('Work of Credicom crone ERROR: '.date("Y-m-d H:i:s").
			'initSftp()'); 
        if(!$this->enabled) return;
        $config = $this->getConfig();
/* Set the correct include path to 'phpseclib'. Note that you will need 
   to change the path below depending on where you save the 'phpseclib' lib.
   The following is valid when the 'phpseclib' library is in the same 
   directory as the current file.
 */
set_include_path(get_include_path() . PATH_SEPARATOR . './phpseclib0.3.0');
Log::info('Work of Credicom crone ERROR: '.date("Y-m-d H:i:s").
			'initSftp(1)'); 
 
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
Log::info('Work of Credicom crone ERROR: '.date("Y-m-d H:i:s").
			'initSftp(2)'.print_r($x,true)); 
//Loop through `x` and then download the file using.
//$result = $sftp->get($x); // Normally I use the string information that is returned and then download using
//dump($result);

    /* This is the correct way to loop over the directory. */
   foreach($x as $file){
        if ($file != "." && $file != "..") 
        {
            $files_to_download[] = $file;
			$file_name=pathinfo($file, PATHINFO_FILENAME);;
			$file_extension=pathinfo($file, PATHINFO_EXTENSION);;
			$_file=$file_name.time().".".$file_extension;
			$sftp->get($file, $this->local_directory.$_file);
			$sftp->get($file, $this->storage_directory.$_file);
			$this->filename[]=$_file;
			$sftp->delete($file, false);
			$bon_kredit=new BonKredit();

			$bon_kredit->file=$_file;
			$bon_kredit->datum=Carbon::now();
			$bon_kredit->description='file_laoded_successfuly';
			$bon_kredit->save();
			Log::info('BonKredit1: '.date("Y-m-d H:i:s").
			''.print_r($bon_kredit,true));
        }
    } 

//file_put_contents($root, $result);
 
    }


    public function processCSV()
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
		Log::info('Work of Credicom crone ERROR: '.date("Y-m-d H:i:s").
			'count($csvKeys)'.count($csvKeys)); 
        $result = [];
		dump('$csv->fetchAssoc()',$csv->fetchAssoc());
		dump($csv_array);
        //dump('$csv->fetchAssoc($csvKeys)',$csv->fetchAssoc($csvKeys));
		Log::info('Work of Credicom crone ERROR: '.date("Y-m-d H:i:s").
			'count($csv_array)'.count($csv_array));
		foreach ($csv_array as $string){
			
			$string_to_array=explode(",",$string);
			dump(count($string_to_array));
			Log::info('Work of Credicom crone ERROR: '.date("Y-m-d H:i:s").
			'count($string_to_array)'.count($string_to_array));
			if(count($string_to_array)>count($csvKeys)){
				$res=count($string_to_array)-count($csvKeys);
				Log::info('Work of Credicom crone ERROR: '.date("Y-m-d H:i:s").
			'count($$res)'.$res);
				$string_to_array = array_splice($string_to_array,0, -$res);
				Log::info('Work of Credicom crone ERROR: '.date("Y-m-d H:i:s").
			'count($string_to_array)2'.count($string_to_array));
			}
			$c[] = array_combine($csvKeys, $string_to_array);
			
		}
	}
	
	if(!isset($c)){
	return ;
	}
		dump($c);
		Log::info('Work of Credicom crone ERROR: '.date("Y-m-d H:i:s").
			'$c=>'.print_r($c,true));
		$this->actionPutCSVToDB($c);
        dump('END _ processReceive');
    }
	
	public function actionPutCSVToDB($csv){
		$i=0;
		foreach($csv as $value){

		try{
			$this->componate($value);
		}
		catch(\Exception $e){
		if($i==0){
			//Если первая строка в csv создать для записи error_csv
		
        $this->csv_filename = base_path('files/csv_upload/error'.time().".csv");
       header("Content-Type: application/csv");
		header("Content-Disposition:attachment;filename=".$this->csv_filename);
		$fd = fopen ($this->csv_filename, "wb");
		$str_value=implode(",", $value);
        fputs($fd,$str_value);
        fclose($fd);
       
		}
		else{
			//Добавить текущую строку в error_csv
		header("Content-Type: application/csv");
		header("Content-Disposition:attachment;filename=".$this->csv_filename);
		$fd = fopen ($this->csv_filename, "a");
		$str_value=implode(",", $value);
		$contents="\n";
		$contents.=$str_value;
		fwrite($fd, $contents, strlen($contents));
        //fwrite($fd,'\n'.$str_value);
		fclose($fd);
		} 
		
		
		
		
		$log = ['date' => date("Y-m-d H:i:s"),
				'vorname'=>mb_convert_encoding(trim($value['vorname']), 'UTF-8', mb_detect_encoding(trim($value['vorname']), 'UTF-8, ISO-8859-1', true)),
				'nachname'=>mb_convert_encoding(trim($value['nachname']), 'UTF-8', mb_detect_encoding(trim($value['nachname']), 'UTF-8, ISO-8859-1', true)),
				'error' => $e];

		$orderLog = new Logger('files');
		$orderLog->pushHandler(new StreamHandler(storage_path('logs/csv_import.log')), Logger::INFO);
		$orderLog->info('CsvImportLog', $log);
		
		$notificationRecipient = new BaseModel([
            'email' => 'imediasun@gmail.com',//
        ]);
        $mailService = CreditRequestMailService::getInstance();
        $mailBlock = new SendCsvErrorNotification([
            'error' => $e,
			'vorname'=>mb_convert_encoding(trim($value['vorname']), 'UTF-8', mb_detect_encoding(trim($value['vorname']), 'UTF-8, ISO-8859-1', true)),
			'nachname'=>mb_convert_encoding(trim($value['nachname']), 'UTF-8', mb_detect_encoding(trim($value['nachname']), 'UTF-8, ISO-8859-1', true)),
            'sender' => $mailService->getSender(),
            'recipient' => $notificationRecipient
        ]);

        $mailSendResult = $mailService->send($mailBlock);
		
		$i++;
		continue;
		}
		

		}
	}
	
	private function componate($value){
				$pattern = "/^[^0-9]*/";
 
		$value['str_nr'] = preg_replace($pattern, "", $value['str_str_nr']);
		$value['str'] = str_replace($value['str_nr'], "", $value['str_str_nr']);

		$value['str_nr1'] = preg_replace($pattern, "", $value['str1_str_nr1']);
		$value['str1'] = str_replace($value['str_nr1'], "", $value['str1_str_nr1']);
		
		if(isset($value['beruf']) && $value['beruf']!==""){
			switch($value['beruf']){
				case 1:
				$beruf=6;
				break;
				case 2:
				$beruf=29;
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

				
				
		}}else{
			$beruf=33;	
			}
			if(isset($value['beruf1']) && $value['beruf1']!==""){
			switch($value['beruf1']){
				case 1:
				$beruf1=6;
				break;
				case 2:
				$beruf1=29;
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
			$beruf1=33;	
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
				$anr1=2;
				break;
				case 'Herr':
				$anr1=1;
				break;
				case 1:
				$anr1=1;
				break;
				case 2:
				$anr1=2;
				break;
						
				
			}
	}else{
		$anr1=0;
	}
				if($value['anr']!==""){
			switch($value['anr']){
				case 'Frau':
				$anr=2;
				break;
				case 'Herr':
				$anr=1;
				break;
				case '1':
				$anr=1;
				break;
				case '2':
				$anr=2;
				break;
				case 1:
				$anr=1;
				break;
				case 2:
				$anr=2;
				break;
						
				
			}
	}else{
		$anr=0;
	}

$at_array=explode('.',$value['anstellung']);
$at1_array=explode('.',$value['anstellung1']);
	$gebdat_array=explode('.',$value['gebdat']);
	$gebdat1_array=explode('.',$value['gebdat1']);
	$wohnhaft_seit_array=explode('.',$value['wohnhaft_seit']);

		
		$this->entity['referenznr']=$value['referenznr'];
		$this->entity['betrag']=$value['kreditbetrag'];
		$this->entity['rate']=$value['mtl_rate'];
		$this->entity['coapplicant_same_household']=($value['gesamtbetrachtung']==1) ? 2 : 1;
		$this->entity['anrede']=$anr;
		$this->entity['vorname']=$value['vorname'];
		$this->entity['nachname']=mb_convert_encoding(trim($value['nachname']), 'UTF-8', mb_detect_encoding(trim($value['nachname']), 'UTF-8, ISO-8859-1', true));
		$this->entity["geb_jahr"] =$gebdat_array[2]; 
		$this->entity["geb_monat"] =$gebdat_array[1]; 
		$this->entity["geb_tag"]=$gebdat_array[0];
		$this->entity['gebdat']=date('Y-m-d', strtotime($value['gebdat']));
		$this->entity['geb_ort']=$value['geb_ort'];
		$this->entity['plz']=$value['plz'];
		$this->entity['ort']=mb_convert_encoding(trim($value['ort']), 'UTF-8', mb_detect_encoding(trim($value['ort']), 'UTF-8, ISO-8859-1', true));
		$this->entity['str']=$value['str'];
		$this->entity['str_nr']=$value['str_nr'];
		$this->entity['land']=$value['land'];
		$this->entity['wohnhaft_seit']=date('Y-m-d', strtotime($value['wohnhaft_seit']));
		$this->entity["resident_since_year"]=(isset($wohnhaft_seit_array[2]) ) ? $wohnhaft_seit_array[2] : null;
		$this->entity["resident_since_month"]=(isset($wohnhaft_seit_array[1]) ) ? $wohnhaft_seit_array[1] : null;
		$this->entity["resident_since_day"]=(isset($wohnhaft_seit_array[0]) ) ? $wohnhaft_seit_array[0] : null;
		$this->entity['staat']=$value['staat'];
		$this->entity['tel']=$value['telefon'];
		$this->entity['handy']=$value['handy'];
		$this->entity['mail']=$value['email'];
		$this->entity['famstand']=$famstand;
		$this->entity['kinder']=$value['kinder'];
		$this->entity['beruf']=$beruf;
		$this->entity['befristet']=($value['arbeit_befristet']=0) ? 1 : 0;
		$this->entity['anstellung']=$value['anstellung'];
		
		$this->entity["at_jahr"] =(isset($at_array[2])) ? $at_array[2] : null;
		$this->entity["at_monat"] =(isset($at_array[1])) ? $at_array[1] :null; 
		$this->entity["at_tag"]=(isset($at_array[0])) ? $at_array[0] : null;
		
		
		$this->entity['arbeitgeber']=$value['arbeitgeber'];
		$this->entity['arbeitgeber_plz']=$value['arbeitgeber_plz'];
		$this->entity['arbeitgeber_ort']=mb_convert_encoding(trim($value['arbeitgeber_ort']), 'UTF-8', mb_detect_encoding(trim($value['arbeitgeber_ort']), 'UTF-8, ISO-8859-1', true));
		$this->entity['anstellung_als']=$value['anstellung_als'];
		$this->entity['netto']=$value['netto'];
		$this->entity['nebeneinkommen']=($value['nebeneinkommen']=='Nein') ? 0 : 1;
		$this->entity['nebeneinkommen_mtl']=$value['nebeneinkommen_mtl'];
		$this->entity['rental_fee']=$value['miete'];
		$this->entity['unterhalt']=$value['unterhalt'];
		$this->entity['eigentum']=$value['eigentum'];
		$this->entity['eigentum_belastung_mtl']=$value['eigentum_belastung_mtl'];
		$this->entity['coapplicant_enabled']=($value['is_coapplicant']=="M") ? 1 : 0;
		$this->entity['coapplicant_anrede']=$anr1;
		$this->entity['coapplicant_vorname']=$value['vorname1'];
		$this->entity['coapplicant_nachname']=$value['nachname1'];
		$this->entity["coapplicant_geb_jahr"]=(isset($gebdat1_array[2])) ? $gebdat1_array[2] :null ;
		$this->entity["coapplicant_geb_monat"]=(isset($gebdat1_array[1])) ? $gebdat1_array[1] :null;
		$this->entity["coapplicant_geb_tag"]=(isset($gebdat1_array[0])) ? $gebdat1_array[0] :null;
		//$this->entity['gebdat1']=date('Y-m-d', strtotime($value['gebdat1']));
		$this->entity['coapplicant_geb_ort']=$value['geb_ort1'];
		$this->entity['coapplicant_plz']=$value['plz1'];
		$this->entity['coapplicant_ort']=mb_convert_encoding(trim($value['ort1']), 'UTF-8', mb_detect_encoding(trim($value['ort1']), 'UTF-8, ISO-8859-1', true));
		$this->entity['coapplicant_str']=$value['str1'];
		$this->entity['coapplicant_str_nr']=$value['str_nr1'];
		$this->entity['land1']=$value['land1'];
		$this->entity['wohnhaft_seit1']=date('Y-m-d', strtotime($value['wohnhaft_seit1']));
		$this->entity['coapplicant_staat']=$value['staat1'];
		$this->entity['coapplicant_famstand']=$famstand1;
		$this->entity['kinder1']=($value['kinder1'] > 0 ) ? $value['kinder1'] : 0;
		$this->entity['coapplicant_beruf']=$beruf1;
		$this->entity['coapplicant_befristet']=($value['arbeit_befristet1']!=="") ? (($value['arbeit_befristet1']=0) ? 1 : 0) : 0;
		$this->entity['anstellung1']=date('Y-m-d', strtotime($value['anstellung1']));
		if(isset($value['anstellung1'])&& $value['anstellung1']!==""){
		$at1_array=explode('.',$value['anstellung1']);
		$this->entity["coapplicant_at_jahr"] =(isset($at1_array[2])) ? $at1_array[2] :null; 
		$this->entity["coapplicant_at_monat"] =(isset($at1_array[1])) ? $at1_array[1] : null; 
		$this->entity["coapplicant_at_tag"]=(isset($at1_array[0])) ? $at1_array[0] : null;
		}
		$this->entity['coapplicant_arbeitgeber']=$value['arbeitgeber1'];
		$this->entity['coapplicant_arbeitgeber_plz']=$value['arbeitgeber_plz1'];
		$this->entity['coapplicant_arbeitgeber_ort']=mb_convert_encoding(trim($value['arbeitgeber_ort1']), 'UTF-8', mb_detect_encoding(trim($value['arbeitgeber_ort1']), 'UTF-8, ISO-8859-1', true));
		$this->entity['coapplicant_anstellung_als']=$value['anstellung_als1'];
		$this->entity['coapplicant_netto']=$value['netto1'];
		$this->entity['coapplicant_additional_revenue_enabled']=($value['nebeneinkommen1']!=="") ? (($value['nebeneinkommen1']=='Nein') ? 0 : 1) : 0;
		$this->entity['coapplicant_additional_revenue']=$value['nebeneinkommen_mtl1'];
		$this->entity['coapplicant_unterhalt']=$value['unterhalt1'];
		if($this->entity['coapplicant_unterhalt']>0){$this->entity["coapplicant_unterhalt_enabled"]=1;}else{$this->entity["coapplicant_unterhalt_enabled"]=0;}
		if($this->entity['unterhalt']>0){$this->entity["unterhalt_enabled"]=1;}else{$this->entity["unterhalt_enabled"]=0;}
		$this->entity['status_intern']=0;
		if($value['miete']=="0" && $value['eigentum']!=="0"){
			dump('popal');
			$this->entity['resident_type']=2 ;
		}
		elseif($value['miete']=="0" && $value['eigentum']=="0" && $value['eigentum_belastung_mtl']=="0"){
			$this->entity['resident_type']=4 ;
		}
		elseif($value['miete']!=="0" ){
			$this->entity['resident_type']=1 ;
		}
		
		$this->entity["bank_account_type"]=null;
		$this->entity["kto"]=null;
		$this->entity["blz"]=null;
		$this->entity['intended_use']=0;
		$this->entity['preferredCallTime']=0;
		$this->entity['resident_owned_propery_type']=0;
		$this->entity['resident_owned_total_value_approx']=0;
		$this->entity['resident_owned_total_load_approx']=0;
		$this->entity['resident_owned_property_approx']=0;
		$this->entity['resident_owned_property_rental_income']=0;
		$this->entity['own_residential_property']=0;
		$this->entity["additional_revenue_enabled"]=0;
		$this->entity["coapplicant_additional_revenue_enabled"]=0;
		
		$this->entity["unterhalt_enabled"]=0;
		$this->entity["coapplicant_unterhalt_enabled"]=0;
		
		$this->entity['kreditkarte']=0;
		
		
		
		
		$collectionCreditRequest = CollectionCreditRequest::getInstance();
        $entityCreditRequest = $collectionCreditRequest->emptyLoad();
		$CsvServiceCreditRequest = CsvServiceCreditRequest::getInstance();
        $CsvServiceCreditRequest->setCreditRequest($entityCreditRequest)->setFormEntity($this->entity); 
        $reply = $CsvServiceCreditRequest->processDataForm();
	}

  

}