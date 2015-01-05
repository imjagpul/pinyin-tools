<?php
$index=$simplified ? 'simplified' : 'traditional';

echo "[[";
$first=TRUE;
foreach($dataChar as $d) {
	//separate the entries with commas
	if(!$first) 
		echo ',';
	else 
		$first=false;
	
	//output as a JSON encoded three-members array
	echo json_encode(array($d[$index], $d['transcription'], $d['translation']));
}
echo "],[";

$first=TRUE;
$index_first=$index.'_begin';
$index_rest=$index.'_rest';
foreach($dataPhrase as $d) {
	//separate the entries with commas
	if(!$first)
		echo ',';
	else
		$first=false;

	//output as a JSON encoded three-members array
	echo json_encode(array($d[$index_first], $d[$index_rest], $d['transcription'], $d['translation']));
}

echo "]]";
?>