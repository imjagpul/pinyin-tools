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
</p><p>(1）</p></div>
<div class="figure"><p><img src="../images/homepage/02.png"  /> 
</p><p>(2）</p></div>
<div class="figure"><p><img src="../images/homepage/03.png"  /> 
</p><p>(3）</p></div>
&nbsp;
</div>

<ul>
	<li>If you do not know why mnemonics should be of any use for learning Chinese characters, read [About mnemonics].</li>
	<li>If you are using mnemonics already, or plan to - take a look at the [Demo page] to see how this site might help you.</li>
</ul>

<p>TOC (Manual)
</p>


<p>(Beta-test image)
Note this site has just launched, so in case you encounter any errors, have suggestions how this site could be more useful, 
please let me know. (Also thanks for correcting my English, I am not a native speaker.)
</p>

<p>Footer</p>