<?php
namespace App\modules\CreditRequest\Model\Service;

//traits
use \App\modules\Core\Model\Traits\Singleton;

//models
use \App\modules\Core\Model\Base as BaseModel;
use \App\modules\Core\Model\Registry;
use \App\modules\Core\Model\Utils;

//vendor
use \phpseclib\Net\SFTP;
use App\Http\ArraysClass;
class Epost extends BaseModel
{
    use Singleton;

    public $sftp;
    public $enabled = false;

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
        $this->setConfig(Utils::arrayToModel($globalConfig['api']['epost']));
        $this->enabled = $this->getConfig()->getEnabled();
		$this->testmode = $globalConfig['api']['epost']['testmode'];
		
		$restmodeEpost=\App\Testmode::where('service','epost')->first();
		$this->testmode=($restmodeEpost->testmode==0) ? null : true;
    }


    public function initSftp()
    {
        if(!$this->enabled) return;
		if($this->testmode) return;
        $config = $this->getConfig();

        $this->sftp = new SFTP($config->getHost(),$config->getPort());
        try {
            if(!$this->sftp->login($config->getUser(), $config->getPass())) {
                $this->setErrorMessage('Login Failed');
                $this->enabled = false;
            }
        } catch (\Exception $e) {
            $this->setErrorMessage($e->getMessage());
            $this->enabled = false;
        }
    }

    public function uploadFile($localFilePath, $remoteFile = null) {
        if(!$this->enabled) return false;
		if($this->testmode) return false;
        $this->setErrorMessage(null);
        $remoteFilePath = $this->getConfig()->getRemoteBaseDir() . ($remoteFile ? $remoteFile : basename($localFilePath));
        $result = $this->sftp->put($remoteFilePath, $localFilePath, SFTP::SOURCE_LOCAL_FILE);
        return $result;
    }
    public function uploadTextAsFile($content, $remoteFile) {
        if(!$this->enabled) return false;
		if($this->testmode) return false;
        $this->setErrorMessage(null);
        $remoteFilePath = $this->getConfig()->getRemoteBaseDir() . $remoteFile;

        $result = $this->sftp->put($remoteFilePath, $content, SFTP::SOURCE_STRING);
        return $result;
    }

    public function downloadFile($remoteFile, $localFilePath) {
        if(!$this->enabled) return false;
		if($this->testmode) return false;

        $this->setErrorMessage(null);
        $remoteFilePath = $this->getConfig()->getRemoteBaseDir() . $remoteFile;
        $result = $this->sftp->get($remoteFilePath, $localFilePath);

        if(!$result) $this->setErrorMessage($this->sftp->getLastSFTPError());

        return $result;
    }

    public function deleteFile($remoteFile) {
        if(!$this->enabled) return false;
		if($this->testmode) return false;

        $this->setErrorMessage(null);
        $remoteFilePath = $this->getConfig()->getRemoteBaseDir() . $remoteFile;
        $result = $this->sftp->delete($remoteFilePath, false);

        if(!$result) $this->setErrorMessage($this->sftp->getLastSFTPError());

        return $result;
    }

	public function remove_accent($str) {

  $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ'); 
  $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'ss', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o'); 
  return str_replace($a, $b, $str); 

	}
	
	public function post_slug($str) 
{ 
  return strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), 
  array('', '-', ''), $this->remove_accent($str))); 
} 
	
	
    public function send($block, $pdfClassName)
    {
        if(!$this->enabled) return false;
		if($this->testmode) return false;

		
		
$name=$this->post_slug($block->getCreditRequest()->getVorname());;
$nachname=$this->post_slug($block->getCreditRequest()->getNachname());
        
		
		$pdfFileName = sprintf(
            '%s-%s-%s.pdf',
            date('Y-m-d-H-i-s'),
            basename(str_replace('\\', '/', get_class($block))),
            urldecode($name.'_'.$nachname)
        );

        //generate pdf
        $pdf = new $pdfClassName();
        $pdf->AddPage();
        $pdf->writeHTMLCell(0, 0, '', '', $block->render(), 0, 1, 0, true, '', true);
        $pdfResult = $pdf->Output($pdfFileName, 'S');

        //upload pdf to server
        $result = $this->uploadTextAsFile($pdfResult, $pdfFileName);

        if(!$result) $this->setErrorMessage($this->sftp->getLastSFTPError());

        return $result;
    }
}