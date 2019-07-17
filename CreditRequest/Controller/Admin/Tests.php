<?php

namespace CreditRequest\Controller\Admin;

use Admin\Controller\Base as BaseAdminController;
use Sigma\Model\Service\Mail;

class Tests extends BaseAdminController {

    function listAction() {
        $this->setDataForAuxmoneyPushApi();
        $this->app->render('list.php');
    }
    
    function setDataForAuxmoneyPushApi() {
        $auxmoneyProgressList = array_keys(\Auxmoney\Model\Auxmoney::AUXMONEY_PROGRESS_MAPPER);
        
        $creditRequestCollection = \CreditRequest\Collection\CreditRequest::getInstance(); 
        $auxmoneyCollection = \Auxmoney\Collection\Auxmoney::getInstance();

        $auxmoneyReplyList = $auxmoneyCollection->getList([
            'join' => [
                "JOIN {$creditRequestCollection->table} AS {$creditRequestCollection->tableAlias} ON ({$auxmoneyCollection->tableAlias}.id={$creditRequestCollection->tableAlias}.auxmoney_id OR {$auxmoneyCollection->tableAlias}.id={$creditRequestCollection->tableAlias}.coapplicant_auxmoney_id)"
            ],
            'filter' => [
                'creditId' => ['!=' => 0]
            ],
            'sort' => [
                'credit_request_id' => 'ASC', 
                'main_applicant' => 'ASC',
                'date' => 'ASC'
            ]
        ])->toArray();   

        $auxmoneyListForSelectGroupedByCreditRequestId = [];
        
//echo "<hr><hr>";       
//foreach ($auxmoneyReplyList as $auxmoneyId => $auxmoneyReply) {
//    var_dump($auxmoneyId); 
//    var_dump($auxmoneyReply['creditRequestId']);
//    var_dump($auxmoneyReply['mainApplicant']);
//    var_dump($auxmoneyReply['creditId']);
//    echo "<br>";
//    $auxmoneyReply['contract'] = '';
//}
//echo "<hr><hr>";

        foreach ($auxmoneyReplyList as $auxmoneyId => $auxmoneyReply) {
            $auxmoneyListForSelectGroupedByCreditRequestId[$auxmoneyReply['creditRequestId']][$auxmoneyReply['mainApplicant']] = $auxmoneyReply['creditId'];
        }
//var_dump($auxmoneyListForSelectGroupedByCreditRequestId);
//echo "<hr><hr>";
        $this->view->appendData(array(
            'auxmoneyProgressList' => $auxmoneyProgressList,
            'auxmoneyListForSelect' => $auxmoneyListForSelectGroupedByCreditRequestId
        ));
    }
    
    function testAuxmoneyPushApiAction() {
        $config = \Core\Model\Registry::getInstance()->getConfig();
        $testMode = $config['api']['auxmoney']['testMode']['enabled'];
        if(!$testMode) {
            exit('Auxmoney test mode is disabled in config');
        }
        
        $auxmoneyCreditId = $_POST['auxmoneyCreditId'];  
        $progressNameList = $_POST['progressNameList'];

        $auxmoneyPushModel = new \Auxmoney\Model\Client\Push();
        
        $responseSettingFakeProgress = $auxmoneyPushModel->setFakeProgress($auxmoneyCreditId, $progressNameList);
        
        echo "Auxmoney response for setting fake progresses:<br>";
        var_dump($responseSettingFakeProgress);
        echo "<hr><br><br>";
        
        $responseTriggeringPushAPI = $auxmoneyPushModel->triggerPushAPI($auxmoneyCreditId);
        
        echo "Auxmoney response for triggering Push API:<br>";
        var_dump($responseTriggeringPushAPI);
        echo "<hr><br><br>";

        exit;
    }
    
    function testCoApplicantDataRequestNotificationAction() {
        $creditRequest = \CreditRequest\Collection\CreditRequest::getInstance()->load(7398);
        $service = \CreditRequest\Model\Service\CoApplicantDataRequest::getInstance();
        $service->setCreditRequest($creditRequest)->process();
        exit('done');
    }
    
    function testCoApplicantDataRequestAction() {          
        \CreditRequest\Model\Service\CoApplicantDataRequest::getInstance()->creditRequestStatusChange();
        exit('done');
    }

    function testGenerateAction() {
        \Sigma\Model\Service::getInstance()->processGenerate();
        exit('done');
    }

    function testSigmaSendGeneratedAction() {
        \Sigma\Model\Service::getInstance()->processSendGenerated();
        exit('done');
    }

    function testReceiveAction() {
        \Sigma\Model\Service::getInstance()->processReceive();
        exit('done');
    }

    function testClarificationTimeoutAction() {
        \Sigma\Model\Service::getInstance()->processClarificationTimeout();
        exit('done');
    }

    function testEmailAction() {
        $email = $this->request->get('email', 'dofer.mail@gmail.com');
        $sender = $this->request->get('sender', 'anfragen4@skag.gmbh');
        $timeout = $this->request->get('timeout', 5);
        $smtpDebug = $this->request->get('smtpDebug', 4);
        $smtpPort = $this->request->get('smtpPort', 587);
        $sendMethod = $this->request->get('sendMethod', 'smtp');

        $config = \Core\Model\Registry::getInstance()->getConfig();

        $config['api']['sigma']['mail']['sendMethod'] = $sendMethod;
        $config['api']['sigma']['mail']['sender'] = $sender;
        $config['api']['sigma']['mail']['smtp']['port'] = $smtpPort;

        \Core\Model\Registry::getInstance()->setConfig($config);


        $mailService = \Sigma\Model\Service\Mail::getInstance();


        $mailService->mailSender->SMTPDebug   = $smtpDebug;
        $mailService->mailSender->Timeout   =  $timeout;
        $mailService->mailSender->Subject   = 'smtp.test.email';
        $mailService->mailSender->Body      = 'test';


        $mailService->mailSender->ClearAllRecipients();
        $mailService->mailSender->AddAddress($email);



        if(!$mailService->mailSender->send()) {
            exit('ERROR:'.$this->mailSender->ErrorInfo);
        }

        exit('done');
    }

    function stepperLoanNotificationAction() {
        \CreditRequest\Model\Service\StepperLoanNotification::getInstance()->process();
        exit('done');
    }

}
