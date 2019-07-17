<?php

namespace App\modules\CreditRequest\Model\Service;

use \App\modules\Core\Model\Base as BaseModel;
use \App\modules\Core\Model\Traits\Singleton;

use \App\modules\Core\Model\Registry;

use \PHPMailer\PHPMailer\PHPMailer;
//use PHPMailer\PHPMailer\Exception;
use App\Http\ArraysClass;

class Sms extends BaseModel
{
    use Singleton;

    public $enabled = false;

    public function __construct(array $items = array())
    {
        parent::__construct($items);
        $this->init();
    }

    public function init()
    {
        $this->initConfig();
        $this->enabled = $this->getConfig()->getEnabled();
        //dump('$SMS->enabled',$this->enabled);
    }

    public function initConfig() {
        $config = Registry::getInstance()->getConfig();
        $config = new ArraysClass();
        $config=$config->conf;
        $this->setConfig(new BaseModel($config['api']['sms']));
		$this->testmode = $config['api']['sms']['testmode'];
    }



    public function send($sms)
    {
        $this->setErrorMessage(null);
        if(!$this->enabled) {
            $this->setErrorMessage('SMS Service disabled');
            return false;
        }
		if($this->testmode) return false;

        $text = $sms->render();
        if($sms->renderError) {
            $this->setErrorMessage($text);
            return false;
        }

        //convert text to iso-8859-1 encoding
        //$text = iconv('UTF-8', "iso-8859-1//TRANSLIT", trim($text));
$charset=mb_detect_encoding(trim($text), 'UTF-8, ISO-8859-1, iso-8859-1', true);//TRANSLIT
//dump(trim($text));
//dump($charset);
	if($charset=='ISO-8859-1'){
	$text = iconv('ISO-8859-1', "ISO-8859-1", trim($text));//TRANSLIT
	}
	elseif($charset=='UTF-8'){
	$text = iconv('UTF-8', "ISO-8859-1", trim($text));//TRANSLIT	
	}
	else{
		$text = iconv('ISO-8859-1', "ISO-8859-1", trim($text));//TRANSLIT//TRANSLIT
	}
$charset=mb_detect_encoding(trim($text), 'UTF-8, ISO-8859-1, ISO-8859-1');//TRANSLIT
//dump($charset);
	//dump($text);
	$text =mb_convert_encoding(trim($text), 'ISO-8859-1', mb_detect_encoding(trim($text), 'UTF-8, ISO-8859-1', true));
        $urlParams = [
            'Username' => $this->getConfig()->getUser(),
            'Password' => $this->getConfig()->getPass(),
            'SMSType' => $this->getConfig()->getType(),
            'SMSTo' => $sms->getPhone(),
            'SMSText' => $text,
        ];

        //if debug recipient phone is set - use it
        if(($debugRecipientPhone = $this->getConfig()->getDebugRecipientPhone())) {
            $urlParams['SMSTo'] = $debugRecipientPhone;
        }

        array_walk($urlParams, function(&$value, $key) { $value = sprintf('%s=%s', $key, urlencode($value));});
        $url = sprintf(
            '%s?%s',
            $this->getConfig()->getUrl(),
            implode('&',$urlParams)
        );

        $success = true;

        $response = file_get_contents($url);
        $result = $this->parseResponse($response);
		//dump($result);
        if($result['return'] == 'ERROR') {
            $success = false;
            $this->setErrorMessage(sprintf(
                'SMS Error: #%s %s',
                isset($result['errorcode']) ? $result['errorcode'] : '',
                isset($result['errortext']) ? $result['errortext'] : ''
            ));

        }

        return $success;
    }

    public function parseResponse($response) {
       // dump('SMSResponse',$response);
        $result = [];
        $lines = explode("\n", $response);
        foreach($lines as $line) {
            //list($value,$key ) = explode(':', $line, 2);
            $list=explode(':', $line, 2);
            if(!empty($list[1])){
            $key=$list[0];
            $value=$list[1];
            $key = trim($key);
            $value = trim($value);}
            if(empty($key) && empty($value)) continue;
            $result[strtolower($key)] = $value;
        }

        return $result;
    }
}