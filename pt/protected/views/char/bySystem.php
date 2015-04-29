<h1>Entries in system "<?php echo $systemName; ?>"</h1>
<?php $this->widget('zii.widgets.CListView', array(
		'dataProvider'=>$dataProvider,
		'itemView'=>'_view',
)); ?>
