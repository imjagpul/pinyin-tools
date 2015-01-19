<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Console Application',

	// preloading 'log' component
	'preload'=>array('log'),

	// application components
	'components'=>array(
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=cndbyi',
			'emulatePrepare' => true,
			'username' => 'cndbuser',
			'password' => 'cndbuserpwd',
			'charset' => 'utf8',
			'tablePrefix' => '',
		),
			
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
			),
		),
	),
	'modules' => array (
		'user'=>array(
			# encrypting method (php hash function)
			'hash' => 'sha512',
	
			# send activation email
			'sendActivationMail' => false,
	
			# allow access for non-activated users
			'loginNotActiv' => true,
	
			# activate user on registration (only sendActivationMail = false)
			'activeAfterRegister' => true,
	
			# automatically login from registration
			'autoLogin' => true,
	
			# registration path
			'registrationUrl' => array('/user/registration'),
	
			# recovery password path
			'recoveryUrl' => array('/user/recovery'),
	
			# login form path
			'loginUrl' => array('/user/login'),
	
			# page after login
			'returnUrl' => array('/user/profile'),
	
			# page after logout
			'returnLogoutUrl' => array('/user/login'),
		),
	),
);