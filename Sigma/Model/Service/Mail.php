<?php

namespace App\modules\Sigma\Model\Service;

//traits
use \App\modules\Core\Model\Traits\Singleton;

//models
use \App\modules\Core\Model\Base as BaseModel;

//blocks
use \App\modules\Sigma\Block\Mail\ExaminationRequest as BlockMailExaminationRequest;

//services and stuff
use \App\modules\Core\Model\Registry;

//vendor
use \PHPMailer\PHPMailer\PHPMailer;
use App\Http\ArraysClass;
class Mail extends BaseModel
{
    use Singleton;

    public $mailSender;
    public $mailReceiver;

    public function __construct(array $items = array())
    {
        parent::__construct($items);
        $this->init();
    }

    public function init()
    {
        //dump('SigmaMailService',$this);
        $this->initConfig();
        $this->initMailSender();
        $this->initMailReceiver();
    }

    public function initConfig()
    {
        $config = Registry::getInstance()->getConfig();
        $config = new ArraysClass();
        $config=$config->conf;
        $this->setConfig($config['api']['sigma']['mail']);
    }


    public function initMailSender()
    {
        $config = $this->getConfig();

        $this->mailSender = new PHPMailer();

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
        $this->mailSender->From      = $config['sender'];
        $this->mailSender->FromName      = $config['senderName'];
        $this->mailSender->addReplyTo($config['sender'], $config['senderName']);
    }

    public function initMailReceiver()
    {
        $config = $this->getConfig();

        $attachmentDir = base_path().'/tmp/';

        $this->mailReceiver = new \PhpImap\Mailbox(
            sprintf(
                '{%s:%s/imap%s%s}INBOX',
                $config['imap']['host'],
                $config['imap']['port'],
                $config['imap']['ssl'] ? '/ssl' : '',
                $config['imap']['tls'] ? '/validate-cert' : ''
            ),
            $config['imap']['user'],
            $config['imap']['pass'],
            $attachmentDir
        );
    }

    public function processSend()
    {
        $config = $this->getConfig();
        dump('before_getFileExport');
        $fileName = $this->getFileExport();
        dump('after_getFileExport');
        $mailBlock = new BlockMailExaminationRequest();
        //dump('$mailBlock ',$mailBlock );
//        $this->mailSender->SMTPDebug   = 2;
        $this->mailSender->Timeout   =  5;
        $this->mailSender->Subject   = $mailBlock->mailSubject;
        dump('before_render');
        $this->mailSender->Body      = $mailBlock->render();
        dump('after_render');
        $this->mailSender->isHTML(true);

        $this->mailSender->ClearAllRecipients();
        $this->mailSender->AddAddress($config['recipient']);

        $this->mailSender->addAttachment($fileName, 'export.csv');

        if(!$this->mailSender->send()) {
            dump('!$this->mailSender->send()');
            //TODO: log : Error $this->mailSender->ErrorInfo;
        } else {
            dump('mail sended');
            //TODO: log: OK
        }
    }

    public function processReceive()
    {
        $config = $this->getConfig();
        $filePathTemplate = $this->getFileImportTemplate();

        //$mailboxes = $this->mailReceiver->getMailboxes();
        $mailIds = $this->mailReceiver->searchMailbox(sprintf('FROM "%s"', $config['recipient']));

        foreach($mailIds as $mailId)
        {
            $deleteMail = false;
            $mail = $this->mailReceiver->getMail($mailId);
            $mailDate = date('Y.m.d-H.i.s',strtotime($mail->headers->date));

            $attachments = $mail->getAttachments();
            foreach($attachments as $attachment) {
                if(strpos($attachment->name, '.csv') === false) continue;
                $deleteMail = true;
                $filePath = sprintf($filePathTemplate, $mailDate);
                rename($attachment->filePath, $filePath);
                //TODO: log: $mail->headers->subject, $attachment->name
            }

            if($deleteMail) {
                $this->mailReceiver->deleteMail($mailId);
                //TODO: log: deleteMail $mailId
            }
        }
    }
}