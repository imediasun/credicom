<?php

namespace App\modules\CreditRequest\Model\Service;

use \App\Http\ArraysClass;
use \App\modules\Core\Model\Base as BaseModel;
use \App\modules\Core\Model\Traits\Singleton;

use \App\modules\Core\Model\Registry;
use \App\modules\Core\Model\Utils;
//use \App\Mail\OrderShipped;
//use Illuminate\Support\Facades\Mail;
use \PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class Mail extends BaseModel
{
    use Singleton;

    public $mailSender;

    public function __construct(array $items = array())
    {
       parent::__construct($items);
       $this->init();
    }

    public function init()
    {
        $this->initConfig();
        $this->initSender();
        $this->initMailSender();
    }

    public function getSender(){
        $globalConfig=new ArraysClass();
        $globalConfig=$globalConfig->conf;
        return $globalConfig['notification']['default']['sender'];
      // return $this;


    }

    public function initConfig()
    {
    $config = Registry::getInstance()->getConfig();
        $globalConfig=new ArraysClass();
        $globalConfig=$globalConfig->conf;
        $this->setConfig($globalConfig['mail']['creditRequestForm']);
    }

    public function initSender($senderData = null)
    {
        $globalConfig=new ArraysClass();
        $globalConfig=$globalConfig->conf;
        //$globalConfig = Registry::getInstance()->getConfig();
        $senderData = ($senderData) ? $senderData : $globalConfig['notification']['default']['sender'];
       $this->setSender(Utils::arrayToModel($senderData));
        $this->mailSender=$senderData;
       // dump('$this->mailSender',$senderData);
    }

    public function initMailSender()
    {

        $config = $this->getConfig();
        $globalConfig=new ArraysClass();
        $globalConfig=$globalConfig->conf;
        $config=$globalConfig['mail']['creditRequestForm'];
        $this->mailSender = new PHPMailer();
//dump('CONFIG');
//dump($config);
        if($config['sendMethod'] == 'smtp') {
            $this->mailSender->isSMTP();
            $this->mailSender->Host = $config['smtp']['host'];
            $this->mailSender->SMTPAuth = true;
            $this->mailSender->Username = $config['smtp']['user'];
            $this->mailSender->Password = $config['smtp']['pass'];
            $this->mailSender->SMTPSecure = $config['smtp']['secure'];
            $this->mailSender->Port = $config['smtp']['port'];
        }

        $this->mailSender->CharSet = 'utf-8';
        $this->mailSender->SetLanguage ("de");
        
        $this->mailSender->isHTML(true);          
    }    
    
    public function setSenderFrom($sender)
    {
        $this->mailSender->From = $sender['email'];//'info@credicom.de'   ->getEmail();
        //dump('$this->mailSender->From',$this->mailSender->From);
        $this->mailSender->FromName = $sender['emailName'];//->getEmailName()
        
        $this->mailSender->clearReplyTos();
        $this->mailSender->addReplyTo($sender['email'], $sender['emailName']);//$sender->getEmail(), $sender->getEmailName()
    } 
    
    public function removeAttachment()
    {
        $this->mailSender->clearAttachments();
    }
    
    public function addAttachment($filePath)
    {
        $this->mailSender->addAttachment($filePath);  
    } 

    public function send($block)
    {
       // Mail::to('imediasun@gmail.com')->send(new \App\Mail\OrderSend($block));

    $this->setErrorMessage(null);

        $this->mailSender->Subject = $block->mailSubject;
        $this->mailSender->Body = $block->render();

        $this->mailSender->ClearAllRecipients();
        $this->mailSender->AddAddress($block->getRecipient()->getEmail());//)
        $rewriteSender =null;
        $rewriteSender = $block->getRewriteSender();
        $sender = ($rewriteSender) ? Utils::arrayToModel($rewriteSender) : $this->getSender();
        $this->setSenderFrom($sender);
        $attachment = $block->getAttachment();
        if($attachment) {
            $this->addAttachment($attachment);
        } else {
            $this->removeAttachment();
        }
        $result = $this->mailSender->send();
        
        if(!$result) {
            $this->setErrorMessage(sprintf('Mail Send Error:%s',$this->mailSender->ErrorInfo));
            dump($this->mailSender->ErrorInfo);
        }

        return $result;
    }
}