<?php
/* @var $this TextController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Texts',
);

$this->menu=array(
	array('label'=>'Create Text', 'url'=>array('create')),
	array('label'=>'Manage Text', 'url'=>array('admin')),
);
?>

<h1>Texts</h1>

<p>
<?php
// @TODO add links to blog article about synoptic reading  
?>
A few tips for your synoptic reading.
</p>
<p>
Obviously there is a huge amount of ebooks available online, even for free
(just google <a href="http://www.google.com/search?q=%E7%94%B5%E5%AD%90%E4%B9%A6%E4%B8%8B%E8%BD%BD">电子书下载</a> for chinese books). 
However, navigating through websties in a foreign language can be crumbersome,
and it is not so easy to find a matching English translation, not to speak of the audio recording.
<?php
// @TODO If you know about books that could be useful for other language learners, preferably but not necessarily with the audio,
// please let me know.   
?>

</p>


<?php
 

$crit=new CDbCriteria();
$crit->distinct=true;
$crit->select=array('language');
// $crit->with="languageData";
$r=TextCategory::model()->findAll($crit); //@TODO presunout praci z daty do Contolleru

foreach($r as $entry) {
	echo '<h2>'.$entry->languageData->text.'</h2>';

	$titles=TextCategory::model()->findAllByAttributes(array('language'=>$entry->language));
// 	var_dump($titles);die;
	foreach($titles as $title) {
		echo '<h3>'.$title->categoryTitle.'</h3>';
		echo '<div id="components-grid" class="grid-view">';
		
		echo '<table class="items">';
		foreach($title->texts as $textDetail) {
// 			var_dump($textDetail);//HERE : mozna list view bez nadpusz
			echo '<tr class="odd">';
			echo '<th>';
			echo $textDetail->name;
			echo '</th>';
			
			echo '<td>';
			echo $textDetail->description;
			echo '</td>';
			echo '<td>';
			echo $textDetail->original;
			echo '</td>';
			echo '<td>';
			echo $textDetail->translations;
			echo '</td>';
			echo '<td>';
			echo $textDetail->audio;
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';
		echo '</div>';
	}
	
}

/*
$this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); 
*/
?>
