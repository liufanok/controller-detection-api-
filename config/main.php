<?php
$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

/**
 * Application configuration shared by all test types
 */
return [
    'id' => 'controller-detection',
    'basePath' => dirname(__DIR__),
    'components' => [
        'db' => $db,
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.163.com',  //每种邮箱的host配置不一样
                'username' => 'buct_cpa_admin@163.com',
                'password' => 'buct123456',
                'port' => '25',
                'encryption' => 'tls',
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                'POST /api/v1/<controller:[\w-]+>/<action:[\w-]+>' => '<controller>/<action>',
            ],
        ],
        'user' => [
            'identityClass' => 'app\models\User',
        ],
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
            // but if you absolutely need it set cookie domain to localhost
            /*
            'csrfCookie' => [
                'domain' => 'localhost',
            ],
            */
        ],
    ],
    'params' => $params,
];
