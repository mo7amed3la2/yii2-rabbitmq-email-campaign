<?php

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

$params = require __DIR__ . '/params.php';
$db     = require __DIR__ . '/db.php';

$config = [
	'id'         => 'basic',
	'basePath'   => dirname(__DIR__),
	'bootstrap'  => ['log'],
	'aliases'    => [
		'@bower' => '@vendor/bower-asset',
		'@npm'   => '@vendor/npm-asset',
	],
	'components' => [
		'fileStorage' => [
			'class' => 'trntv\filekit\Storage',
			'baseUrl' => '@web/uploads',
			'filesystem' => function () {
				$adapter = new Local('uploads');
				return new Filesystem($adapter);
			}
		],
		'request'      => [
			// !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
			'cookieValidationKey' => '-xXOY3VE_XGQ1P_eG82Dy3e51w1nhn0k',
		],
		'cache'        => [
			'class' => 'yii\caching\FileCache',
		],
		'user'         => [
			'identityClass'   => 'app\models\User',
			'enableAutoLogin' => true,
		],
		'errorHandler' => [
			'errorAction' => 'site/error',
		],
		'log'          => [
			'traceLevel' => YII_DEBUG ? 3 : 0,
			'targets'    => [
				[
					'class'  => 'yii\log\FileTarget',
					'levels' => ['error', 'warning'],
				],
			],
		],
		'db'           => $db,
		'rabbitmq'     => require(__DIR__ . '/rabbitmq.php'),
		'urlManager' => [
			'enablePrettyUrl' => true,
			'showScriptName' => false,
			'rules' => [
			],
		],
	],
	'container'  => require(__DIR__ . '/services.php'),
	'params'     => $params,
];

$config['components']['mailer']['transport'] = [
	'class' => 'Swift_SmtpTransport',
	'host' => 'localhost',
	'port' => 1025,
	'username' => null,
	'password' => null,
	'encryption' => false,
];

if (YII_ENV_DEV) {
	// configuration adjustments for 'dev' environment
	$config['bootstrap'][]      = 'debug';
	$config['modules']['debug'] = [
		'class' => 'yii\debug\Module',
		// uncomment the following to add your IP if you are not connecting from localhost.
		//'allowedIPs' => ['127.0.0.1', '::1'],
	];

	$config['bootstrap'][]    = 'gii';
	$config['modules']['gii'] = [
		'class' => 'yii\gii\Module',
		// uncomment the following to add your IP if you are not connecting from localhost.
		//'allowedIPs' => ['127.0.0.1', '::1'],
	];
}

return $config;
