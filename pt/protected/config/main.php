<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
//Yii::setPathOfAlias('user','modules/user');

// require('components/Utilities.php'); //@TODO make it work
// maybe we need to use 
//Yii::import('...');

//taken from php manual ( function.ini-get.html )
function return_bytes($val) {
	$val = trim($val);
	$last = strtolower($val[strlen($val)-1]);
	switch($last) {
		// The 'G' modifier is available since PHP 5.1.0
		case 'g':
			$val *= 1024;
		case 'm':
			$val *= 1024;
		case 'k':
			$val *= 1024;
	}

	return $val;
}

function getPhpMaxUploadInBytes() {
	return
	min(return_bytes(ini_get('upload_max_filesize')),
			return_bytes(ini_get('post_max_size')));
}



// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'runtimePath'=>'/tmp/',
	'name'=>'PinyinTools',
	'layout'=>'column2',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.components.annotatorModes.*',			
		//the users plugin
// 		'application.modules.user.*',
		'application.modules.user.models.*',
		'application.modules.user.components.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'asdf',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),

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

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
                'class' => 'WebUser',
                'allowAutoLogin'=>true,
                'loginUrl' => array('/user/login'),
		),
		 
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName'=>false,
			'rules'=>array(
				's/<s:.+>'=>'char/lookup',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				's/'=>'char/index',
			),
		),
		
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=cndbyi',
			'emulatePrepare' => true,
			'username' => 'cndbuser',
			'password' => 'cndbuserpwd',
			'charset' => 'utf8',
			'tablePrefix' => '',			
		),
		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		
		'assetManager' => array(
				'linkAssets' => false,
		),

		

		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>CLogger::LEVEL_PROFILE,
// 					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
// 				array(
// 					'class'=>'CWebLogRoute',
// 					'levels'=>'error, warning',
// 				),
			),
		),
/**/
		'authManager'=>array(
				'class'=>'CPhpAuthManager',
		),
		
// 		'cache'=>array(
// 				'class'=>'system.caching.CFileCache',
// 				//'cachePath'=>'/tmp/',
// 				'cachePath'=>'D:/tmp/',
// 		),
		
// 		'authManager'=>array(
// 				'class'=>'CDbAuthManager',
// 				'connectionID'=>'db',
// 		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
// 		this is used in contact page
// 		'adminEmail'=>'webmaster@example.com',

	    //php directives 'post_max_size' and 'upload_max_filesize' are the limit
		'maxDictUploadFileSize'=> getPhpMaxUploadInBytes(),
		'fileUploadEncoding'=> 'utf8',
		'annotatorEncoding'=> 'utf8',
		'staticAnnotatorCompositionLengthLimit'=> 2, //20 is the maximal meaningful value. Lower this value if the annotator is too slow.

		//used in dynamic/footer template (Quick mode)
		//20 is the maximal meaningful value. Lower this value if the annotator is too slow.
		'dynamicAnnotatorCompositionLengthLimit'=> 4, 
		
		'maxCompositions'=> 20, //used in CharController - querySingleComposition
		'maxTemplateParts'=> 10, //used in AnnotatorController
		'dbStepSize'=> 1000, //used in AnnotatorEngine when generating dictionaries
		
		//following three constants are used in AnnotatorEngine when spliting input into chunks
		'annotatorChunkInputSizeMin'=> 50, //the minimal size of a chunk 
		'annotatorChunkInputSizeMax'=> 70, //the maximal size of a chunk
		'annotatorChunkInputSizeAlwaysDirectMax'=> 70, //when the input text length is smaller than this, background task is never created
	),
);