<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'application',
    'language' => 'ru-RU',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'urlManager' => [
            'enablePrettyUrl' => true,
			'showScriptName' => false,
			'rules' => [
				'<controller>/page/<page:\d+>' => '<controller>/index',
				'<controller>/<action>/<num:\d+>' => '<controller>/<action>',
			],
        ],
        'authManager' => [ //тоже моя вставка
            'class' => 'yii\rbac\phpManager',
            'defaultRoles' => ['user', 'admin'],
            'itemFile' => '@app/components/rbac/items.php',
            'assignmentFile' => '@app/components/rbac/assignments.php',
            'ruleFile' => '@app/components/rbac/rules.php',
        ],
        'request' => [
            'cookieValidationKey' => 'xWlAqaOYc9wvrqXwDEGu4Mhw8cBiGn_H',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\dbmodels\User',
            'enableAutoLogin' => true,
			'loginUrl' => ['user/login'], //если адрес страницы входа не site/login, то необходимо указать данный параметр
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.mail.ru',
                'username' => 'dlc.by@mail.ru',
                'password' => 'skurd630a',
                'port' => '465',
                'encryption' => 'ssl',
            ],
        ],
		'assetManager' => [
			'bundles' => [
                'yii\web\JqueryAsset' => [
					'sourcePath' => null,   // не опубликовывать комплект
                    'js' => [
                        '//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js',
                    ]
                ],
            ],
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
        'db' => require(__DIR__ . '/db.php'),
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
