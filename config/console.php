<?php

$params = require __DIR__ . '/params.php';
$db     = require __DIR__ . '/db.php';

$config = [
	'id'                  => 'basic-console',
	'basePath'            => dirname(__DIR__),
	'bootstrap'           => ['log'],
	'controllerNamespace' => 'app\commands',
	'aliases'             => [
		'@bower' => '@vendor/bower-asset',
		'@npm'   => '@vendor/npm-asset',
		'@tests' => '@app/tests',
		'@upload' => '@app/web/uploads',
	],
	'components'          => [
		'cache'    => [
			'class' => 'yii\caching\FileCache',
		],
		'log'      => [
			'targets' => [
				[
					'class'  => 'yii\log\FileTarget',
					'levels' => ['error', 'warning'],
				],
			],
		],
		'db'       => $db,
		'rabbitmq' => require(__DIR__ . '/rabbitmq.php'),
	],
	'container'           => require(__DIR__ . '/services.php'),
	'params'              => $params,
	/*
	'controllerMap' => [
		'fixture' => [ // Fixture generation command line.
			'class' => 'yii\faker\FixtureController',
		],
	],
	*/
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
	$config['bootstrap'][]    = 'gii';
	$config['modules']['gii'] = [
		'class' => 'yii\gii\Module',
	];
}

return $config;
