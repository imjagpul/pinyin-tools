<?php
/* @var $this SiteController */
?>

<div id="silo" class="silo">
		<?php $obj=$this->widget('zii.widgets.CMenu',array(
				'activeCssClass'=>'current',
				
				'items'=>array(
				array('label'=>'What\'s this all about?', 'url'=>array('/site/page')), //"whatsthis" is default view
				array('label'=>'Browse mnemonics', 'url'=>array('/char/browse')),
				array('label'=>'Add your own mnemonics', 'url'=>array('/char/create')),
				array('label'=>'Annotator', 'url'=>array('/annotator'))
			),
		)); 
		?>
</div>

<h3 class="quote">Serious Chinese learners might find it difficult to memorize Chinese characters.
Here is the solution.
</h3>

<div class="figblock">
&nbsp;
<div class="figure"><p><img src="../images/homepage/01.png"  /> 
</p><p>One mnemonic per char</p></div>
<div class="figure"><p><img src="../images/homepage/02.png"  /> 
</p><p>Visualize and remember</p></div>
<div class="figure"><p><img src="../images/homepage/03.png"  /> 
</p><p>Recall when reading</p></div>
&nbsp;
</div>

<div>
<p>
	Welcome! First, read 
	 
	<?php echo CHtml::link("Why are mnemonics useful?", array('/site/page', 'view'=>'chineseCharacters')); 
	/*
	 If you do not know why mnemonics should be of any use for learning Chinese characters, take a look at
	 */
	?>
	
	</p><p>
	
	Then, take a look at the 
	<?php echo CHtml::link("Demo page", array('/site/page', 'view'=>'demonstration')); ?>
	to see how this site might help you.
</p></div>
