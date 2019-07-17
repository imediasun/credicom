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
use App\BonKredit;
use App\Http\ArraysClass;
use App\Services\GetCsvService;

class GetCsvFromFtp extends GetCsvService{
    use Singleton;

    public $enabled = false;
	public $ftp_server;
	public $ftp_port;
	public $ftp_user_name;
	public $ftp_user_pass;
    public $folders;
	public $local_directory ;
	public $entity=[];
	public $filename=[];
	public $help=true;
	public $config;
	
	public $csvKeys = ['referenznr',
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
	
 /*      public function __construct(array $items = array())
    {
        $this->init();
    } */

	public function helper($value){
		$pattern = "/^[^0-9]*/";
 
		$value['str_nr'] = preg_replace($pattern, "", $value['str_str_nr']);
		$value['str'] = str_replace($value['str_nr'], "", $value['str_str_nr']);

		$value['str_nr1'] = preg_replace($pattern, "", $value['str1_str_nr1']);
		$value['str1'] = str_replace($value['str_nr1'], "", $value['str1_str_nr1']);
			switch($value['beruf']){
				case 1:
				$value['beruf']=6;
				break;
				case 2:
				$value['beruf']=29;
				break;
				case 3:
				$value['beruf']=31;
				break;
				case 4:
				$value['beruf']=14;
				break;
				case 5:
				$value['beruf']=33;
				break;
				case 6:
				$value['beruf']=16;
				break;
				case 7:
				$value['beruf']=7;
				break;
				case 9:
				$value['beruf']=19;
				break;
				
				
			}
			if($value['beruf1']!==""){
			switch($value['beruf1']){
				case 1:
				$value['beruf1']=6;
				break;
				case 2:
				$value['beruf1']=29;
				break;
				case 3:
				$value['beruf1']=31;
				break;
				case 4:
				$value['beruf1']=14;
				break;
				case 5:
				$value['beruf1']=33;
				break;
				case 6:
				$value['beruf1']=16;
				break;
				case 7:
				$value['beruf1']=7;
				break;
				case 9:
				$value['beruf1']=19;
				break;
				
				
			}
			}else{
			$value['beruf1']=0;	
			}
			switch($value['famstand']){
				case 'ledig':
				$value['famstand']=1;
				break;
				case 'verheiratet':
				$value['famstand']=2;
				break;
				case 'verwitwet':
				$value['famstand']=3;
				break;
				case 'geschieden':
				$value['famstand']=4;
				break;
				case 'getrennt lebend':
				$value['famstand']=5;
				break;
				case 'Lebensgemeinschaft':
				$value['famstand']=6;
				break;
					
				
			}
			if($value['famstand1']!==""){
			switch($value['famstand1']){
				case 'ledig':
				$value['famstand1']=1;
				break;
				case 'verheiratet':
				$value['famstand1']=2;
				break;
				case 'verwitwet':
				$value['famstand1']=3;
				break;
				case 'geschieden':
				$value['famstand1']=4;
				break;
				case 'getrennt lebend':
				$value['famstand1']=5;
				break;
				case 'Lebensgemeinschaft':
				$value['famstand1']=6;
				break;
					
				
			}
	}else{
		$value['famstand1']=0;
	}
	
			if($value['anr1']!==""){
			switch($value['anr1']){
				case 'Frau':
				$value['anr1']=1;
				break;
				case 'Herr':
				$value['anr1']=2;
				break;
				case 1:
				$value['anr1']=1;
				break;
				case 2:
				$value['anr1']=2;
				break;
						
				
			}
	}else{
		$value['anr1']=0;
	}
				if($value['anr']!==""){
			switch($value['anr']){
				case 'Frau':
				$value['anr']=1;
				break;
				case 'Herr':
				$value['anr']=2;
				break;
				case '1':
				$value['anr']=1;
				break;
				case '2':
				$value['anr']=2;
				break;
				case 1:
				$value['anr']=1;
				break;
				case 2:
				$value['anr']=2;
				break;
						
				
			}
	}else{
		$value['anr']=0;
	}
	
	return $value;
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
        $this->config=$globalConfig['csv_sftp'];
        $this->enabled = $globalConfig['csv_sftp']['enabled'];
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
        $config = $this->config;
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



  

}