<?php 
/* @var $data System */ 

$targetLangText=!is_null($data->targetLanguageData) ? $data->targetLanguageData->text : "(none)"; 
$langText=!is_null($data->languageData) ? $data->languageData->text : "(none)"; 
?>

<p><b>Maintained by:</b><br/> <?php echo $data->masterUser->username; ?></p>

<p>
<b>Target language:</b><br/> <?php echo $targetLangText; ?><br/>
<b>Mnemonics language:</b><br/> <?php echo $langText; ?><br/>
</p>
<?php /*

<ul>
<li>Mark as preferred</li>
<li>Add to ignore list</li>
</ul>
*/
?>