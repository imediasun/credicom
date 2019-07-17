<?php return [
    'cron' => [
        'credit.request.stepperLoanNotification' => [
            'description' => 'Credit Request Stepper Loan Notification',
            'service' => \CreditRequest\Model\Cron::class,
            'function' => 'processStepperLoanNotification',
            'interval' => '1 hour',
        ],
        'credit.request.coApplicantCreditRequestStatusChange' => [
            'description' => 'Credit Request status change to offen after 7 day if no response',
            'service' => \CreditRequest\Model\Cron::class,
            'function' => 'processCoApplicantCreditRequestStatusChange',
            'interval' => '1 hour',
        ]
    ],
    'routes' => [
        [
            'url' => '/kreditanfrage-erfolgreich.html',
            'callbackClass' => \CreditRequest\Controller\CreditRequest::class,
            'callbackFunction' => 'replyAction'
        ]
    ]
];