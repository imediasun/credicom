<?php return [
    'cron' => [
        'sigma.import' => [
            'description' => 'Sigma Import',
            'service' => \Sigma\Model\Cron::class,
            'function' => 'processReceive',
            'interval' => '1 hour'
        ],
        'sigma.export' => [
            'description' => 'Sigma Export',
            'service' => \Sigma\Model\Cron::class,
            'function' => 'processGenerate',
            'interval' => '1 day'
        ],
        'sigma.clarification.timeout' => [
            'description' => 'Sigma Clarification Timeout',
            'service' => \Sigma\Model\Cron::class,
            'function' => 'processClarificationTimeout',
            'interval' => '1 hour'
        ],
    ]
];