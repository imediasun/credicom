<?php

namespace App\modules\Sigma\Model\Service;

//traits
use \App\modules\Core\Model\Traits\Singleton;

//models
use \App\modules\Core\Model\Base as BaseModel;
use \App\modules\Sigma\Model\Reply as ModelReply;
use \App\modules\CreditRequest\Model\CreditRequest as ModelCreditRequest;

//services and stuff
use \App\modules\Sigma\Collection\Reply as CollectionReply;
use \App\modules\CreditRequest\Collection\CreditRequest as CollectionCreditRequest;

//services and stuff
use \App\modules\CreditRequest\Model\Service as CreditRequestService;
use \App\modules\Sigma\Model\Service as SigmaService;
use Log;
class ClarificationTimeout extends BaseModel
{
    use Singleton;

    public function process()
    {
        $date = new \DateTime();
        $date->modify('-1 week');

        $collectionCreditRequest = CollectionCreditRequest::getInstance();
        $collectionReply = CollectionReply::getInstance();

        //get list of all green and yellow sigma responses, with attached credit request in 'clarification' status
        dump('$collectionReply',$collectionReply);
        $list = $collectionReply->getList([
            'join' => [
                sprintf(
                    'LEFT JOIN %s as %s ON `sr`.`credit_request_id` = `%s`.`id`',
                    $collectionCreditRequest->table,
                    $collectionCreditRequest->tableAlias,
                    $collectionCreditRequest->tableAlias
                )
            ],
            'filter' => [
                'sr.date' => ['<=' => $date->format('Y-m-d H:i:s')],
                'sr.type' => [ModelReply::TYPE_GREEN, ModelReply::TYPE_YELLOW],
                sprintf('%s.status_intern', $collectionCreditRequest->tableAlias) => ModelCreditRequest::STATUS_WDV_KLARUNG
            ]
        ]);
        dump('$list',$list);
        if(!count($list)) return;

        $collectionCreditRequest = CollectionCreditRequest::getInstance();
        
        $duplicatedRequestList = $this->getDuplicatedRequestList($list);

        foreach($list as $reply) {
            $creditRequestId = $reply->getCreditRequestId();
            if(!$creditRequestId){
                dump('!$creditRequestId');
                continue;
            }
            
            //skip duplicates
            if($duplicatedRequestList[$creditRequestId]) {
                dump('$duplicatedRequestList[$creditRequestId',$duplicatedRequestList[$creditRequestId]);
                continue;
            }

            $creditRequest = $collectionCreditRequest->load($creditRequestId);
            if(!$creditRequest) continue;            
            //update status
            $creditAmount = $creditRequest->getKreditbetrag();
            $status = ModelCreditRequest::STATUS_NV_SV;
			
            if($creditAmount < 3000) {
				dump($creditAmount);
                $status = ModelCreditRequest::STATUS_NV_NEGATIVE_SCHUFA;
				
            }
            if($status == ModelCreditRequest::STATUS_NV_SV){
                $creditRequest = \App\modules\CreditRequest\Collection\CreditRequest::getInstance()->load($creditRequestId);
                $resultPlanfinanz24 = \App\modules\CreditRequest\Model\Service\ExportDataForPartner\Planfinanz24::getInstance()->inits($creditRequest);
            }
            $creditRequestService = CreditRequestService::getInstance();
            $creditRequestService->changeInternStatus($status, $creditRequest);///

            //add credit request note about timeout
            $creditRequestService->addCreditRequestNote('Kunde hat nicht reagiert - keine Gehaltsbescheinigung geschickt', $creditRequest);
        }
    }
    
    public function getDuplicatedRequestList($list) {
        $creditRequestIdList = [];
        foreach($list as $reply) {
            $creditRequestId = $reply->getCreditRequestId();
            $creditRequestIdList[$creditRequestId] = $creditRequestId;
        }        

        $result = SigmaService::getInstance()->checkDuplicateRequestList($creditRequestIdList);
        
        return $result;
    }
}