<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'blog',
    'basePath' => dirname(__DIR__),
    'name' => 'Бе3печный блоггер',
    'homeUrl' => '/',
    'defaultRoute' => 'blog/blog/index',
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'controllerMap' =>
    [
        //
    ],
    'modules' => [
        'blog' => [
            'class' =>'app\modules\blog\Module',
        ],
        'sef' => [
            'class' => 'app\modules\sef\Module',
        ],
        'grabber' => [
            'class' => 'app\modules\grabber\Module',
        ],
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '5dvq1Rie-92iMZFJmjOlInGcl7hSK1sy',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'defaultDuration' => 86400,
        ],
        'user' => [
            'identityClass' => 'app\models\Users',
            'enableAutoLogin' => true,
            'loginUrl' => '/',
        ],
        'errorHandler' => [
            'errorAction' => 'default/error',
            'errorView' => '//default/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'queue' =>
        [
            'class' => 'yii\queue\file\Queue',
            'path' => '@runtime/queue',
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                [ 'class' => 'app\modules\sef\components\UrlRule' ],
            ],
        ],
    ],
    'params' => $params,
];

$config['bootstrap'][] = 'queue';
$config['bootstrap'][] = 'blog';
$config['bootstrap'][] = 'sef';
$config['bootstrap'][] = 'grabber';

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
