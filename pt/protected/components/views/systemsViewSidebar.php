<?php 
/* @var $data System */ 
?>

<p><b>Maintained by:</b><br/> <?php echo $data->masterUser->login; ?></p>

<p>
<b>Target language:</b><br/> <?php echo $data->languageData->text; ?><br/>
<b>Mnemonics language:</b><br/> <?php echo $data->targetLanguageData->text; ?><br/>
</p>
<?php /*

<ul>
<li>Mark as preferred</li>
<li>Add to ignore list</li>
</ul>
*/
?>