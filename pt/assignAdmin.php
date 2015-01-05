<?php
// die;
// change the following paths if necessary
$yii=dirname(__FILE__).'/../framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
Yii::createWebApplication($config)->run();

// $auth=Yii::app()->authManager;

// $role=$auth->createRole('admin');
// $auth->assign('admin', '2');
// $auth->save();

// overit, jestli $auth fakt ho uzna jako admina
// $r=$auth->checkAccess("admin", 2);
// var_dump($r);

// -------- dict stats

// $param='.';

// for($of=0; $of<100; $of+=1) {
// 	$param.='.';
// 	$sql="SELECT COUNT(*)  FROM `dict_entry_phrase` WHERE `simplified_middle` REGEXP '^$param$'";
// 	$result=Yii::app()->getDb()->getCommandBuilder()->createSqlCommand($sql);
// 	$result=$result->queryScalar();
// 	echo "len $of = $result";	
// }

/*
add 1 to get (length /3)-2 (approx) 

len 0 = 384len 1 = 39len 2 = 128len 3 = 70len 4 = 80556len 5 = 327len 6 = 532len 7 = 76306len 8 = 80len 9 = 81len 10 = 24427len 11 = 59len 12 = 80len 13 = 16410len 14 = 55len 15 = 81len 16 = 12905len 17 = 23len 18 = 30len 19 = 3813len 20 = 31len 21 = 34len 22 = 2234len 23 = 13len 24 = 10len 25 = 1189len 26 = 22len 27 = 10len 28 = 677len 29 = 10len 30 = 6len 31 = 382len 32 = 0len 33 = 4len 34 = 261len 35 = 4len 36 = 1len 37 = 158len 38 = 15len 39 = 3len 40 = 106len 41 = 2len 42 = 1len 43 = 53len 44 = 0len 45 = 0len 46 = 21len 47 = 5len 48 = 9len 49 = 7len 50 = 1len 51 = 4len 52 = 9len 53 = 1len 54 = 0len 55 = 7len 56 = 0len 57 = 0
len 58 = 8
 */
// -------- import UNICHIN
 ini_set('max_execution_time', 1500);
 
//  $specialCompositionChars=array('⿰', '⿱', '⿲', '⿳', '⿴', '⿵', '⿶', '⿷', '⿸', '⿹', '⿺', '⿻'); 
//  function removeSpecialChars($str) {
//  	global $specialCompositionChars;
//  	//note we cannot use trim as trim works only with 8-bit characters
//  	foreach ($specialCompositionChars as $c)
//  		$str=str_replace($c, '', $str);
//  	return $str;
//  }
 
//  /**
//   * 
//   * @param Integer $targetSystemId
//   * @param String $char
//   * @return Integer the id of the given character 
//   */
//  function ensureExistence($targetSystemId, $char) {
//  	$result=Char::model()->findAllByAttributes(array('system'=>$targetSystemId, 'chardef'=>$char));
//  	$c=count($result);
//  	if($c==0) {
//  	//add to db
// 	 	$newChar=new Char;
// 	 	$newChar->system=$targetSystemId;
// 	 	$newChar->chardef=$char;
// 	 	$newChar->insert();
// 	 	return $newChar->id;
//  	} else {
// 	 	if($c>1) {
// 	 		echo "Warning: $char exists more than one time";
// 	 	}
// 	 	return $result[0]->id;
//  	}

//  }
 
 
// // $f=fopen("/tmp/unichin_converted.txt", "rb");
// $f=fopen("/home/imjagpul/Data/javaprojekty/PinyinToolsDb/data/unichin/unichin_converted.txt", "rb");

// $encoding='utf-8';
// $targetSystemId=3;
// // $targetSystem=System::model()->findByPk($pk)
// $lastLine=NULL;
// while(($line=fgets($f)) !== FALSE) {
	
// 	if($line==$lastLine) //skip duplicite lines
// 		continue;
// 	$lastLine=$line;
	
// 	$line=removeSpecialChars(trim($line));
	
// 	$len=mb_strlen($line, $encoding);
// 	$first=mb_substr($line, 0,1, $encoding);
// // 	$otherStr=mb_substr($line, 1, $len-1, $encoding);
// 	//split to an array of chars
// 	$other=array();
// 	for($i=1;$i<$len;$i++) {
// 		$other[]=mb_substr($line, $i, 1, $encoding);
// 	}
	
// 	//if the only composition is equal to the string itself - skip
// 	if(count($other)==1 && $other[0]==$first)
// 		continue;
	
// 	//ensure the char and all subcomponents are in the db already, and get their IDs
// 	$charId=ensureExistence($targetSystemId, $first);
// 	$subChar=array(); //$chardef => ($id, $count)
// 	foreach($other as $subcomponent) {
// 		if(!isset($subChar[$subcomponent]))
// 			$subChar[$subcomponent]=array(ensureExistence($targetSystemId, $subcomponent), 1);
// 		else
// 			$subChar[$subcomponent][1]++; //increase the count if a single subcomponent is used more times
// 	}
	
// 	//create the corresponding Composition entries
// 	foreach($subChar as $chardef => $data) {
// 		$subCharId=$data[0];
// 		$subCharCount=$data[1];
		
// 		$c=new Composition;
// 		$c->charId=$charId;
// 		$c->subcharId=$subCharId;
// 		$c->count=$subCharCount;
// 		$c->insert(); //note this script assumes no conflicting Composition entries are present in the DB
// 	}	
// }

// echo 'Composition import finished.';
 
