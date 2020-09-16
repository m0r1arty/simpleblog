<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
    'modules' => [
        'blog' => [
            'class' => 'app\modules\blog\Module',
        ],
        'sef' => [
            'class' => 'app\modules\sef\Module',
        ],
        'grabber' => [
            'class' => 'app\modules\grabber\Module',
        ],
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
        'urlManager' => [
            'scriptUrl' => '',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'baseUrl' => 'http://blog/',
            'rules' => [
                [ 'class' => 'app\modules\sef\components\UrlRule' ],
            ],
        ],
        'queue' => [
        	'class' => 'yii\queue\file\Queue',
            'as log' => \yii\queue\LogBehavior::class,
        	'path' => '@runtime/queue',
        ],
        'db' => $db,
    ],
    'params' => $params,
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

$config['bootstrap'][] = 'blog';
$config['bootstrap'][] = 'sef';
$config['bootstrap'][] = 'grabber';
$config[ 'bootstrap' ][] = 'queue';

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'generators' =>
        [
        	'job' => [
        		'class' => \yii\queue\gii\Generator::className(),
        	]
        ]
    ];
}

return $config;
